# How to Merge P&L Module into te-abc Project

## Step 1: Install Required Packages

Run in your te-abc project root:
```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

## Step 2: Copy These Folders/Files

From this repo, copy to your te-abc project:

| From (this repo) | To (te-abc) |
|------------------|-------------|
| `app/Models/PnL/` | `app/Models/PnL/` |
| `app/Http/Controllers/PnL/` | `app/Http/Controllers/PnL/` |
| `app/Mail/PaymentReminderMail.php` | `app/Mail/PaymentReminderMail.php` |
| `app/Exports/` | `app/Exports/` |
| `app/Policies/` | `app/Policies/` |
| `app/Providers/PnLServiceProvider.php` | `app/Providers/PnLServiceProvider.php` |
| `app/Traits/` | `app/Traits/` |
| `app/Console/Commands/SendPaymentReminders.php` | `app/Console/Commands/SendPaymentReminders.php` |
| `database/migrations/*` | `database/migrations/` |
| `resources/views/pnl/` | `resources/views/pnl/` |
| `routes/pnl.php` | `routes/pnl.php` |

## Step 3: Register Service Provider

Add to `config/app.php` in the `providers` array:
```php
App\Providers\PnLServiceProvider::class,
```

## Step 4: Include Routes

Add to `routes/web.php` at the bottom:
```php
// P&L Module
require __DIR__.'/pnl.php';
```

## Step 5: Run Migrations
```bash
php artisan migrate
```

## Step 6: Add AdminLTE Menu

Add to `config/adminlte.php` in the `menu` array:
```php
[
    'text' => 'P&L Management',
    'icon' => 'fas fa-chart-line',
    'submenu' => [
        ['text' => 'Dashboard', 'url' => 'pnl/dashboard', 'icon' => 'fas fa-tachometer-alt'],
        ['text' => 'Events', 'url' => 'pnl/events', 'icon' => 'fas fa-calendar-alt'],
        ['text' => 'Vendors', 'url' => 'pnl/vendors', 'icon' => 'fas fa-users'],
        ['text' => 'Expenses', 'url' => 'pnl/expenses', 'icon' => 'fas fa-receipt'],
        ['text' => 'Revenue', 'url' => 'pnl/revenues', 'icon' => 'fas fa-ticket-alt'],
        ['text' => 'Payments', 'url' => 'pnl/payments', 'icon' => 'fas fa-credit-card'],
        ['text' => 'Categories', 'url' => 'pnl/categories', 'icon' => 'fas fa-tags'],
    ],
],
```

## Step 7: Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## Step 8: Access
Visit: `your-domain.com/pnl/dashboard`
