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

### Models
```
app/Models/PnL/  →  your-project/app/Models/PnL/
```

### Controllers
```
app/Http/Controllers/PnL/  →  your-project/app/Http/Controllers/PnL/
```

### Exports
```
app/Exports/  →  your-project/app/Exports/
```

### Mail
```
app/Mail/PaymentReminderMail.php  →  your-project/app/Mail/PaymentReminderMail.php
```

### Policies
```
app/Policies/  →  your-project/app/Policies/
```

### Service Provider
```
app/Providers/PnLServiceProvider.php  →  your-project/app/Providers/PnLServiceProvider.php
```

### Traits
```
app/Traits/  →  your-project/app/Traits/
```

### Console Commands
```
app/Console/Commands/SendPaymentReminders.php  →  your-project/app/Console/Commands/SendPaymentReminders.php
```

### Migrations
```
database/migrations/*  →  your-project/database/migrations/
```

### Views
```
resources/views/pnl/  →  your-project/resources/views/pnl/
```

### Routes
```
routes/pnl.php  →  your-project/routes/pnl.php
```

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

**Alternative simple version** (if you don't want the tooltip):

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

### For Mobile Menu (Optional)

If you want to add P&L to the mobile bottom menu, find the mobile section in `sidemenu.blade.php` and add it to the dropdown menu:

```php
<li>
    <a href="{{ route('pnl.dashboard') }}"
        class="d-flex gap-2 align-items-center dropdown-item py-2 {{ request()->is('pnl/*') ? 'leftMenuActive' : '' }}">
        <i class="{{ request()->is('pnl/*') ? 'fi fi-sr-chart-line' : 'fi fi-rr-chart-line' }} fs-5 menuIcon icon-color"></i>
        <span style="color: {{ request()->is('pnl/*') ? '#FD0404' : '#8d8d8d' }};">P&L</span>
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
```

---

## Step 9: Test

Visit: `your-domain.com/pnl/dashboard`

You should see the P&L Dashboard!

---

## Layout Information

The P&L module views are configured to use your existing organiser layout:
- **Layout file:** `resources/views/layouts/organiser_layout.blade.php`
- **Content section:** `@yield('content')`
- **Custom JS section:** `@yield('customjs')`

The module uses Bootstrap 5 classes and integrates with your existing jQuery and Select2 setup.

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

### Modal not working
Make sure Bootstrap 5 JS is loaded. The P&L views use `bootstrap.Modal` for confirmation dialogs.

### Icons not showing
The P&L module uses FontAwesome icons (`fas fa-*`). Make sure FontAwesome is included in your layout.

---

## Available Routes

| Route | URL | Description |
|-------|-----|-------------|
| `pnl.dashboard` | `/pnl/dashboard` | Main P&L Dashboard |
| `pnl.events.index` | `/pnl/events` | Events List |
| `pnl.vendors.index` | `/pnl/vendors` | Vendors & Artists |
| `pnl.expenses.index` | `/pnl/expenses` | Expenses List |
| `pnl.revenues.index` | `/pnl/revenues` | Revenue Tracking |
| `pnl.payments.index` | `/pnl/payments` | Payment Tracking |
| `pnl.categories.index` | `/pnl/categories` | Expense Categories |

---

## Done!

Your P&L module is now integrated with your TicketKart project.
