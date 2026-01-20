# P&L Module Installation Guide for te-abc

Follow these steps to add the P&L module to your te-abc Laravel project.

---

## Step 1: Install Required Packages

Open terminal in your `te-abc` project root and run:

```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

---

## Step 2: Copy Module Files

Copy these folders/files from this repo to your te-abc project:

### Models
```
app/Models/PnL/  →  te-abc/app/Models/PnL/
```

### Controllers
```
app/Http/Controllers/PnL/  →  te-abc/app/Http/Controllers/PnL/
```

### Exports
```
app/Exports/  →  te-abc/app/Exports/
```

### Mail
```
app/Mail/PaymentReminderMail.php  →  te-abc/app/Mail/PaymentReminderMail.php
```

### Policies
```
app/Policies/  →  te-abc/app/Policies/
```

### Service Provider
```
app/Providers/PnLServiceProvider.php  →  te-abc/app/Providers/PnLServiceProvider.php
```

### Traits
```
app/Traits/  →  te-abc/app/Traits/
```

### Console Commands
```
app/Console/Commands/SendPaymentReminders.php  →  te-abc/app/Console/Commands/SendPaymentReminders.php
```

### Migrations
```
database/migrations/*  →  te-abc/database/migrations/
```

### Views
```
resources/views/pnl/  →  te-abc/resources/views/pnl/
```

### Routes
```
routes/pnl.php  →  te-abc/routes/pnl.php
```

---

## Step 3: Register Service Provider

Edit `config/app.php` in te-abc, find the `providers` array and add:

```php
'providers' => [
    // ... existing providers
    
    App\Providers\PnLServiceProvider::class,
],
```

---

## Step 4: Add Routes

Edit `routes/web.php` in te-abc, add at the bottom:

```php
// P&L Module Routes
require __DIR__.'/pnl.php';
```

---

## Step 5: Run Migrations (SAFE - Creates NEW Tables Only)

All P&L tables are prefixed with `pnl_` so they will NOT touch your existing tables.

### Check what migrations will run (preview only):
```bash
php artisan migrate:status
```

### Run only the P&L migrations:
```bash
php artisan migrate
```

### New tables created (8 total):

| Table Name | Description | Touches Existing? |
|------------|-------------|-------------------|
| `pnl_events` | Event details | ❌ NO |
| `pnl_vendors` | Artists/DJs/Vendors | ❌ NO |
| `pnl_expense_categories` | Expense categories | ❌ NO |
| `pnl_expenses` | Individual expenses | ❌ NO |
| `pnl_payments` | Payment tracking | ❌ NO |
| `pnl_revenues` | Ticket sales | ❌ NO |
| `pnl_attachments` | File uploads | ❌ NO |
| `pnl_audit_logs` | Change history | ❌ NO |

**Note:** These migrations only CREATE new tables. They do NOT modify any existing tables in your database.

### If you want to rollback P&L tables only:
```bash
php artisan migrate:rollback --step=8
```

### Verify tables created:
```bash
php artisan tinker
>>> Schema::hasTable('pnl_events')
=> true
```

---

## Step 6: Add Menu to AdminLTE Sidebar

Edit `config/adminlte.php`, find the `'menu'` array and add:

```php
[
    'text' => 'P&L Management',
    'icon' => 'fas fa-chart-line',
    'submenu' => [
        [
            'text' => 'Dashboard',
            'url'  => 'pnl/dashboard',
            'icon' => 'fas fa-tachometer-alt',
        ],
        [
            'text' => 'Events',
            'url'  => 'pnl/events',
            'icon' => 'fas fa-calendar-alt',
        ],
        [
            'text' => 'Vendors & Artists',
            'url'  => 'pnl/vendors',
            'icon' => 'fas fa-users',
        ],
        [
            'text' => 'Expenses',
            'url'  => 'pnl/expenses',
            'icon' => 'fas fa-receipt',
        ],
        [
            'text' => 'Revenue',
            'url'  => 'pnl/revenues',
            'icon' => 'fas fa-ticket-alt',
        ],
        [
            'text' => 'Payments',
            'url'  => 'pnl/payments',
            'icon' => 'fas fa-credit-card',
        ],
        [
            'text' => 'Categories',
            'url'  => 'pnl/categories',
            'icon' => 'fas fa-tags',
        ],
    ],
],
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
```

---

## Step 9: Test

Visit: `your-domain.com/pnl/dashboard`

You should see the P&L Dashboard!

---

## Troubleshooting

### Class not found error
```bash
composer dump-autoload
```

### View not found error
```bash
php artisan view:clear
```

### Route not found error
```bash
php artisan route:clear
```

### Permission denied error
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

## Done!

Your P&L module is now integrated with te-abc.
