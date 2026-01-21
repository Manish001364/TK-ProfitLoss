# P&L Module Migration Guide

## Upgrading from Previous Versions

If you have already installed a previous version of the P&L module, follow these steps to upgrade to the latest version.

---

## Version 2.3 Changes (January 2025) - LATEST

### New Features
- **Built-in Sidebar Navigation** - All P&L pages now have integrated sidebar
- **White text on colored headers** - Better visibility
- **Protected default categories** - System categories can't be deleted
- **Fixed audit log column** - Added missing `reason` column

### Database Migration for v2.3

Run this SQL to add the missing `reason` column to `pnl_audit_logs`:

```sql
-- ==============================================
-- MIGRATION SCRIPT v2.3
-- ==============================================

-- Add missing 'reason' column to audit_logs table
ALTER TABLE `pnl_audit_logs` 
    ADD COLUMN `reason` VARCHAR(500) NULL COMMENT 'Reason for the change' 
    AFTER `new_values`;

-- ==============================================
-- END OF v2.3 MIGRATION
-- ==============================================
```

---

## Version 2.0 Changes (January 2025)

### New Features
- **Per-Organiser VAT/Tax Settings** - Each organiser can set their own default tax rate
- **New Invoice Number Format** - INV-YYYYMM-XXX (e.g., INV-202501-001)
- **Tax Rate Per Expense** - Store and display the tax percentage used
- **Email Notification Controls** - Per-expense option to enable/disable vendor emails
- **Settings Page** - Configure defaults for tax, invoices, and notifications
- **Revenue Edit Page** - Easy "Add More Tickets Sold" functionality

---

## Step 1: Database Schema Updates

Run these SQL statements in your MySQL database to add the new columns and table:

```sql
-- ==============================================
-- MIGRATION SCRIPT v2.0
-- Run this AFTER backing up your database
-- ==============================================

-- 1. Create new pnl_settings table
CREATE TABLE IF NOT EXISTS `pnl_settings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `default_tax_rate` DECIMAL(5, 2) DEFAULT 20.00 COMMENT 'Default VAT/Tax rate %',
    `invoice_prefix` VARCHAR(10) DEFAULT 'INV' COMMENT 'Invoice number prefix',
    `invoice_next_number` INT UNSIGNED DEFAULT 1 COMMENT 'Next invoice sequence number',
    `send_email_on_payment_created` TINYINT(1) DEFAULT 1,
    `send_email_on_payment_paid` TINYINT(1) DEFAULT 1,
    `send_email_on_payment_scheduled` TINYINT(1) DEFAULT 1,
    `company_name` VARCHAR(255) NULL COMMENT 'Company name for invoices',
    `company_address` TEXT NULL COMMENT 'Company address for invoices',
    `company_vat_number` VARCHAR(50) NULL COMMENT 'VAT registration number',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `idx_pnl_settings_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Add user_id column to pnl_events
ALTER TABLE `pnl_events` 
    ADD COLUMN `user_id` BIGINT UNSIGNED NULL AFTER `id`,
    ADD INDEX `idx_pnl_events_user_id` (`user_id`);

-- 3. Add user_id column to pnl_vendors
ALTER TABLE `pnl_vendors`
    ADD COLUMN `user_id` BIGINT UNSIGNED NULL AFTER `id`,
    ADD INDEX `idx_pnl_vendors_user_id` (`user_id`);

-- 4. Add user_id column to pnl_expense_categories
ALTER TABLE `pnl_expense_categories`
    ADD COLUMN `user_id` BIGINT UNSIGNED NULL AFTER `id`,
    ADD INDEX `idx_pnl_expense_categories_user_id` (`user_id`);

-- 5. Add new columns to pnl_expenses
ALTER TABLE `pnl_expenses` 
    ADD COLUMN `user_id` BIGINT UNSIGNED NULL AFTER `id`,
    ADD COLUMN `tax_rate` DECIMAL(5, 2) DEFAULT 0.00 COMMENT 'Tax/VAT rate %' AFTER `amount`,
    ADD COLUMN `total_amount` DECIMAL(15, 2) DEFAULT 0.00 COMMENT 'Amount + Tax' AFTER `tax_amount`,
    ADD COLUMN `is_taxable` TINYINT(1) DEFAULT 1 AFTER `total_amount`,
    ADD INDEX `idx_pnl_expenses_user_id` (`user_id`),
    ADD INDEX `idx_pnl_expenses_invoice_number` (`invoice_number`);

-- 6. Add new columns to pnl_payments
ALTER TABLE `pnl_payments`
    ADD COLUMN `user_id` BIGINT UNSIGNED NULL AFTER `id`,
    ADD COLUMN `reminder_count` INT DEFAULT 0 AFTER `last_reminder_sent_at`,
    ADD COLUMN `send_email_to_vendor` TINYINT(1) DEFAULT 1 COMMENT 'Enable/disable email notifications to vendor' AFTER `reminder_count`,
    ADD COLUMN `deleted_at` TIMESTAMP NULL AFTER `updated_at`,
    ADD INDEX `idx_pnl_payments_user_id` (`user_id`);

-- 7. Add user_id column to pnl_revenues
ALTER TABLE `pnl_revenues`
    ADD COLUMN `user_id` BIGINT UNSIGNED NULL AFTER `id`,
    ADD INDEX `idx_pnl_revenues_user_id` (`user_id`);

-- 8. Update existing records to set user_id (IMPORTANT!)
-- Replace YOUR_USER_ID with your actual organiser user ID
-- You can find this by running: SELECT id FROM users WHERE email = 'your@email.com';

SET @YOUR_USER_ID = 1;  -- CHANGE THIS to your actual user ID

UPDATE `pnl_events` SET `user_id` = @YOUR_USER_ID WHERE `user_id` IS NULL;
UPDATE `pnl_vendors` SET `user_id` = @YOUR_USER_ID WHERE `user_id` IS NULL;
UPDATE `pnl_expense_categories` SET `user_id` = @YOUR_USER_ID WHERE `user_id` IS NULL;
UPDATE `pnl_expenses` SET `user_id` = @YOUR_USER_ID WHERE `user_id` IS NULL;
UPDATE `pnl_payments` SET `user_id` = @YOUR_USER_ID WHERE `user_id` IS NULL;
UPDATE `pnl_revenues` SET `user_id` = @YOUR_USER_ID WHERE `user_id` IS NULL;

-- 9. Update existing expense records to calculate total_amount
UPDATE `pnl_expenses` SET `total_amount` = `amount` + COALESCE(`tax_amount`, 0) WHERE `total_amount` = 0 OR `total_amount` IS NULL;

-- 10. Set tax_rate to 20% for existing taxable expenses
UPDATE `pnl_expenses` SET `tax_rate` = 20.00 WHERE `tax_amount` > 0 AND (`tax_rate` = 0 OR `tax_rate` IS NULL);

-- ==============================================
-- END OF MIGRATION SCRIPT
-- ==============================================
```

**⚠️ IMPORTANT:** Before running step 8, change `@YOUR_USER_ID` to your actual user ID. You can find it by running:
```sql
SELECT id, email FROM users WHERE email = 'your@email.com';
```

---

## Step 2: Copy Updated Files

Replace/add these files in your project:

### New Files to Add:
| Source | Destination |
|--------|-------------|
| `app/Models/PnL/PnlSettings.php` | `your-project/app/Models/PnL/PnlSettings.php` |
| `app/Http/Controllers/PnL/SettingsController.php` | `your-project/app/Http/Controllers/PnL/SettingsController.php` |
| `resources/views/pnl/settings/index.blade.php` | `your-project/resources/views/pnl/settings/index.blade.php` |
| `resources/views/pnl/revenues/edit.blade.php` | `your-project/resources/views/pnl/revenues/edit.blade.php` |

### Updated Files to Replace:
| Source | Destination |
|--------|-------------|
| `app/Models/PnL/PnlExpense.php` | `your-project/app/Models/PnL/PnlExpense.php` |
| `app/Models/PnL/PnlPayment.php` | `your-project/app/Models/PnL/PnlPayment.php` |
| `app/Http/Controllers/PnL/ExpenseController.php` | `your-project/app/Http/Controllers/PnL/ExpenseController.php` |
| `routes/pnl.php` | `your-project/routes/pnl.php` |
| `resources/views/pnl/expenses/create.blade.php` | `your-project/resources/views/pnl/expenses/create.blade.php` |
| `resources/views/pnl/expenses/edit.blade.php` | `your-project/resources/views/pnl/expenses/edit.blade.php` |
| `resources/views/pnl/expenses/show.blade.php` | `your-project/resources/views/pnl/expenses/show.blade.php` |

---

## Step 3: Install PDF Library (if not already installed)

```bash
composer require barryvdh/laravel-dompdf
```

---

## Step 4: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

---

## Step 5: Add Settings Menu Link (Optional)

Add a link to the settings page in your sidebar. Edit `resources/views/customer/sidemenu.blade.php`:

```php
<!-- Inside your P&L menu section -->
<a href="{{ route('pnl.settings.index') }}" class="dropdown-item">
    <i class="fas fa-cog"></i> Settings
</a>
```

---

## Step 6: Test the Upgrade

1. Visit `/pnl/settings` to configure your default VAT rate
2. Create a new expense and verify the tax rate is pre-filled
3. Try editing a revenue entry to add more tickets sold
4. Generate a PDF invoice to verify the new format

---

## New Routes Added

| Route | URL | Description |
|-------|-----|-------------|
| `pnl.settings.index` | `/pnl/settings` | Settings page |
| `pnl.settings.update` | `PUT /pnl/settings` | Update settings |
| `pnl.settings.reset-invoice` | `POST /pnl/settings/reset-invoice` | Reset invoice sequence |

---

## Breaking Changes

### Invoice Number Format
- **Old:** `INV-00001` (sequential only)
- **New:** `INV-202501-001` (year-month + sequence)

Existing invoice numbers are preserved. New invoices will use the new format.

### Database Schema
- New columns added to `pnl_expenses` and `pnl_payments` tables
- New `pnl_settings` table created
- Existing data is migrated automatically by the SQL script

---

## Rollback

If you need to rollback, restore your database backup. The new columns and table can be dropped with:

```sql
-- WARNING: Only run if you need to completely remove v2.0 changes
DROP TABLE IF EXISTS `pnl_settings`;

ALTER TABLE `pnl_expenses`
    DROP COLUMN `user_id`,
    DROP COLUMN `tax_rate`,
    DROP COLUMN `total_amount`,
    DROP COLUMN `is_taxable`,
    DROP INDEX `idx_pnl_expenses_user_id`,
    DROP INDEX `idx_pnl_expenses_invoice_number`;

ALTER TABLE `pnl_payments`
    DROP COLUMN `user_id`,
    DROP COLUMN `reminder_count`,
    DROP COLUMN `send_email_to_vendor`,
    DROP COLUMN `deleted_at`,
    DROP INDEX `idx_pnl_payments_user_id`;
```

---

## Support

If you encounter issues during migration, check:
1. Your MySQL version supports the column types
2. Foreign key constraints are not violated
3. All required PHP packages are installed

For the latest version and updates, refer to the main `INSTALLATION_GUIDE.md`.
