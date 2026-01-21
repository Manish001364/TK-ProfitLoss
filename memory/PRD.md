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
- **Currency:** Multi-currency support (GBP default)
- **Layout System:** Custom organiser layout (`layouts.organiser_layout`)
- **Sidebar:** Custom (`customer/sidemenu.blade.php`)

---

## Core Features Implemented

### 1. Vendor/Artist Management ✅
- Comprehensive form with service type (DJ, Caterer, Artist, etc.)
- Only vendor name is mandatory - all other fields optional
- **International Phone Numbers** - intl-tel-input with country flags and validation (NEW v2.5)
- **Address Enhancement** - Country dropdown and postcode fields with country-specific validation (NEW v2.5)
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
- **Smart Tips & Insights with budget utilisation analysis** (v2.4)
- **First-time user walkthrough modal** (v2.4)
- **Chart period filters (3/6/12 months, YTD)** (v2.4)

### 7. Cash Flow Projections ✅ (NEW v2.5)
- Dedicated cash flow page accessible from sidebar
- Summary cards: Overdue, Due in 7/14/30 days
- Interactive chart showing projected inflows vs outflows
- Upcoming payments list with urgency indicators
- Expected revenue from upcoming events
- Net position calculation
- Period filter (30/60/90 days)

### 8. Multi-Currency Support ✅ (NEW v2.5)
- Default currency selection in settings (GBP, USD, EUR, INR, AUD, CAD, AED, SGD, CHF, JPY, CNY)
- Per-event currency selection
- **User-defined exchange rates** for currency conversion
- Exchange rate management in settings page
- Currency symbols displayed throughout

### 9. Per-Organiser Settings ✅
- **Default currency configuration** (NEW v2.5)
- **Exchange rates management** (NEW v2.5)
- **Default VAT/Tax rate configuration**
- **Invoice prefix customization**
- **Invoice sequence reset**
- **Company details for invoices**
- **Email notification preferences:**
  - On invoice created
  - On payment scheduled
  - On payment completed
- **Walkthrough dismissed flag**

### 10. Expense Categories - System & User Split ✅ (v2.4)
- **System categories (read-only defaults)**: Artist Fees, DJ Fees, Venue Hire, Catering, Security, Equipment Hire, Marketing, Staff, Transportation, Insurance, Licensing, Production, Other
- **User categories (editable)**: Users can create custom categories
- **Protected system defaults**: Cannot be edited or deleted by users

### 11. Sidebar Navigation ✅
- **Built-in P&L sidebar in all module pages**
- **Light-themed sidebar matching TicketKart style**
- **Cash Flow link added** (NEW v2.5)
- **Combined Categories & Services page** (NEW v2.7) - Single page with both Expense Categories and Service Types
- **All P&L links accessible from sidebar**
- **CSS styling included**

---

## Database Schema

### Tables (12 total - all prefixed with `pnl_`):

| Table | Purpose |
|-------|---------|
| `pnl_settings` | Per-user settings (VAT, invoice prefix, email prefs, currency) |
| `pnl_currency_rates` | User-defined exchange rates (NEW v2.5) |
| `pnl_events` | Event details, budget, dates, currency |
| `pnl_vendors` | Vendor/artist contact info, phone country codes, addresses |
| `pnl_expense_categories` | Legacy category definitions (backward compat) |
| `pnl_expense_categories_system` | System default categories |
| `pnl_expense_categories_user` | User-created categories |
| `pnl_expenses` | Individual expenses with tax info |
| `pnl_payments` | Payment tracking with email toggle |
| `pnl_revenues` | Ticket sales revenue |
| `pnl_attachments` | File attachments (polymorphic) |
| `pnl_audit_logs` | Change history |

---

## Session Changelog

### January 2025 - Version 2.7 (Current)
- **CRITICAL BUG FIX: Cash Flow Calculations** - Profit/Loss now correctly calculated from actual ticket sales revenue (`total_revenue` - `total_expenses`), NOT from manual `expected_revenue` estimates
- **Expense Currency Support** - Each expense now stores its own currency code, defaults to user's default currency
- **Combined Categories & Services Page** - Single page showing both Expense Categories and Service Types in two columns (cleaner UI, less sidebar clutter)
- **Database Schema Update** - Added `currency` column to `pnl_expenses` table

### January 2025 - Version 2.6
- **Service Types Management**: 13 system defaults + custom vendor service types
- **Default Service Types**: Artist, DJ, Venue, Catering, Security, Equipment Hire, Marketing, Staff, Transport, Photography, Decor, MC/Host, Other
- **Sidebar Update**: Configuration section with dividers for Categories, Service Types, Settings

### January 2025 - Version 2.5
- **International Phone Numbers**: intl-tel-input library with country flags and validation
- **Address Enhancement**: Country dropdown and postcode fields with country-specific validation
- **Multi-Currency Support**: Default currency setting, per-event currency, exchange rate management
- **Cash Flow Projections**: New dedicated page for visualising upcoming payments and expected revenue
- **Expected Revenue Field**: Added to events for cash flow calculations
- **Sidebar Update**: Added Cash Flow link

### January 2025 - Version 2.4
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

---

## Deliverables

1. **ticketkart-pnl-module.zip** - Complete module package (v2.7)
2. **SQL_TABLES.sql** - Raw SQL for direct database creation
3. **INSTALLATION_GUIDE.md** - Step-by-step installation instructions (v2.7)
4. **MIGRATION_GUIDE.md** - Upgrade instructions for existing users (v2.7 latest)
5. **README.md** - Module overview

---

## Key Routes

| Route | URL | Description |
|-------|-----|-------------|
| `pnl.dashboard` | `/pnl/dashboard` | Main Dashboard |
| `pnl.dashboard.cashflow` | `/pnl/cashflow` | Cash Flow Projections (NEW) |
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
- [ ] Budget vs Actual comparison charts
- [ ] API endpoints for mobile app

---

## Technical Dependencies

### Required Composer Packages
```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

### Frontend Libraries (CDN)
- **intl-tel-input v18.5.3** - International phone number input with flags
- **Chart.js** - Dashboard charts
- **Bootstrap 5** - UI framework

### Key Models
- `PnlSettings` - Per-user settings with currency
- `PnlCurrencyRate` - Exchange rates (NEW)
- `PnlExpense` - Expenses with tax fields
- `PnlExpenseCategory` - Categories with system/user split
- `PnlPayment` - Payments with email toggle
- `PnlVendor` - Vendor/artist contacts with phone country codes
- `PnlEvent` - Event management with currency
- `PnlRevenue` - Ticket sales

### Key Controllers
- `SettingsController` - Settings management with currency rates
- `DashboardController` - Dashboard stats, tips, walkthrough, cash flow
- `ExpenseController` - Expense CRUD + PDF/Email
- `ExpenseCategoryController` - Category management with protection
- `RevenueController` - Revenue CRUD
- `PaymentController` - Payment tracking
- `VendorController` - Vendor management with phone/address
- `EventController` - Event management with currency
