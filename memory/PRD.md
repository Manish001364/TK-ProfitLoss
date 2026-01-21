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
- **Edit form now matches create form with all options**
- **Disabled editing for paid expenses**

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
- **Collapsible sections with localStorage persistence**
- **Pagination controls (5/10/25 rows per table)**
- Smaller pie chart for expense breakdown
- Date range picker (single calendar for range selection)
- Auto-filter on dropdown selection
- Visual bar charts (Chart.js)
- Recent events performance table
- Upcoming and overdue payments widgets
- **Settings button in header**
- **Mobile-friendly layout**
- **Expense by Vendor Type section at bottom**
- **Smart Tips & Insights with budget utilisation analysis** (NEW v2.4)
- **First-time user walkthrough modal** (NEW v2.4)
- **Chart period filters (3/6/12 months, YTD)** (NEW v2.4)

### 7. Per-Organiser Settings ✅
- **Default VAT/Tax rate configuration**
- **Invoice prefix customization**
- **Invoice sequence reset**
- **Company details for invoices**
- **Email notification preferences:**
  - On invoice created
  - On payment scheduled
  - On payment completed
- **Walkthrough dismissed flag** (NEW v2.4)

### 8. Currency ✅
- All values in GBP (£)
- Consistent throughout all views
- Fixed: Budget (₹) → Budget (£)

### 9. UI/UX Improvements ✅
- Narrower layout (max-width: 900-1200px) matching TicketKart style
- Clean cards with subtle shadows
- Smaller fonts and better spacing
- Collapsible sections for bank/tax details
- Status badges with subtle background colors
- **White text on colored backgrounds for readability**

### 10. Sidebar Navigation ✅
- **Built-in P&L sidebar in all module pages**
- **Light-themed sidebar matching TicketKart style**
- **All P&L links accessible from sidebar**
- **CSS styling included**

### 11. Expense Categories - System & User Split ✅ (NEW v2.4)
- **System categories (read-only defaults)**: Artist Fees, DJ Fees, Venue Hire, Catering, Security, Equipment Hire, Marketing, Staff, Transportation, Insurance, Licensing, Production, Other
- **User categories (editable)**: Users can create custom categories
- **Protected system defaults**: Cannot be edited or deleted by users

---

## Database Schema

### Tables (11 total - all prefixed with `pnl_`):

| Table | Purpose |
|-------|---------|
| `pnl_settings` | Per-user settings (VAT, invoice prefix, email prefs) |
| `pnl_events` | Event details, budget, dates |
| `pnl_vendors` | Vendor/artist contact info, service type |
| `pnl_expense_categories` | Legacy category definitions (backward compat) |
| `pnl_expense_categories_system` | System default categories (NEW v2.4) |
| `pnl_expense_categories_user` | User-created categories (NEW v2.4) |
| `pnl_expenses` | Individual expenses with tax info |
| `pnl_payments` | Payment tracking with email toggle |
| `pnl_revenues` | Ticket sales revenue |
| `pnl_attachments` | File attachments (polymorphic) |
| `pnl_audit_logs` | Change history |

---

## Session Changelog

### January 2025 - Version 2.4 (Current)
- **Dashboard walkthrough**: First-time user onboarding modal with carousel slides
- **Smart budget tips**: Rule-based insights for budget utilisation (UK English)
- **Chart period filters**: 3/6/12 months or Year-to-Date filters for trend chart
- **Expense category split**: System defaults (read-only) + User categories (editable)
- **UI consistency fixes**: Warning headers now use `text-dark` for readability
- **Database schema updates**: New system/user category tables added

### January 2025 - Version 2.3
- **Built-in Sidebar Navigation** - All P&L pages now have integrated sidebar
- **White text on colored headers** - Better visibility
- **Protected default categories** - System categories can't be deleted
- **Fixed audit log column** - Added missing `reason` column

### January 2025 - Version 2.2
- **TicketKart Integration** - Added `ticketkart_event_id` column and import command
- **Revenue import command** - `php artisan pnl:import-revenue`

### January 2025 - Version 2.1
- **Dashboard: Collapsible sections with state persistence (localStorage)**
- **Dashboard: Pagination controls for tables (5/10/25 rows)**
- **Dashboard: Smaller, mobile-friendly charts**
- **Dashboard: Expense by Vendor Type moved to bottom**
- **Fixed: Currency symbol ₹ → £ in event edit page**
- **Fixed: Expense edit form now has all options from create form**
- **Fixed: Editing disabled for paid expenses**
- **Fixed: "N/A" bug in upcoming payments (null-safe operators)**

### January 2025 - Version 2.0
- **Added pnl_settings table for per-organiser settings**
- **Added tax_rate, total_amount, is_taxable columns to expenses**
- **Added send_email_to_vendor column to payments**
- **New invoice number format: INV-YYYYMM-XXX**
- **Created Settings page with VAT, invoice, and email preferences**
- **Created Revenue edit page with quick-add ticket buttons**
- **PDF invoice generation with barryvdh/laravel-dompdf**
- **Email invoice functionality**

---

## Deliverables

1. **ticketkart-pnl-module.zip** - Complete module package
2. **SQL_TABLES.sql** - Raw SQL for direct database creation
3. **INSTALLATION_GUIDE.md** - Step-by-step installation instructions with sidebar code
4. **MIGRATION_GUIDE.md** - Upgrade instructions for existing users
5. **README.md** - Module overview

---

## Key Routes

| Route | URL | Description |
|-------|-----|-------------|
| `pnl.dashboard` | `/pnl/dashboard` | Main Dashboard |
| `pnl.settings.index` | `/pnl/settings` | Settings Page |
| `pnl.settings.dismiss-walkthrough` | `POST /pnl/settings/dismiss-walkthrough` | Dismiss Walkthrough |
| `pnl.events.index` | `/pnl/events` | Events List |
| `pnl.vendors.index` | `/pnl/vendors` | Vendors List |
| `pnl.expenses.index` | `/pnl/expenses` | Expenses List |
| `pnl.expenses.pdf` | `/pnl/expenses/{id}/pdf` | Download PDF Invoice |
| `pnl.expenses.email` | `POST /pnl/expenses/{id}/email` | Email Invoice |
| `pnl.revenues.index` | `/pnl/revenues` | Revenue List |
| `pnl.payments.index` | `/pnl/payments` | Payments List |
| `pnl.categories.index` | `/pnl/categories` | Expense Categories |
| `pnl.audit.index` | `/pnl/audit` | Audit Logs |

---

## Future Enhancements (Backlog)

- [ ] File upload for invoices/contracts
- [ ] Cash flow projections view
- [ ] Budget vs Actual comparison charts
- [ ] Multi-currency support
- [ ] API endpoints for mobile app
- [ ] Dedicated settings page for default currency management

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
- `PnlExpenseCategory` - Categories with system/user split
- `PnlPayment` - Payments with email toggle
- `PnlVendor` - Vendor/artist contacts
- `PnlEvent` - Event management
- `PnlRevenue` - Ticket sales

### Key Controllers
- `SettingsController` - Settings management
- `ExpenseController` - Expense CRUD + PDF/Email
- `ExpenseCategoryController` - Category management with protection
- `RevenueController` - Revenue CRUD
- `PaymentController` - Payment tracking
- `VendorController` - Vendor management
- `DashboardController` - Dashboard stats with tips & walkthrough
