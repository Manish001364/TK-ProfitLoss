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
- **Layout System:** Custom organiser layout (`layouts.organiser_layout`)
- **Sidebar:** Custom (`customer/sidemenu.blade.php`)

---

## Core Features Implemented

### 1. Vendor/Artist Management ✅
- Full contact management (Name, Email, Phone, Addresses)
- Bank details storage (for reference only)
- Tax information (PAN, GST, VAT)
- Emergency contact details
- Payment history tracking
- Export to Excel

### 2. Payment Tracking ✅
- Manual payment tracking with statuses: Pending, Scheduled, Paid
- Scheduled payment dates
- Payment methods: Bank Transfer, Cash, Cheque, UPI
- Transaction reference tracking
- Mark as Paid functionality
- Overdue payment alerts

### 3. Automated Email Reminders ✅
- Payment reminder console command
- Configurable reminder days before due date
- Reminder on due date option
- Email template for reminders

### 4. Expense Management ✅
- Expense categories with types (Fixed/Variable)
- Budget limits per category
- Full expense tracking with vendor assignment
- Tax amount tracking
- Invoice number recording

### 5. Revenue Tracking ✅
- Ticket sales tracking by type
- Gross/Net revenue calculations
- Platform fees, gateway fees, tax deductions
- Refund tracking
- Per-event breakdown

### 6. P&L Dashboard ✅
- Total Revenue, Total Expenses, Net Profit/Loss
- Visual charts (Chart.js)
- Event filter and date range filter
- Recent events performance table
- Upcoming and overdue payments widgets

### 7. Exporting ✅
- Vendor contacts to Excel
- P&L Summary to Excel/PDF
- Event P&L to Excel

### 8. Audit Log ✅
- Track all changes (create, update, delete)
- Old/new value comparison
- User tracking
- Filterable by action and date

---

## Database Schema (8 Tables)

| Table | Purpose |
|-------|---------|
| `pnl_events` | Event details, budget, dates |
| `pnl_vendors` | Vendor/artist contact info |
| `pnl_expense_categories` | Category definitions |
| `pnl_expenses` | Individual expenses |
| `pnl_payments` | Payment tracking |
| `pnl_revenues` | Ticket sales revenue |
| `pnl_attachments` | File attachments (polymorphic) |
| `pnl_audit_logs` | Change history |

All tables prefixed with `pnl_` - does NOT touch existing tables.

---

## Technical Architecture

### Directory Structure
```
app/
├── Console/Commands/SendPaymentReminders.php
├── Exports/
│   ├── EventPnlExport.php
│   ├── PnlSummaryExport.php
│   └── VendorsExport.php
├── Http/Controllers/PnL/
│   ├── DashboardController.php
│   ├── EventController.php
│   ├── VendorController.php
│   ├── ExpenseController.php
│   ├── PaymentController.php
│   ├── RevenueController.php
│   └── ...
├── Mail/PaymentReminderMail.php
├── Models/PnL/
│   ├── PnlEvent.php
│   ├── PnlVendor.php
│   ├── PnlExpense.php
│   └── ...
├── Providers/PnLServiceProvider.php
└── Traits/HasAuditLog.php

database/migrations/
└── 2024_01_01_000001-8_create_pnl_*.php

resources/views/pnl/
├── dashboard/
├── events/
├── vendors/
├── expenses/
├── payments/
├── revenues/
├── categories/
└── audit/

routes/pnl.php
```

### Layout Integration
- Views extend `layouts.organiser_layout`
- Content goes in `@section('content')`
- JavaScript goes in `@section('customjs')`
- Uses Bootstrap 5 classes
- Compatible with existing jQuery and Select2

---

## Session Changelog

### December 2025 - Initial Build
- Created complete P&L module from scratch
- Built 8 migration files, 8 models, 10 controllers
- Created all Blade views with AdminLTE layout (initial)

### December 2025 - Layout Fix
- **Issue:** Module views used AdminLTE layout which user doesn't have
- **Solution:** Updated all 21 Blade views to use `layouts.organiser_layout`
- Updated INSTALLATION_GUIDE.md with correct sidebar menu instructions
- Re-packaged module into `ticketkart-pnl-module.zip`

---

## Deliverables

1. **ticketkart-pnl-module.zip** - Complete module package
2. **INSTALLATION_GUIDE.md** - Step-by-step installation instructions
3. **README.md** - Module overview

---

## Next Steps (For User)

1. Download `ticketkart-pnl-module.zip`
2. Follow INSTALLATION_GUIDE.md step-by-step
3. Add menu link to `sidemenu.blade.php`
4. Run migrations
5. Test at `/pnl/dashboard`

---

## Future Enhancements (Backlog)

- [ ] File upload for invoices/contracts (infrastructure ready)
- [ ] Cash flow projections view
- [ ] Budget vs Actual comparison charts
- [ ] Multi-currency support
- [ ] API endpoints for mobile app
- [ ] Integration with TicketKart's existing ticket sales data
