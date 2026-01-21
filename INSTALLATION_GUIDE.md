# P&L Module Installation Guide for TicketKart

Follow these steps to add the P&L module to your Laravel project.

---

## Step 1: Install Required Packages

Open terminal in your project root and run:

```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

---

## Step 2: Copy Module Files

Copy these folders/files from this package to your project:

| Source | Destination |
|--------|-------------|
| `app/Models/PnL/` | `your-project/app/Models/PnL/` |
| `app/Http/Controllers/PnL/` | `your-project/app/Http/Controllers/PnL/` |
| `app/Exports/` | `your-project/app/Exports/` |
| `app/Mail/PaymentReminderMail.php` | `your-project/app/Mail/PaymentReminderMail.php` |
| `app/Policies/` | `your-project/app/Policies/` |
| `app/Providers/PnLServiceProvider.php` | `your-project/app/Providers/PnLServiceProvider.php` |
| `app/Traits/` | `your-project/app/Traits/` |
| `app/Console/Commands/SendPaymentReminders.php` | `your-project/app/Console/Commands/SendPaymentReminders.php` |
| `resources/views/pnl/` | `your-project/resources/views/pnl/` |
| `routes/pnl.php` | `your-project/routes/pnl.php` |

---

## Step 3: Register Service Provider

Edit `config/app.php`, find the `providers` array and add:

```php
'providers' => [
    // ... existing providers
    
    App\Providers\PnLServiceProvider::class,
],
```

---

## Step 4: Add Routes

Edit `routes/web.php`, add at the bottom:

```php
// P&L Module Routes
require __DIR__.'/pnl.php';
```

---

## Step 5: Create Database Tables

### Option A: Run Raw SQL (Recommended - No Migration Conflicts)

Copy the contents of `SQL_TABLES.sql` and run directly in your MySQL database (via phpMyAdmin, MySQL Workbench, or command line).

```bash
mysql -u your_username -p your_database < SQL_TABLES.sql
```

Or copy-paste the SQL directly into phpMyAdmin's SQL tab.

### Option B: Use Laravel Migrations

Copy `database/migrations/*` to your project's migrations folder, then run:

```bash
php artisan migrate
```

### Tables Created (8 total - all prefixed with `pnl_`):

| Table | Description |
|-------|-------------|
| `pnl_events` | Event details |
| `pnl_vendors` | Artists/DJs/Vendors |
| `pnl_expense_categories` | Expense categories |
| `pnl_expenses` | Individual expenses |
| `pnl_payments` | Payment tracking |
| `pnl_revenues` | Ticket sales |
| `pnl_attachments` | File uploads |
| `pnl_audit_logs` | Change history |

**Note:** All tables are prefixed with `pnl_` - they will NOT touch your existing tables.

---

## Step 6: Add Menu Link to Sidebar

Edit `resources/views/customer/sidemenu.blade.php` and add the P&L menu link.

Find a suitable location in the `<ul class="menuUl">` section (e.g., after the Finance menu item) and add:

```php
@php
    $pnlActive = request()->is('pnl/*') || request()->routeIs('pnl.*');
@endphp

<li class="menuLi d-flex align-items-center {{ $pnlActive ? 'leftMenuActive' : '' }}">
    <a href="{{ route('pnl.dashboard') }}" class="d-flex icon-color gap-2">
        <i class="{{ $pnlActive ? 'fi fi-sr-chart-line' : 'fi fi-rr-chart-line' }} icon-color menuIcon fs-5"
            class="tooltip-trigger" 
            @mouseenter="showTooltip($el.getAttribute('data-tooltip'))"
            @mouseleave="hideTooltip()" 
            data-tooltip="P&L"></i>
        <span class="menuCon d-flex justify-content-between align-items-center gap-2">
            <span>P&L</span>
        </span>
    </a>
</li>
```

**Simple version** (without tooltip):

```php
<li class="menuLi d-flex align-items-center {{ request()->is('pnl/*') ? 'leftMenuActive' : '' }}">
    <a href="{{ route('pnl.dashboard') }}" class="d-flex icon-color gap-2">
        <i class="{{ request()->is('pnl/*') ? 'fi fi-sr-chart-line' : 'fi fi-rr-chart-line' }} icon-color menuIcon fs-5"></i>
        <span class="menuCon d-flex justify-content-between align-items-center gap-2">
            <span>P&L Management</span>
        </span>
    </a>
</li>
```

---

## Step 7: Setup Payment Reminders (Optional)

Edit `app/Console/Kernel.php`, add to the `schedule` method:

```php
protected function schedule(Schedule $schedule)
{
    // P&L Payment Reminders - runs daily at 9 AM
    $schedule->command('pnl:send-payment-reminders')->dailyAt('09:00');
}
```

---

## Step 8: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

---

## Step 9: Test

Visit: `your-domain.com/pnl/dashboard`

You should see the P&L Dashboard!

---

## Currency Configuration

The module uses **GBP (£)** as the default currency. All monetary values are displayed with the £ symbol.

If you need to change the currency:
1. Search and replace `£` in the view files (`resources/views/pnl/`)
2. Update the locale in the JavaScript functions (search for `en-GB`)

---

## Available Routes

| Route | URL | Description |
|-------|-----|-------------|
| `pnl.dashboard` | `/pnl/dashboard` | Main P&L Dashboard |
| `pnl.events.index` | `/pnl/events` | Events List |
| `pnl.events.create` | `/pnl/events/create` | Create Event |
| `pnl.vendors.index` | `/pnl/vendors` | Vendors & Artists |
| `pnl.vendors.create` | `/pnl/vendors/create` | Add Vendor/Artist |
| `pnl.expenses.index` | `/pnl/expenses` | Expenses List |
| `pnl.revenues.index` | `/pnl/revenues` | Revenue Tracking |
| `pnl.payments.index` | `/pnl/payments` | Payment Tracking |
| `pnl.categories.index` | `/pnl/categories` | Expense Categories |

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Class not found | `composer dump-autoload` |
| View not found | `php artisan view:clear` |
| Route not found | `php artisan route:clear` |
| Permission denied | `chmod -R 775 storage bootstrap/cache` |
| Modal not working | Ensure Bootstrap 5 JS is loaded |
| Icons not showing | Ensure FontAwesome is loaded |

---

## Files Included

```
├── app/
│   ├── Console/Commands/SendPaymentReminders.php
│   ├── Exports/ (3 export classes)
│   ├── Http/Controllers/PnL/ (10 controllers)
│   ├── Mail/PaymentReminderMail.php
│   ├── Models/PnL/ (8 models)
│   ├── Policies/ (policy classes)
│   ├── Providers/PnLServiceProvider.php
│   └── Traits/HasAuditLog.php
├── database/migrations/ (8 migrations)
├── resources/views/pnl/ (all blade views)
├── routes/pnl.php
├── SQL_TABLES.sql (⭐ Raw SQL - use this to avoid migration conflicts)
├── INSTALLATION_GUIDE.md (this file)
└── README.md
```

---

## Done!

Your P&L module is now integrated with TicketKart.
