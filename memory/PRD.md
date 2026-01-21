# P&L Module for TicketKart - Product Requirements Document

## Version History
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
- `pnl_vendors` - Vendors with international phone support
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

## Recent Changes (v2.8)

### Fixed
- Walkthrough guide now shows on first visit
- Phone country dropdown works with native select
- Removed duplicate files and unused code
- Added proper comments to all controllers

### Cleaned Up
- Removed `sidebar-menu.blade.php` (duplicate)
- Removed `categories/index.blade.php` (moved to configuration)
- Removed `service-types/index.blade.php` (moved to configuration)
- Controllers now have proper documentation headers

## Future/Backlog
- Add more sold tickets functionality
- File uploads for invoices/contracts
- Budget vs Actual comparison charts
- API endpoints for mobile app
