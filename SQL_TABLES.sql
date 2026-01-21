-- ==============================================
-- P&L MODULE - SQL DATABASE TABLES
-- Version: 2.0 (Fresh Install)
-- ==============================================
-- Run these SQL statements directly in your MySQL database
-- All tables are prefixed with 'pnl_' to avoid conflicts
-- Currency: GBP (£)
-- ==============================================

-- ---------------------------------------------
-- TABLE 0: pnl_settings (Per-Organiser Settings)
-- ---------------------------------------------
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

-- ---------------------------------------------
-- TABLE 1: pnl_events
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `pnl_events` (
    `id` CHAR(36) NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `venue` VARCHAR(255) NULL,
    `location` VARCHAR(255) NULL,
    `event_date` DATE NOT NULL,
    `event_time` TIME NULL,
    `status` ENUM('draft', 'planning', 'active', 'completed', 'cancelled') DEFAULT 'planning',
    `budget` DECIMAL(15, 2) DEFAULT 0.00,
    `expected_revenue` DECIMAL(15, 2) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_pnl_events_user_id` (`user_id`),
    INDEX `idx_pnl_events_status` (`status`),
    INDEX `idx_pnl_events_event_date` (`event_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------
-- TABLE 2: pnl_vendors (Artists, DJs, Caterers, etc.)
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `pnl_vendors` (
    `id` CHAR(36) NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `type` ENUM('artist', 'dj', 'vendor', 'caterer', 'security', 'equipment', 'venue', 'marketing', 'staff', 'other') DEFAULT 'vendor',
    `full_name` VARCHAR(255) NOT NULL,
    `business_name` VARCHAR(255) NULL,
    `email` VARCHAR(255) NULL,
    `phone` VARCHAR(50) NULL,
    `alternate_phone` VARCHAR(50) NULL,
    `business_address` TEXT NULL,
    `home_address` TEXT NULL,
    `emergency_contact_name` VARCHAR(255) NULL,
    `emergency_contact_phone` VARCHAR(50) NULL,
    `emergency_contact_relation` VARCHAR(100) NULL,
    `bank_name` VARCHAR(255) NULL,
    `bank_branch` VARCHAR(255) NULL,
    `bank_account_name` VARCHAR(255) NULL,
    `bank_account_number` VARCHAR(100) NULL,
    `bank_ifsc_code` VARCHAR(50) NULL COMMENT 'Sort Code for UK',
    `pan_number` VARCHAR(50) NULL COMMENT 'UTR for UK',
    `gst_number` VARCHAR(50) NULL COMMENT 'VAT Number for UK',
    `tax_vat_reference` VARCHAR(100) NULL COMMENT 'Company Number',
    `preferred_payment_cycle` VARCHAR(50) NULL,
    `specialization` VARCHAR(255) NULL COMMENT 'Service area description',
    `notes` TEXT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_pnl_vendors_user_id` (`user_id`),
    INDEX `idx_pnl_vendors_type` (`type`),
    INDEX `idx_pnl_vendors_email` (`email`),
    INDEX `idx_pnl_vendors_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------
-- TABLE 3: pnl_expense_categories
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `pnl_expense_categories` (
    `id` CHAR(36) NOT NULL,
    `user_id` BIGINT UNSIGNED NULL COMMENT 'NULL for system defaults',
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
    INDEX `idx_pnl_expense_categories_user_id` (`user_id`),
    INDEX `idx_pnl_expense_categories_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------
-- TABLE 4: pnl_expenses
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `pnl_expenses` (
    `id` CHAR(36) NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `event_id` CHAR(36) NOT NULL,
    `category_id` CHAR(36) NOT NULL,
    `vendor_id` CHAR(36) NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `tax_rate` DECIMAL(5, 2) DEFAULT 0.00 COMMENT 'Tax/VAT rate %',
    `tax_amount` DECIMAL(15, 2) DEFAULT 0.00,
    `total_amount` DECIMAL(15, 2) DEFAULT 0.00 COMMENT 'Amount + Tax',
    `is_taxable` TINYINT(1) DEFAULT 1,
    `expense_date` DATE NOT NULL,
    `invoice_number` VARCHAR(100) NULL COMMENT 'Format: INV-YYYYMM-XXX',
    `receipt_path` VARCHAR(500) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_pnl_expenses_user_id` (`user_id`),
    INDEX `idx_pnl_expenses_event_id` (`event_id`),
    INDEX `idx_pnl_expenses_category_id` (`category_id`),
    INDEX `idx_pnl_expenses_vendor_id` (`vendor_id`),
    INDEX `idx_pnl_expenses_expense_date` (`expense_date`),
    INDEX `idx_pnl_expenses_invoice_number` (`invoice_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------
-- TABLE 5: pnl_payments
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `pnl_payments` (
    `id` CHAR(36) NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `expense_id` CHAR(36) NOT NULL,
    `vendor_id` CHAR(36) NULL,
    `amount` DECIMAL(15, 2) NOT NULL,
    `status` ENUM('pending', 'scheduled', 'paid', 'cancelled') DEFAULT 'pending',
    `payment_method` VARCHAR(50) NULL COMMENT 'bank_transfer, cash, cheque, card',
    `scheduled_date` DATE NULL,
    `actual_paid_date` DATE NULL,
    `transaction_reference` VARCHAR(255) NULL,
    `internal_notes` TEXT NULL,
    `reminder_enabled` TINYINT(1) DEFAULT 1,
    `reminder_days_before` INT DEFAULT 3,
    `reminder_on_due_date` TINYINT(1) DEFAULT 1,
    `reminder_count` INT DEFAULT 0,
    `last_reminder_sent_at` TIMESTAMP NULL,
    `send_email_to_vendor` TINYINT(1) DEFAULT 1 COMMENT 'Enable/disable email notifications to vendor',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_pnl_payments_user_id` (`user_id`),
    INDEX `idx_pnl_payments_expense_id` (`expense_id`),
    INDEX `idx_pnl_payments_vendor_id` (`vendor_id`),
    INDEX `idx_pnl_payments_status` (`status`),
    INDEX `idx_pnl_payments_scheduled_date` (`scheduled_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------
-- TABLE 6: pnl_revenues (Ticket Sales)
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `pnl_revenues` (
    `id` CHAR(36) NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `event_id` CHAR(36) NOT NULL,
    `ticket_type` ENUM('general', 'vip', 'early_bird', 'group', 'premium', 'student', 'custom') DEFAULT 'general',
    `ticket_name` VARCHAR(255) NULL COMMENT 'Custom ticket name',
    `ticket_price` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `tickets_available` INT NOT NULL DEFAULT 0,
    `tickets_sold` INT NOT NULL DEFAULT 0,
    `tickets_refunded` INT DEFAULT 0,
    `platform_fees` DECIMAL(15, 2) DEFAULT 0.00 COMMENT 'TicketKart commission',
    `payment_gateway_fees` DECIMAL(15, 2) DEFAULT 0.00,
    `taxes` DECIMAL(15, 2) DEFAULT 0.00,
    `refund_amount` DECIMAL(15, 2) DEFAULT 0.00,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_pnl_revenues_user_id` (`user_id`),
    INDEX `idx_pnl_revenues_event_id` (`event_id`),
    INDEX `idx_pnl_revenues_ticket_type` (`ticket_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------
-- TABLE 7: pnl_attachments (For invoices, contracts)
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `pnl_attachments` (
    `id` CHAR(36) NOT NULL,
    `attachable_type` VARCHAR(255) NOT NULL,
    `attachable_id` CHAR(36) NOT NULL,
    `filename` VARCHAR(255) NOT NULL,
    `original_filename` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(100) NULL,
    `size` BIGINT UNSIGNED NULL,
    `path` VARCHAR(500) NOT NULL,
    `uploaded_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_pnl_attachments_attachable` (`attachable_type`, `attachable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------
-- TABLE 8: pnl_audit_logs
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `pnl_audit_logs` (
    `id` CHAR(36) NOT NULL,
    `user_id` BIGINT UNSIGNED NULL,
    `auditable_type` VARCHAR(255) NOT NULL,
    `auditable_id` CHAR(36) NOT NULL,
    `action` VARCHAR(50) NOT NULL COMMENT 'created, updated, deleted, status_changed',
    `old_values` JSON NULL,
    `new_values` JSON NULL,
    `reason` VARCHAR(500) NULL COMMENT 'Reason for the change',
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_pnl_audit_logs_auditable` (`auditable_type`, `auditable_id`),
    INDEX `idx_pnl_audit_logs_user_id` (`user_id`),
    INDEX `idx_pnl_audit_logs_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- END OF SQL SCRIPT
-- ==============================================
-- 
-- NOTES:
-- 1. All tables are prefixed with 'pnl_' to avoid conflicts
-- 2. Default currency is GBP (£)
-- 3. All primary keys use UUID (CHAR(36)) for better distribution
-- 4. user_id links to your existing users table
-- 5. Revenue calculations (gross_revenue, net_revenue) are computed in PHP, not stored
-- 6. Invoice numbers use format: INV-YYYYMM-XXX (e.g., INV-202501-001)
-- 7. Default VAT rate is 20% (UK standard)
-- 
-- To drop all P&L tables (CAREFUL - deletes all data!):
-- DROP TABLE IF EXISTS pnl_audit_logs, pnl_attachments, pnl_revenues, pnl_payments, pnl_expenses, pnl_expense_categories, pnl_vendors, pnl_events, pnl_settings;
-- ==============================================
