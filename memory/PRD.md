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
- Per-expense email notification toggle

### 3. Automated Email Reminders ✅
- Payment reminder console command
- Configurable reminder days before due date
- Email template for reminders
- Invoice emails with PDF attachments

### 4. Expense Management ✅
- Expense categories with types (Fixed/Variable)
- Budget limits per category
- Link to add new vendor from expense form
- **Tax/VAT rate per expense (configurable)**
- **Taxable/Non-taxable toggle**
- **Auto-generated invoice numbers (INV-YYYYMM-XXX format)**
- **Editable invoice numbers**
- **PDF invoice generation**
- **Email invoice to vendor**

### 5. Revenue Tracking ✅
- Ticket sales tracking by type
- **Revenue edit page with "Add More Tickets Sold" feature**
- Quick-add buttons (+1, +5, +10, +25, +50, +100)
- Gross/Net revenue calculations
- Platform fees, gateway fees, VAT deductions
- Refund tracking
- Per-event breakdown

### 6. P&L Dashboard ✅
- Summary cards with left-colored borders (matching TicketKart style)
- Smaller pie chart for expense breakdown
- Date range picker (single calendar for range selection)
- Auto-filter on dropdown selection
- Visual bar charts (Chart.js)
- Recent events performance table
- Upcoming and overdue payments widgets
- **Settings button in header**

### 7. Per-Organiser Settings ✅ (NEW)
- **Default VAT/Tax rate configuration**
- **Invoice prefix customization**
- **Invoice sequence reset**
- **Company details for invoices**
- **Email notification preferences:**
  - On invoice created
  - On payment scheduled
  - On payment completed

### 8. Currency ✅
- All values in GBP (£)
- Consistent throughout all views

### 9. UI/UX Improvements ✅
- Narrower layout (max-width: 900-1200px) matching TicketKart style
- Clean cards with subtle shadows
- Smaller fonts and better spacing
- Collapsible sections for bank/tax details
- Status badges with subtle background colors

---

## Database Schema

### Tables (9 total - all prefixed with `pnl_`):

| Table | Purpose |
|-------|---------|
| `pnl_settings` | Per-user settings (VAT, invoice prefix, email prefs) |
| `pnl_events` | Event details, budget, dates |
| `pnl_vendors` | Vendor/artist contact info, service type |
| `pnl_expense_categories` | Category definitions |
| `pnl_expenses` | Individual expenses with tax info |
| `pnl_payments` | Payment tracking with email toggle |
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
- Smaller pie chart on event show page
- Date range picker for dashboard filters
- Auto-filter on dropdown selection
- Improved vendor form with optional fields
- Created SQL_TABLES.sql as alternative to migrations
- Narrowed UI to match TicketKart's event creation page
- Updated INSTALLATION_GUIDE.md

### January 2025 - Version 2.0 Features (Current)
- **Added pnl_settings table for per-organiser settings**
- **Added tax_rate, total_amount, is_taxable columns to expenses**
- **Added send_email_to_vendor column to payments**
- **New invoice number format: INV-YYYYMM-XXX**
- **Created Settings page with VAT, invoice, and email preferences**
- **Created Revenue edit page with quick-add ticket buttons**
- **Created Expense edit page**
- **PDF invoice generation with barryvdh/laravel-dompdf**
- **Email invoice functionality**
- **Created MIGRATION_GUIDE.md for existing users**
- **Updated INSTALLATION_GUIDE.md**

---

## Deliverables

1. **ticketkart-pnl-module.zip** - Complete module package
2. **SQL_TABLES.sql** - Raw SQL for direct database creation
3. **INSTALLATION_GUIDE.md** - Step-by-step installation instructions
4. **MIGRATION_GUIDE.md** - Upgrade instructions for existing users
5. **README.md** - Module overview

---

## Installation Summary

1. Install required packages: `maatwebsite/excel`, `barryvdh/laravel-dompdf`
2. Copy module files to Laravel project
3. Register `PnLServiceProvider` in `config/app.php`
4. Add routes in `routes/web.php`
5. Run `SQL_TABLES.sql` in MySQL (or use migrations)
6. Add menu link to `sidemenu.blade.php`
7. Clear cache and test at `/pnl/dashboard`
8. Configure settings at `/pnl/settings`

---

## Key Routes

| Route | URL | Description |
|-------|-----|-------------|
| `pnl.dashboard` | `/pnl/dashboard` | Main Dashboard |
| `pnl.settings.index` | `/pnl/settings` | Settings Page |
| `pnl.vendors.index` | `/pnl/vendors` | Vendors List |
| `pnl.expenses.index` | `/pnl/expenses` | Expenses List |
| `pnl.expenses.pdf` | `/pnl/expenses/{id}/pdf` | Download PDF Invoice |
| `pnl.expenses.email` | `POST /pnl/expenses/{id}/email` | Email Invoice |
| `pnl.revenues.edit` | `/pnl/revenues/{id}/edit` | Edit/Add Tickets |
| `pnl.payments.upcoming` | `/pnl/payments/upcoming` | Upcoming Payments |

---

## Future Enhancements (Backlog)

- [ ] File upload for invoices/contracts
- [ ] Cash flow projections view
- [ ] Budget vs Actual comparison charts
- [ ] Multi-currency support
- [ ] API endpoints for mobile app
- [ ] Integration with TicketKart's existing ticket sales data

---

## Technical Dependencies

### Required Composer Packages
```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

### Key Models
- `PnlSettings` - Per-user settings
- `PnlExpense` - Expenses with tax fields
- `PnlPayment` - Payments with email toggle
- `PnlVendor` - Vendor/artist contacts
- `PnlEvent` - Event management
- `PnlRevenue` - Ticket sales

### Key Controllers
- `SettingsController` - Settings management
- `ExpenseController` - Expense CRUD + PDF/Email
- `RevenueController` - Revenue CRUD
- `PaymentController` - Payment tracking
- `VendorController` - Vendor management
- `DashboardController` - Dashboard stats
