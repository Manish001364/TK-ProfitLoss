# P&L Module Migration Guide

## Upgrading from Previous Versions

If you have already installed a previous version of the P&L module, follow these steps to upgrade to the latest version.

---

## Version 2.6 Changes (January 2025) - LATEST

### New Features
- **Service Types Management** - System default + custom vendor service types (like expense categories)
- **13 Default Service Types**: Artist, DJ, Venue, Catering, Security, Equipment Hire, Marketing, Staff, Transport, Photography, Decor, MC/Host, Other
- **Custom Service Types**: Users can create their own vendor categories
- **Improved Sidebar Navigation**: Configuration section with dividers

### Database Migration for v2.6

Run this SQL to create service types tables:

```sql
-- ==============================================
-- MIGRATION SCRIPT v2.6 - Service Types
-- ==============================================

-- 1. Create System Service Types Table
CREATE TABLE IF NOT EXISTS `pnl_service_types_system` (
    `id` CHAR(36) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(50) NOT NULL,
    `description` TEXT NULL,
    `icon` VARCHAR(50) DEFAULT 'fas fa-user',
    `color` VARCHAR(20) DEFAULT '#6366f1',
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `idx_pnl_service_types_system_slug` (`slug`),
    INDEX `idx_pnl_service_types_system_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Insert default system service types
INSERT INTO `pnl_service_types_system` (`id`, `name`, `slug`, `description`, `icon`, `color`, `sort_order`) VALUES
(UUID(), 'Artist', 'artist', 'Musicians, bands, performers', 'fas fa-music', '#dc3545', 1),
(UUID(), 'DJ', 'dj', 'Disc jockeys and sound controllers', 'fas fa-headphones', '#6f42c1', 2),
(UUID(), 'Venue', 'venue', 'Event venues and locations', 'fas fa-building', '#0dcaf0', 3),
(UUID(), 'Catering', 'catering', 'Food and beverage services', 'fas fa-utensils', '#fd7e14', 4),
(UUID(), 'Security', 'security', 'Security personnel and services', 'fas fa-shield-alt', '#6c757d', 5),
(UUID(), 'Equipment Hire', 'equipment', 'Sound, lighting, and stage equipment', 'fas fa-cogs', '#20c997', 6),
(UUID(), 'Marketing', 'marketing', 'Advertising, PR, and promotions', 'fas fa-bullhorn', '#d63384', 7),
(UUID(), 'Staff', 'staff', 'Event staff and volunteers', 'fas fa-users', '#198754', 8),
(UUID(), 'Transport', 'transport', 'Transportation and logistics', 'fas fa-truck', '#0d6efd', 9),
(UUID(), 'Photography', 'photography', 'Photographers and videographers', 'fas fa-camera', '#ffc107', 10),
(UUID(), 'Decor', 'decor', 'Decorations and staging', 'fas fa-paint-brush', '#17a2b8', 11),
(UUID(), 'MC/Host', 'mc', 'Master of ceremonies and event hosts', 'fas fa-microphone', '#6610f2', 12),
(UUID(), 'Other', 'other', 'Other service providers', 'fas fa-ellipsis-h', '#adb5bd', 99);

-- 3. Create User Service Types Table
CREATE TABLE IF NOT EXISTS `pnl_service_types_user` (
    `id` CHAR(36) NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(50) NOT NULL,
    `description` TEXT NULL,
    `icon` VARCHAR(50) DEFAULT 'fas fa-user',
    `color` VARCHAR(20) DEFAULT '#6366f1',
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `idx_pnl_service_types_user_slug` (`user_id`, `slug`),
    INDEX `idx_pnl_service_types_user_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- END OF v2.6 MIGRATION
-- ==============================================
```

### Important Notes for v2.6

1. **Service Types**: The sidebar now shows "Service Types" under Configuration. Users can view system defaults and create custom types.

2. **Backward Compatibility**: Existing vendors using old type values (artist, dj, vendor, etc.) will continue to work. The new system types use the same slugs.

3. **New Route**: `/pnl/service-types` for managing service types.

---

## Version 2.5 Changes (January 2025)

### New Features
- **International Phone Numbers** - Phone fields now use intl-tel-input with country flags and validation
- **Address Enhancements** - Country dropdown and postcode fields with country-specific validation
- **Multi-Currency Support** - Set default currency and define custom exchange rates
- **Cash Flow Projections** - New dedicated page for visualising upcoming payments and expected revenue
- **Exchange Rate Management** - User-defined conversion rates for multi-currency events

### Database Migration for v2.5

Run this SQL to add new columns to existing tables:

```sql
-- ==============================================
-- MIGRATION SCRIPT v2.5 - Phone, Address & Currency
-- ==============================================

-- 1. Add phone country code columns to pnl_vendors
ALTER TABLE `pnl_vendors`
    ADD COLUMN IF NOT EXISTS `phone_country_code` VARCHAR(10) DEFAULT '+44' AFTER `email`,
    ADD COLUMN IF NOT EXISTS `alternate_phone_country_code` VARCHAR(10) DEFAULT '+44' AFTER `alternate_phone`,
    ADD COLUMN IF NOT EXISTS `business_country` VARCHAR(100) DEFAULT 'United Kingdom' AFTER `business_address`,
    ADD COLUMN IF NOT EXISTS `business_postcode` VARCHAR(20) NULL AFTER `business_country`,
    ADD COLUMN IF NOT EXISTS `home_country` VARCHAR(100) NULL AFTER `home_address`,
    ADD COLUMN IF NOT EXISTS `home_postcode` VARCHAR(20) NULL AFTER `home_country`,
    ADD COLUMN IF NOT EXISTS `emergency_contact_phone_country_code` VARCHAR(10) DEFAULT '+44' AFTER `emergency_contact_name`,
    ADD COLUMN IF NOT EXISTS `specialization` VARCHAR(255) NULL AFTER `preferred_payment_cycle`;

-- 2. Add currency columns to pnl_settings
ALTER TABLE `pnl_settings`
    ADD COLUMN IF NOT EXISTS `default_currency` VARCHAR(3) DEFAULT 'GBP' AFTER `default_tax_rate`;

-- 3. Add currency column to pnl_events
ALTER TABLE `pnl_events`
    ADD COLUMN IF NOT EXISTS `currency` VARCHAR(3) DEFAULT 'GBP' AFTER `budget`,
    ADD COLUMN IF NOT EXISTS `expected_revenue` DECIMAL(15, 2) NULL AFTER `currency`;

-- 4. Create pnl_currency_rates table
CREATE TABLE IF NOT EXISTS `pnl_currency_rates` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `from_currency` VARCHAR(3) NOT NULL DEFAULT 'GBP',
    `to_currency` VARCHAR(3) NOT NULL,
    `rate` DECIMAL(15, 6) NOT NULL COMMENT 'Conversion rate',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `idx_pnl_currency_rates_user_pair` (`user_id`, `from_currency`, `to_currency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- END OF v2.5 MIGRATION
-- ==============================================
```

### Important Notes for v2.5

1. **Phone Number Format**: Phone numbers are now stored as national format (without country code). The country code is stored separately in `phone_country_code` columns.

2. **Currency Support**: Events can now have individual currencies. Use the Settings page to define your default currency and exchange rates.

3. **Cash Flow Page**: Access the new Cash Flow Projections page from the sidebar navigation.

4. **Address Fields**: The new country and postcode fields use JavaScript validation for common formats (UK, US, India, etc.).

---

## Version 2.4 Changes (January 2025)

### New Features
- **Split Expense Categories** - System categories (read-only) separated from user categories (editable)
- **Dashboard Walkthrough** - First-time user onboarding guide
- **Smart Budget Tips** - Rule-based insights for budget utilisation and revenue optimisation
- **Chart Period Filters** - Filter Revenue vs Expenses chart by 3/6/12 months or Year to Date

### Database Migration for v2.4

Run this SQL to create the new category tables and migrate data:

```sql
-- ==============================================
-- MIGRATION SCRIPT v2.4 - Expense Category Split
-- ==============================================
-- This migration splits the expense categories into:
-- 1. pnl_expense_categories_system (read-only defaults)
-- 2. pnl_expense_categories_user (user-created categories)

-- 1. Create System Categories Table
CREATE TABLE IF NOT EXISTS `pnl_expense_categories_system` (
    `id` CHAR(36) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `type` ENUM('fixed', 'variable') DEFAULT 'variable',
    `description` TEXT NULL,
    `color` VARCHAR(20) DEFAULT '#6366f1',
    `icon` VARCHAR(50) DEFAULT 'fas fa-tag',
    `default_budget_limit` DECIMAL(15, 2) NULL,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_pnl_expense_categories_system_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Insert default system categories
INSERT INTO `pnl_expense_categories_system` (`id`, `name`, `type`, `description`, `color`, `icon`, `sort_order`) VALUES
(UUID(), 'Artist Fees', 'fixed', 'Payments to artists and performers', '#dc3545', 'fas fa-music', 1),
(UUID(), 'DJ Fees', 'fixed', 'Payments to DJs', '#6f42c1', 'fas fa-headphones', 2),
(UUID(), 'Venue Hire', 'fixed', 'Venue rental and facility costs', '#0dcaf0', 'fas fa-building', 3),
(UUID(), 'Catering', 'variable', 'Food and beverage expenses', '#fd7e14', 'fas fa-utensils', 4),
(UUID(), 'Security', 'variable', 'Security personnel and services', '#6c757d', 'fas fa-shield-alt', 5),
(UUID(), 'Equipment Hire', 'variable', 'Sound, lighting, and stage equipment', '#20c997', 'fas fa-cogs', 6),
(UUID(), 'Marketing', 'variable', 'Advertising, promotions, and marketing', '#d63384', 'fas fa-bullhorn', 7),
(UUID(), 'Staff', 'variable', 'Staff and volunteer expenses', '#198754', 'fas fa-users', 8),
(UUID(), 'Transportation', 'variable', 'Transport and logistics', '#0d6efd', 'fas fa-truck', 9),
(UUID(), 'Insurance', 'fixed', 'Event insurance and liability', '#ffc107', 'fas fa-file-contract', 10),
(UUID(), 'Licensing', 'fixed', 'Music licensing and permits', '#17a2b8', 'fas fa-certificate', 11),
(UUID(), 'Production', 'variable', 'Stage production and technical', '#6610f2', 'fas fa-theater-masks', 12),
(UUID(), 'Other', 'variable', 'Miscellaneous expenses', '#adb5bd', 'fas fa-tag', 99);

-- 3. Create User Categories Table
CREATE TABLE IF NOT EXISTS `pnl_expense_categories_user` (
    `id` CHAR(36) NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `type` ENUM('fixed', 'variable') DEFAULT 'variable',
    `description` TEXT NULL,
    `color` VARCHAR(20) DEFAULT '#6366f1',
    `icon` VARCHAR(50) DEFAULT 'fas fa-tag',
    `default_budget_limit` DECIMAL(15, 2) NULL,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_pnl_expense_categories_user_user_id` (`user_id`),
    INDEX `idx_pnl_expense_categories_user_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Migrate existing user-created categories to user table
INSERT INTO `pnl_expense_categories_user` (`id`, `user_id`, `name`, `type`, `description`, `color`, `icon`, `default_budget_limit`, `sort_order`, `is_active`, `created_at`, `updated_at`)
SELECT `id`, `user_id`, `name`, `type`, `description`, `color`, `icon`, `default_budget_limit`, `sort_order`, `is_active`, `created_at`, `updated_at`
FROM `pnl_expense_categories`
WHERE `user_id` IS NOT NULL;

-- 5. Add walkthrough_dismissed column to pnl_settings (if not exists)
ALTER TABLE `pnl_settings` 
    ADD COLUMN IF NOT EXISTS `walkthrough_dismissed` TINYINT(1) DEFAULT 0 COMMENT 'User dismissed the walkthrough';

-- 6. Add ticketkart_event_id column to pnl_events (if not exists)
ALTER TABLE `pnl_events` 
    ADD COLUMN IF NOT EXISTS `ticketkart_event_id` BIGINT UNSIGNED NULL COMMENT 'Links to main TicketKart events table' AFTER `user_id`,
    ADD INDEX IF NOT EXISTS `idx_pnl_events_tk_event` (`ticketkart_event_id`);

-- ==============================================
-- END OF v2.4 MIGRATION
-- ==============================================
```

### Important Notes for v2.4

1. **Backward Compatibility**: The original `pnl_expense_categories` table is kept for existing expense references. All foreign keys will continue to work.

2. **Category Display**: The UI now queries both system and user tables to display categories. System categories show a "System Default" badge and cannot be edited by users.

3. **New Categories**: When users create new categories, they are stored in `pnl_expense_categories_user`.

4. **Settings Route**: A new route `pnl.settings.dismiss-walkthrough` has been added for the walkthrough feature.

---

## Version 2.3 Changes (January 2025)

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
