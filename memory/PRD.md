# P&L Module for TicketKart - Product Requirements Document

## Project Overview
A self-contained Profit & Loss (P&L) module designed for integration into the TicketKart Laravel-based event ticketing platform.

## Original Problem Statement
Build a P&L module for ticketkart.com in PHP (Laravel) for easy integration as a new feature into the existing platform.

## Target Platform
- **Application:** ticketkart.com
- **Framework:** Laravel 10+
- **Database:** MySQL
- **UI Framework:** Bootstrap 5
- **Currency:** GBP (£)
- **Layout System:** Custom organiser layout (`layouts.organiser_layout`)
- **Sidebar:** Custom (`customer/sidemenu.blade.php`)

---

## Core Features Implemented

### 1. Vendor/Artist Management ✅
- Comprehensive form with service type (DJ, Caterer, Artist, etc.)
- Only vendor name is mandatory - all other fields optional
- Contact details: email, phone, secondary phone, addresses
- Emergency contact information
- Bank details (collapsible section)
- Tax information: UTR, VAT Number, Company Number
- Service area / specialization field
- Notes and description
- Export to Excel

### 2. Payment Tracking ✅
- Payment statuses: Pending, Scheduled, Paid
- Scheduled payment dates with "days until due" display
- Payment methods: Bank Transfer (BACS), Cash, Cheque, Card
- Mark as Paid functionality
- Overdue payment alerts (highlighted in red)
- Fixed "N/A" display - now shows "Not assigned" or "Not set"

### 3. Automated Email Reminders ✅
- Payment reminder console command
- Configurable reminder days before due date
- Email template for reminders

### 4. Expense Management ✅
- Expense categories with types (Fixed/Variable)
- Budget limits per category
- Link to add new vendor from expense form
- Tax/VAT amount tracking
- Invoice number recording

### 5. Revenue Tracking ✅
- Ticket sales tracking by type
- **"+ Add more tickets sold" button** next to sold count
- Gross/Net revenue calculations
- Platform fees, gateway fees, VAT deductions
- Refund tracking
- Per-event breakdown

### 6. P&L Dashboard ✅
- Summary cards with left-colored borders (matching TicketKart style)
- **Smaller pie chart** for expense breakdown
- **Date range picker** (single calendar for range selection)
- **Auto-filter on dropdown selection**
- Visual bar charts (Chart.js)
- Recent events performance table
- Upcoming and overdue payments widgets

### 7. Currency ✅
- **All values now in GBP (£)** instead of ₹
- Consistent throughout all views

### 8. UI/UX Improvements ✅
- **Narrower layout** (max-width: 900-1200px) matching TicketKart style
- Clean cards with subtle shadows
- Smaller fonts and better spacing
- Collapsible sections for bank/tax details
- Status badges with subtle background colors

---

## Database Options

### Option A: Raw SQL (Recommended)
Use `SQL_TABLES.sql` - run directly in MySQL to avoid migration conflicts.

### Option B: Laravel Migrations
Use files in `database/migrations/` folder.

### Tables (8 total - all prefixed with `pnl_`):

| Table | Purpose |
|-------|---------|
| `pnl_events` | Event details, budget, dates |
| `pnl_vendors` | Vendor/artist contact info, service type |
| `pnl_expense_categories` | Category definitions |
| `pnl_expenses` | Individual expenses |
| `pnl_payments` | Payment tracking |
| `pnl_revenues` | Ticket sales revenue |
| `pnl_attachments` | File attachments (polymorphic) |
| `pnl_audit_logs` | Change history |

---

## Session Changelog

### December 2025 - Initial Build
- Created complete P&L module from scratch
- Built 8 migration files, 8 models, 10 controllers
- Created all Blade views

### January 2025 - Layout & Design Fix
- Updated all views to use `layouts.organiser_layout`
- Changed currency from ₹ to £ (GBP)
- **Smaller pie chart** on event show page
- **Date range picker** for dashboard filters
- **Auto-filter** on dropdown selection
- Fixed "N/A" display in upcoming payments
- Improved vendor form with:
  - Only name mandatory
  - Service type/specialization field
  - Collapsible bank & tax sections
- Added "+ Add more tickets" button to revenue display
- **Created SQL_TABLES.sql** as alternative to migrations
- Narrowed UI to match TicketKart's event creation page
- Updated INSTALLATION_GUIDE.md

---

## Deliverables

1. **ticketkart-pnl-module.zip** - Complete module package
2. **SQL_TABLES.sql** - Raw SQL for direct database creation
3. **INSTALLATION_GUIDE.md** - Step-by-step instructions
4. **README.md** - Module overview

---

## Installation Summary

1. Copy module files to Laravel project
2. Register `PnLServiceProvider` in `config/app.php`
3. Add routes in `routes/web.php`
4. Run `SQL_TABLES.sql` in MySQL (or use migrations)
5. Add menu link to `sidemenu.blade.php`
6. Clear cache and test at `/pnl/dashboard`

---

## Future Enhancements (Backlog)

- [ ] File upload for invoices/contracts
- [ ] Cash flow projections view
- [ ] Budget vs Actual comparison charts
- [ ] Multi-currency support
- [ ] API endpoints for mobile app
- [ ] Integration with TicketKart's existing ticket sales data
- [ ] Currency settings page
