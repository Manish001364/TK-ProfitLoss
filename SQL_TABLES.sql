-- ==============================================
-- P&L MODULE - SQL DATABASE TABLES
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
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
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
    INDEX `idx_pnl_events_status` (`status`),
    INDEX `idx_pnl_events_event_date` (`event_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------
-- TABLE 2: pnl_vendors (Artists, DJs, Caterers, etc.)
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `pnl_vendors` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
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
    INDEX `idx_pnl_vendors_type` (`type`),
    INDEX `idx_pnl_vendors_email` (`email`),
    INDEX `idx_pnl_vendors_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------
-- TABLE 3: pnl_expense_categories
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `pnl_expense_categories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
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
    INDEX `idx_pnl_expense_categories_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default expense categories
INSERT INTO `pnl_expense_categories` (`name`, `type`, `color`, `icon`, `sort_order`) VALUES
('Artist Fee', 'fixed', '#dc3545', 'fas fa-music', 1),
('Venue', 'fixed', '#fd7e14', 'fas fa-building', 2),
('Staff', 'variable', '#28a745', 'fas fa-users', 3),
('Marketing', 'variable', '#17a2b8', 'fas fa-bullhorn', 4),
('Equipment', 'variable', '#6f42c1', 'fas fa-cogs', 5),
('Catering', 'variable', '#ffc107', 'fas fa-utensils', 6),
('Security', 'variable', '#343a40', 'fas fa-shield-alt', 7),
('Miscellaneous', 'variable', '#6c757d', 'fas fa-ellipsis-h', 8);

-- ---------------------------------------------
-- TABLE 4: pnl_expenses
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `pnl_expenses` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NULL,
    `event_id` BIGINT UNSIGNED NOT NULL,
    `category_id` BIGINT UNSIGNED NOT NULL,
    `vendor_id` BIGINT UNSIGNED NULL,
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
    INDEX `idx_pnl_expenses_invoice_number` (`invoice_number`),
    CONSTRAINT `fk_pnl_expenses_event` FOREIGN KEY (`event_id`) REFERENCES `pnl_events` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pnl_expenses_category` FOREIGN KEY (`category_id`) REFERENCES `pnl_expense_categories` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_pnl_expenses_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `pnl_vendors` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------
-- TABLE 5: pnl_payments
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `pnl_payments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NULL,
    `expense_id` BIGINT UNSIGNED NOT NULL,
    `vendor_id` BIGINT UNSIGNED NULL,
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
    INDEX `idx_pnl_payments_scheduled_date` (`scheduled_date`),
    CONSTRAINT `fk_pnl_payments_expense` FOREIGN KEY (`expense_id`) REFERENCES `pnl_expenses` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pnl_payments_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `pnl_vendors` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------
-- TABLE 6: pnl_revenues (Ticket Sales)
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `pnl_revenues` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `event_id` BIGINT UNSIGNED NOT NULL,
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
    INDEX `idx_pnl_revenues_event_id` (`event_id`),
    INDEX `idx_pnl_revenues_ticket_type` (`ticket_type`),
    CONSTRAINT `fk_pnl_revenues_event` FOREIGN KEY (`event_id`) REFERENCES `pnl_events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------
-- TABLE 7: pnl_attachments (For invoices, contracts)
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `pnl_attachments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `attachable_id` BIGINT UNSIGNED NOT NULL,
    `attachable_type` VARCHAR(255) NOT NULL COMMENT 'expense, vendor, event',
    `filename` VARCHAR(255) NOT NULL,
    `original_filename` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,
    `file_size` INT NULL,
    `mime_type` VARCHAR(100) NULL,
    `uploaded_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_pnl_attachments_attachable` (`attachable_type`, `attachable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------
-- TABLE 8: pnl_audit_logs (Change history)
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS `pnl_audit_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NULL,
    `loggable_id` BIGINT UNSIGNED NOT NULL,
    `loggable_type` VARCHAR(255) NOT NULL,
    `action` ENUM('created', 'updated', 'deleted') NOT NULL,
    `old_values` JSON NULL,
    `new_values` JSON NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_pnl_audit_logs_loggable` (`loggable_type`, `loggable_id`),
    INDEX `idx_pnl_audit_logs_user_id` (`user_id`),
    INDEX `idx_pnl_audit_logs_action` (`action`),
    INDEX `idx_pnl_audit_logs_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- END OF SQL SCRIPT
-- ==============================================
-- 
-- NOTES:
-- 1. All tables are prefixed with 'pnl_' to avoid conflicts with existing tables
-- 2. Default currency is GBP (£) - store values as decimal
-- 3. Foreign keys ensure data integrity
-- 4. Soft deletes enabled on main tables (deleted_at column)
-- 5. Default expense categories are inserted automatically
-- 6. pnl_settings stores per-organiser VAT defaults and invoice settings
-- 7. Invoice numbers use format: INV-YYYYMM-XXX (e.g., INV-202501-001)
-- 
-- To drop all P&L tables (CAREFUL - deletes all data!):
-- DROP TABLE IF EXISTS pnl_audit_logs, pnl_attachments, pnl_revenues, pnl_payments, pnl_expenses, pnl_expense_categories, pnl_vendors, pnl_events, pnl_settings;
-- ==============================================
