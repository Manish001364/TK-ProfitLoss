# TicketKart P&L Module

Profit & Loss management module for event organisers. Track artists, vendors, expenses, revenue, and get complete financial visibility for your events.

## Features

- **Vendors & Artists Management** - Full contact details, bank info, emergency contacts, tax details
- **Event Management** - Create events with budgets, track P&L per event
- **Expense Tracking** - Categorized expenses (Artist Fee, Venue, Marketing, etc.)
- **Revenue Tracking** - Ticket sales by type (Regular, VIP, Early Bird)
- **Payment Management** - Track Pending/Scheduled/Paid status
- **Payment Reminders** - Automated email reminders before due dates
- **P&L Dashboard** - Visual charts, profit/loss indicators, cash flow view
- **Export Reports** - CSV, Excel, PDF exports
- **Audit Trail** - Track all changes with history

## Requirements

- Laravel 10.x
- PHP 8.1+
- MySQL 5.7+
- AdminLTE (already installed in te-abc)

## Quick Installation

See `INSTALLATION_GUIDE.md` for detailed steps.

## File Structure

```
app/
├── Console/Commands/SendPaymentReminders.php
├── Exports/
│   ├── EventPnlExport.php
│   ├── PnlSummaryExport.php
│   └── VendorsExport.php
├── Http/Controllers/PnL/
│   ├── AttachmentController.php
│   ├── AuditLogController.php
│   ├── DashboardController.php
│   ├── EventController.php
│   ├── ExpenseCategoryController.php
│   ├── ExpenseController.php
│   ├── ExportController.php
│   ├── PaymentController.php
│   ├── RevenueController.php
│   └── VendorController.php
├── Mail/PaymentReminderMail.php
├── Models/PnL/
│   ├── PnlAttachment.php
│   ├── PnlAuditLog.php
│   ├── PnlEvent.php
│   ├── PnlExpense.php
│   ├── PnlExpenseCategory.php
│   ├── PnlPayment.php
│   ├── PnlRevenue.php
│   └── PnlVendor.php
├── Policies/
├── Providers/PnLServiceProvider.php
└── Traits/HasAuditLog.php

database/migrations/
├── 2024_01_01_000001_create_pnl_events_table.php
├── 2024_01_01_000002_create_pnl_vendors_table.php
├── 2024_01_01_000003_create_pnl_expense_categories_table.php
├── 2024_01_01_000004_create_pnl_expenses_table.php
├── 2024_01_01_000005_create_pnl_payments_table.php
├── 2024_01_01_000006_create_pnl_revenues_table.php
├── 2024_01_01_000007_create_pnl_attachments_table.php
└── 2024_01_01_000008_create_pnl_audit_logs_table.php

resources/views/pnl/
├── audit/
├── categories/
├── dashboard/
├── emails/
├── events/
├── expenses/
├── exports/
├── payments/
├── revenues/
└── vendors/

routes/pnl.php
```

## Database Tables

| Table | Description |
|-------|-------------|
| pnl_events | Event details with budget |
| pnl_vendors | Artists, DJs, vendors with all contact/bank details |
| pnl_expense_categories | Expense categories (fixed/variable) |
| pnl_expenses | Individual expenses linked to events |
| pnl_payments | Payment tracking with reminders |
| pnl_revenues | Ticket sales and revenue breakdown |
| pnl_attachments | File uploads (invoices, contracts) |
| pnl_audit_logs | Change history |

## Access URLs

After installation, access at:
- Dashboard: `/pnl/dashboard`
- Events: `/pnl/events`
- Vendors: `/pnl/vendors`
- Expenses: `/pnl/expenses`
- Revenue: `/pnl/revenues`
- Payments: `/pnl/payments`
- Categories: `/pnl/categories`

## Author

TicketKart Team
