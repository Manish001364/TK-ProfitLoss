# TicketKart P&L Module for Laravel

A comprehensive Profit & Loss management module for event organisers. This module helps track artists, vendors, expenses, revenue, and provides a clean dashboard for financial visibility.

## Features

- 👤 **Artists/DJs/Vendors Management** - Full contact details, bank info, tax references
- 💸 **Payment Tracking** - Pending, Scheduled, Paid statuses with reminders
- 📧 **Automated Email Reminders** - Scheduled payment notifications
- 🧾 **Expense Categories** - Fixed/Variable categorization with budget limits
- 🎟 **Revenue Tracking** - Ticket sales breakdown by type
- 📊 **P&L Dashboard** - Charts, filters, profit/loss indicators
- 📤 **Export & Reports** - CSV, Excel, PDF exports
- 🧠 **Audit Trail** - Change history logging
- 📎 **Attachments** - Invoice/contract uploads

## Requirements

- PHP >= 8.1
- Laravel >= 10.x
- MySQL >= 5.7
- Composer

## Installation

### Step 1: Copy Module Files

Copy the following directories to your Laravel project:

```bash
# Copy migrations
cp -r database/migrations/* your-laravel-project/database/migrations/

# Copy models
cp -r app/Models/* your-laravel-project/app/Models/

# Copy controllers
cp -r app/Http/Controllers/PnL/* your-laravel-project/app/Http/Controllers/PnL/

# Copy views
cp -r resources/views/pnl/* your-laravel-project/resources/views/pnl/

# Copy mail classes
cp -r app/Mail/* your-laravel-project/app/Mail/

# Copy exports
cp -r app/Exports/* your-laravel-project/app/Exports/

# Copy commands
cp -r app/Console/Commands/* your-laravel-project/app/Console/Commands/
```

### Step 2: Install Required Packages

```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

### Step 3: Run Migrations

```bash
php artisan migrate
```

### Step 4: Add Routes

Add to your `routes/web.php`:

```php
require __DIR__.'/pnl.php';
```

Or copy the contents of `routes/pnl.php` to your web.php file.

### Step 5: Schedule Payment Reminders

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('pnl:send-payment-reminders')->dailyAt('09:00');
}
```

### Step 6: Publish AdminLTE Assets (if not already installed)

```bash
composer require jeroennoten/laravel-adminlte
php artisan adminlte:install
```

### Step 7: Configure Mail

Ensure your `.env` has mail configuration:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@ticketkart.com
MAIL_FROM_NAME="TicketKart"
```

## Usage

Access the P&L module at: `/pnl/dashboard`

## Database Schema

The module creates the following tables:

- `pnl_events` - Event details
- `pnl_vendors` - Artists/DJs/Vendors
- `pnl_expense_categories` - Expense categorization
- `pnl_expenses` - Individual expenses
- `pnl_payments` - Payment tracking
- `pnl_revenues` - Revenue/ticket sales
- `pnl_attachments` - File attachments
- `pnl_audit_logs` - Change history

## Support

For support, contact TicketKart technical team.

## License

Proprietary - TicketKart
