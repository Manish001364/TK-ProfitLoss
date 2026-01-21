# P&L Module for TicketKart - Product Requirements Document

## Version History
- **v2.9** (January 2025) - CRITICAL FIX: Vendor creation ENUM error, Event view null category fix
- **v2.8** (January 2025) - Code cleanup, walkthrough fix, phone dropdown fix
- **v2.7** (January 2025) - Cash flow fix, payment emails, combined config page
- **v2.6** (January 2025) - Service types, multi-currency
- **v2.5** (January 2025) - International phone, cash flow projections

## Overview
Self-contained P&L (Profit & Loss) module for TicketKart event management platform.

## Core Features

### Implemented ✅

1. **Dashboard** - Overview with charts, totals, recent activity
2. **Events** - CRUD with budget tracking, currency support
3. **Vendors** - Artist/supplier management with international phone
4. **Expenses** - Categorized expenses with tax/VAT
5. **Revenue** - Ticket sales and income tracking
6. **Payments** - Payment scheduling and tracking
7. **PDF Invoices** - Generate and email invoices
8. **Multi-Currency** - GBP, USD, EUR, INR support
9. **Cash Flow** - Projections and upcoming payments
10. **First-Time Walkthrough** - Interactive guide for new users
11. **Categories & Services** - Combined configuration page
12. **Payment Emails** - Confirmation to vendor and organiser

### Database Tables
- `pnl_events` - Events with budget and currency
- `pnl_vendors` - Vendors with international phone support + service_type_id
- `pnl_expenses` - Expenses with tax and currency
- `pnl_revenues` - Revenue entries
- `pnl_payments` - Payment records
- `pnl_expense_categories` - User custom categories
- `pnl_expense_categories_system` - System default categories
- `pnl_service_types_user` - User custom service types
- `pnl_service_types_system` - System default service types
- `pnl_settings` - User settings
- `pnl_attachments` - File attachments
- `pnl_audit_logs` - Audit trail

## Deliverables
1. `ticketkart-pnl-module.zip` - Complete module package
2. `SQL_TABLES.sql` - Database schema
3. `INSTALLATION_GUIDE.md` - Setup instructions
4. `MIGRATION_GUIDE.md` - Upgrade instructions
5. `README.md` - Module overview

## Recent Changes (v2.9)

### Fixed
- **CRITICAL: Vendor Creation ENUM Error** - Service type slug was being saved directly to ENUM column. Now:
  - `service_type_id` stores the service type slug for display
  - `type` stores mapped ENUM value for backward compatibility
- **CRITICAL: Event Creation Error** - Removed call to non-existent `createDefaultsForUser()` method
- **CRITICAL: Expense Category Validation** - System categories now properly validated and shown in dropdown
- **Event View Null Category** - Added null-safe operators for expenses without categories

### Updated Files
- `EventController.php` - Removed `createDefaultsForUser()` call
- `ExpenseController.php` - Fixed category validation, use `getAllForUser()` everywhere
- `PnlExpenseCategory.php` - Rewritten to fetch from system/user tables like PnlServiceType
- `VendorController.php` - Added `mapServiceTypeToEnum()` method
- `PnlVendor.php` - Added `service_type_id` field and `service_type_name` accessor
- `PnlServiceType.php` - Added `getBySlugOrId()` method
- `SQL_TABLES.sql` - Added `service_type_id` column to pnl_vendors
- `events/show.blade.php` - Null-safe category display
- `vendors/index.blade.php`, `show.blade.php`, `edit.blade.php` - Use service_type_name

### Migration Required
```sql
ALTER TABLE `pnl_vendors`
    ADD COLUMN IF NOT EXISTS `service_type_id` CHAR(36) NULL AFTER `type`;
UPDATE `pnl_vendors` SET `service_type_id` = `type` WHERE `service_type_id` IS NULL;
```

## Future/Backlog
- Add more sold tickets functionality
- File uploads for invoices/contracts
- Budget vs Actual comparison charts
- API endpoints for mobile app
- Walkthrough guide fix (P1 - still needs testing)
