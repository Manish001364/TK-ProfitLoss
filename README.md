# P&L Module for TicketKart

A self-contained Profit & Loss tracking module for event management.

## Version 2.8 (January 2025)

### Features

- **Event P&L Tracking** - Track profit/loss per event
- **Expense Management** - Categorize expenses with tax/VAT support
- **Revenue Tracking** - Track ticket sales and other income
- **Vendor Management** - Manage artists, DJs, suppliers with international phone support
- **Payment Tracking** - Pending, scheduled, and paid payment statuses
- **PDF Invoicing** - Generate and email PDF invoices
- **Multi-Currency** - Support for GBP, USD, EUR, INR and more
- **Cash Flow Projections** - Visualize upcoming payments and revenue
- **First-Time Walkthrough** - Interactive guide for new users

### Installation

See `INSTALLATION_GUIDE.md` for complete setup instructions.

### Database

See `SQL_TABLES.sql` for database schema.
Run `MIGRATION_GUIDE.md` instructions if upgrading from previous version.

### File Structure

```
app/
├── Console/Commands/       # Artisan commands
├── Exports/               # Excel export classes
├── Http/Controllers/PnL/  # All P&L controllers
├── Mail/                  # Email templates
├── Models/PnL/           # Eloquent models
├── Policies/             # Authorization policies
├── Providers/            # Service provider
└── Traits/               # Shared traits

resources/views/pnl/
├── layouts/              # Module layout
├── partials/             # Sidebar, walkthrough, tips
├── dashboard/            # Dashboard views
├── events/               # Event CRUD views
├── vendors/              # Vendor CRUD views
├── expenses/             # Expense CRUD views
├── revenues/             # Revenue CRUD views
├── payments/             # Payment views
├── configuration/        # Categories & Services
├── settings/             # Settings page
└── emails/               # Email templates

routes/
└── pnl.php               # All P&L routes
```

### Routes

All routes are prefixed with `/pnl` and require authentication.

| Route | Description |
|-------|-------------|
| `/pnl` | Dashboard |
| `/pnl/cashflow` | Cash flow projections |
| `/pnl/events` | Events CRUD |
| `/pnl/vendors` | Vendors CRUD |
| `/pnl/expenses` | Expenses CRUD |
| `/pnl/revenues` | Revenue CRUD |
| `/pnl/payments` | Payments management |
| `/pnl/configuration` | Categories & Services |
| `/pnl/settings` | Module settings |

### Support

For issues or feature requests, contact TicketKart support.
