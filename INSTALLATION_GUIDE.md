# P&L Module Installation Guide for TicketKart

Follow these steps to add the P&L module to your Laravel project.

---

## Step 1: Install Required Packages

Open terminal in your project root and run:

```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

**Package Purposes:**
- `maatwebsite/excel` - Excel export functionality
- `barryvdh/laravel-dompdf` - PDF invoice generation

---

## Step 2: Copy Module Files

Copy these folders/files from this package to your project:

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

### Tables Created (9 total - all prefixed with `pnl_`):

| Table | Description |
|-------|-------------|
| `pnl_settings` | Per-user settings (VAT rate, invoice prefix, etc.) |
| `pnl_events` | Event details |
| `pnl_vendors` | Artists/DJs/Vendors |
| `pnl_expense_categories` | Expense categories |
| `pnl_expenses` | Individual expenses with tax info |
| `pnl_payments` | Payment tracking |
| `pnl_revenues` | Ticket sales |
| `pnl_attachments` | File uploads |
| `pnl_audit_logs` | Change history |

**Note:** All tables are prefixed with `pnl_` - they will NOT touch your existing tables.

---

## Step 6: Add Menu Links to Sidebar

Edit `resources/views/customer/sidemenu.blade.php` and add the P&L menu link.

### Option 1: Simple Link

Find a suitable location in the sidebar (e.g., after the Finance menu item) and add:

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

### Option 2: Dropdown Menu with Sub-items

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

## Step 10: Configure Your Settings

Visit: `your-domain.com/pnl/settings`

Configure:
- **Default Tax Rate** - Set your default VAT percentage (UK standard is 20%)
- **Invoice Prefix** - Customize your invoice number prefix
- **Company Details** - Add company info for invoice headers
- **Email Notifications** - Choose when to notify vendors

---

## Currency Configuration

The module uses **GBP (£)** as the default currency. All monetary values are displayed with the £ symbol.

If you need to change the currency:
1. Search and replace `£` in the view files (`resources/views/pnl/`)
2. Update the locale in the JavaScript functions (search for `en-GB`)

---

## Available Routes

### Main Pages
| Route | URL | Description |
|-------|-----|-------------|
| `pnl.dashboard` | `/pnl/dashboard` | Main P&L Dashboard |
| `pnl.settings.index` | `/pnl/settings` | Settings Page |

### Events
| Route | URL | Description |
|-------|-----|-------------|
| `pnl.events.index` | `/pnl/events` | Events List |
| `pnl.events.create` | `/pnl/events/create` | Create Event |
| `pnl.events.show` | `/pnl/events/{id}` | Event Details |

### Vendors & Artists
| Route | URL | Description |
|-------|-----|-------------|
| `pnl.vendors.index` | `/pnl/vendors` | Vendors List |
| `pnl.vendors.create` | `/pnl/vendors/create` | Add Vendor/Artist |
| `pnl.vendors.show` | `/pnl/vendors/{id}` | Vendor Details |

### Expenses
| Route | URL | Description |
|-------|-----|-------------|
| `pnl.expenses.index` | `/pnl/expenses` | Expenses List |
| `pnl.expenses.create` | `/pnl/expenses/create` | Add Expense |
| `pnl.expenses.show` | `/pnl/expenses/{id}` | Expense Details |
| `pnl.expenses.pdf` | `/pnl/expenses/{id}/pdf` | Download Invoice PDF |
| `pnl.expenses.email` | `POST /pnl/expenses/{id}/email` | Email Invoice to Vendor |

### Revenue
| Route | URL | Description |
|-------|-----|-------------|
| `pnl.revenues.index` | `/pnl/revenues` | Revenue List |
| `pnl.revenues.create` | `/pnl/revenues/create` | Add Revenue Entry |
| `pnl.revenues.edit` | `/pnl/revenues/{id}/edit` | Edit/Add More Tickets |

### Payments
| Route | URL | Description |
|-------|-----|-------------|
| `pnl.payments.index` | `/pnl/payments` | All Payments |
| `pnl.payments.upcoming` | `/pnl/payments/upcoming` | Upcoming Payments |
| `pnl.payments.overdue` | `/pnl/payments/overdue` | Overdue Payments |

### Categories
| Route | URL | Description |
|-------|-----|-------------|
| `pnl.categories.index` | `/pnl/categories` | Expense Categories |

---

## Invoice Number Format

Invoices use the format: `{PREFIX}-{YYYYMM}-{SEQUENCE}`

Examples:
- `INV-202501-001` (January 2025, first invoice)
- `INV-202501-002` (January 2025, second invoice)
- `TK-202502-001` (February 2025, with custom prefix "TK")

You can customize the prefix in Settings.

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
| PDF not generating | Ensure `barryvdh/laravel-dompdf` is installed |
| Email not sending | Check mail configuration in `.env` |

---

## Files Included

```
├── app/
│   ├── Console/Commands/SendPaymentReminders.php
│   ├── Exports/ (3 export classes)
│   ├── Http/Controllers/PnL/ (11 controllers)
│   ├── Mail/ (2 mailable classes)
│   ├── Models/PnL/ (9 models)
│   ├── Policies/ (policy classes)
│   ├── Providers/PnLServiceProvider.php
│   └── Traits/HasAuditLog.php
├── database/migrations/ (9 migrations)
├── resources/views/pnl/ (all blade views)
├── routes/pnl.php
├── SQL_TABLES.sql (⭐ Raw SQL - use this to avoid migration conflicts)
├── INSTALLATION_GUIDE.md (this file)
├── MIGRATION_GUIDE.md (for upgrading from previous versions)
└── README.md
```

---

## Upgrading from Previous Version?

If you already have an older version installed, see `MIGRATION_GUIDE.md` for upgrade instructions.

---

## Done!

Your P&L module is now integrated with TicketKart.

**Quick Links:**
- Dashboard: `/pnl/dashboard`
- Settings: `/pnl/settings`
- Vendors: `/pnl/vendors`
- Expenses: `/pnl/expenses`
