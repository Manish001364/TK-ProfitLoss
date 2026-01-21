# P&L Module Installation Guide for TicketKart
## Version 2.0 - Fresh Install

Follow these steps to add the P&L module to your Laravel project.

---

## Step 1: Install Required Packages

```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

**Package Purposes:**
- `maatwebsite/excel` - Excel export functionality
- `barryvdh/laravel-dompdf` - PDF invoice generation

---

## Step 2: Copy Module Files

Extract the zip and copy these folders/files to your Laravel project:

| Source | Destination |
|--------|-------------|
| `app/Models/PnL/` | `your-project/app/Models/PnL/` |
| `app/Http/Controllers/PnL/` | `your-project/app/Http/Controllers/PnL/` |
| `app/Exports/` | `your-project/app/Exports/` |
| `app/Mail/` | `your-project/app/Mail/` |
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

Copy the contents of `SQL_TABLES.sql` and run directly in your MySQL database.

**Via Command Line:**
```bash
mysql -u your_username -p your_database < SQL_TABLES.sql
```

**Via phpMyAdmin:**
1. Open phpMyAdmin
2. Select your database
3. Click "SQL" tab
4. Paste the contents of `SQL_TABLES.sql`
5. Click "Go"

### Tables Created (9 total):

| Table | Description |
|-------|-------------|
| `pnl_settings` | Per-user settings (VAT rate, invoice prefix) |
| `pnl_events` | Event details |
| `pnl_vendors` | Artists/DJs/Vendors |
| `pnl_expense_categories` | Expense categories |
| `pnl_expenses` | Individual expenses with tax info |
| `pnl_payments` | Payment tracking |
| `pnl_revenues` | Ticket sales |
| `pnl_attachments` | File uploads |
| `pnl_audit_logs` | Change history |

---

## Step 6: Add Menu Link to Sidebar

Edit `resources/views/customer/sidemenu.blade.php` and add the P&L menu:

### Simple Link:
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

### Dropdown Menu (Recommended):
```php
@php
    $pnlActive = request()->is('pnl/*') || request()->routeIs('pnl.*');
@endphp

<li class="menuLi d-flex align-items-center {{ $pnlActive ? 'leftMenuActive' : '' }}">
    <a href="#pnlSubmenu" data-bs-toggle="collapse" class="d-flex icon-color gap-2">
        <i class="{{ $pnlActive ? 'fi fi-sr-chart-line' : 'fi fi-rr-chart-line' }} icon-color menuIcon fs-5"></i>
        <span class="menuCon d-flex justify-content-between align-items-center gap-2">
            <span>P&L</span>
            <i class="fas fa-chevron-down small"></i>
        </span>
    </a>
    <ul class="collapse {{ $pnlActive ? 'show' : '' }}" id="pnlSubmenu">
        <li><a href="{{ route('pnl.dashboard') }}">Dashboard</a></li>
        <li><a href="{{ route('pnl.events.index') }}">Events</a></li>
        <li><a href="{{ route('pnl.vendors.index') }}">Vendors & Artists</a></li>
        <li><a href="{{ route('pnl.expenses.index') }}">Expenses</a></li>
        <li><a href="{{ route('pnl.revenues.index') }}">Revenue</a></li>
        <li><a href="{{ route('pnl.payments.index') }}">Payments</a></li>
        <li><a href="{{ route('pnl.settings.index') }}">Settings</a></li>
    </ul>
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

## Step 10: Configure Settings

Visit: `your-domain.com/pnl/settings`

Configure:
- **Default Tax Rate** - Set your VAT % (UK default: 20%)
- **Invoice Prefix** - Customize prefix (e.g., INV, TK)
- **Company Details** - For invoice headers
- **Email Notifications** - When to notify vendors

---

## Features

### Tax/VAT System
- Per-organiser default VAT rate
- Toggle taxable/non-taxable per expense
- Tax calculated automatically
- Shown on PDF invoices

### Invoice Numbers
- Format: `INV-YYYYMM-XXX` (e.g., INV-202501-001)
- Auto-generated
- Editable by user
- Configurable prefix

### Revenue Tracking
- Ticket sales by type
- Quick-add buttons for more tickets sold
- Automatic calculations:
  - Gross Revenue = Ticket Price × Tickets Sold
  - Net Revenue = Gross - Fees - Taxes
  - Final Revenue = Net - Refunds

### Email Notifications
- Send invoice to vendor
- Notify on payment status change
- Enable/disable per expense

---

## Available Routes

| Route | URL | Description |
|-------|-----|-------------|
| `pnl.dashboard` | `/pnl/dashboard` | Main Dashboard |
| `pnl.settings.index` | `/pnl/settings` | Settings |
| `pnl.events.index` | `/pnl/events` | Events List |
| `pnl.vendors.index` | `/pnl/vendors` | Vendors List |
| `pnl.expenses.index` | `/pnl/expenses` | Expenses List |
| `pnl.revenues.index` | `/pnl/revenues` | Revenue List |
| `pnl.payments.index` | `/pnl/payments` | Payments List |
| `pnl.payments.upcoming` | `/pnl/payments/upcoming` | Upcoming Payments |

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Class not found | `composer dump-autoload` |
| View not found | `php artisan view:clear` |
| Route not found | `php artisan route:clear` |
| Permission denied | `chmod -R 775 storage bootstrap/cache` |
| PDF not generating | Ensure `barryvdh/laravel-dompdf` installed |
| Email not sending | Check mail config in `.env` |

---

## Files Structure

```
├── app/
│   ├── Console/Commands/SendPaymentReminders.php
│   ├── Exports/ (3 export classes)
│   ├── Http/Controllers/PnL/ (11 controllers)
│   ├── Mail/ (2 mailable classes)
│   ├── Models/PnL/ (9 models)
│   ├── Policies/
│   ├── Providers/PnLServiceProvider.php
│   └── Traits/HasAuditLog.php
├── resources/views/pnl/ (all blade views)
├── routes/pnl.php
├── SQL_TABLES.sql
└── INSTALLATION_GUIDE.md
```

---

## Done!

Your P&L module is ready. 

**Quick Start:**
1. Go to `/pnl/settings` - Set your VAT rate
2. Go to `/pnl/events` - Create your first event
3. Go to `/pnl/vendors` - Add vendors/artists
4. Go to `/pnl/expenses` - Track expenses
5. Go to `/pnl/revenues` - Track ticket sales
6. Go to `/pnl/dashboard` - View your P&L summary
