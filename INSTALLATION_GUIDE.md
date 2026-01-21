# P&L Module Installation Guide for TicketKart
## Version 2.2 - With Sidebar Integration & TicketKart Events

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
| `public/css/pnl-sidebar.css` | `your-project/public/css/pnl-sidebar.css` |

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

## Step 6: Add P&L Sidebar CSS

### 6.1 Copy the CSS File

Copy `public/css/pnl-sidebar.css` to your project:
```
your-project/public/css/pnl-sidebar.css
```

### 6.2 Include CSS in Your Layout

Open your main layout file (e.g., `resources/views/layouts/organiser_layout.blade.php`) and add this line in the `<head>` section:

```html
<head>
    <!-- ... existing CSS ... -->
    
    <!-- P&L Sidebar Styles -->
    <link rel="stylesheet" href="{{ asset('css/pnl-sidebar.css') }}">
</head>
```

---

## Step 7: Add P&L Menu to Sidebar

### 7.1 Find Your Sidebar File

Your sidebar is likely in one of these locations:
- `resources/views/customer/sidemenu.blade.php`
- `resources/views/layouts/sidebar.blade.php`
- `resources/views/partials/sidebar.blade.php`

### 7.2 Add the P&L Menu Code

Copy and paste this code into your sidebar file. Place it where you want the P&L menu to appear (e.g., after Events):

```html
{{-- P&L Module Menu --}}
@php
    $pnlActive = request()->is('pnl/*') || request()->routeIs('pnl.*');
@endphp

<li class="pnl-menu-item {{ $pnlActive ? 'active' : '' }}">
    <a href="#pnlSubmenu" data-bs-toggle="collapse" class="{{ $pnlActive ? '' : 'collapsed' }}" aria-expanded="{{ $pnlActive ? 'true' : 'false' }}">
        <i class="fas fa-chart-line pnl-menu-icon"></i>
        <span class="pnl-menu-text">P&L</span>
        <i class="fas fa-chevron-down pnl-chevron"></i>
    </a>
    
    <ul class="collapse pnl-submenu {{ $pnlActive ? 'show' : '' }}" id="pnlSubmenu">
        <li class="{{ request()->routeIs('pnl.dashboard') ? 'active' : '' }}">
            <a href="{{ route('pnl.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.events.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.events.index') }}">
                <i class="fas fa-calendar-alt"></i>
                <span>Events</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.vendors.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.vendors.index') }}">
                <i class="fas fa-users"></i>
                <span>Vendors & Artists</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.expenses.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.expenses.index') }}">
                <i class="fas fa-receipt"></i>
                <span>Expenses</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.revenues.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.revenues.index') }}">
                <i class="fas fa-pound-sign"></i>
                <span>Revenue</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.payments.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.payments.index') }}">
                <i class="fas fa-credit-card"></i>
                <span>Payments</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.categories.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.categories.index') }}">
                <i class="fas fa-tags"></i>
                <span>Categories</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.settings.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.settings.index') }}">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>
    </ul>
</li>
```

### 7.3 Alternative: Use Blade Include

Instead of copying the code, you can use a Blade include. 

Copy `resources/views/pnl/partials/sidebar-menu.blade.php` to your project, then add this line to your sidebar:

```php
@include('pnl.partials.sidebar-menu')
```

---

## Step 8: Setup Payment Reminders (Optional)

Edit `app/Console/Kernel.php`, add to the `schedule` method:

```php
protected function schedule(Schedule $schedule)
{
    // P&L Payment Reminders - runs daily at 9 AM
    $schedule->command('pnl:send-payment-reminders')->dailyAt('09:00');
}
```

---

## Step 9: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

---

## Step 10: Test

Visit: `your-domain.com/pnl/dashboard`

You should see the P&L Dashboard and the sidebar menu!

---

## Files Summary

After installation, you should have these P&L files:

```
your-project/
├── app/
│   ├── Console/Commands/SendPaymentReminders.php
│   ├── Exports/ (3 files)
│   ├── Http/Controllers/PnL/ (11 controllers)
│   ├── Mail/ (2 files)
│   ├── Models/PnL/ (9 models)
│   ├── Policies/ (5 files)
│   ├── Providers/PnLServiceProvider.php
│   └── Traits/HasAuditLog.php
├── public/
│   └── css/
│       └── pnl-sidebar.css  ← CSS for sidebar menu
├── resources/views/
│   └── pnl/
│       └── partials/
│           └── sidebar-menu.blade.php  ← Sidebar menu code
├── routes/
│   └── pnl.php
└── SQL_TABLES.sql
```

---

## Troubleshooting

### Sidebar not showing?

1. **Check CSS is loaded**: View page source and search for `pnl-sidebar.css`
2. **Check menu code**: Make sure the P&L menu code is inside your `<ul>` element
3. **Clear cache**: Run `php artisan view:clear`
4. **Check Bootstrap**: The menu uses Bootstrap's collapse. Ensure Bootstrap JS is loaded.

### Menu styling doesn't match?

The `pnl-sidebar.css` uses its own class names (`.pnl-menu-item`, `.pnl-submenu`) to avoid conflicts with your existing styles. You can customize the CSS file to match your theme.

### Other Issues

| Issue | Solution |
|-------|----------|
| Class not found | `composer dump-autoload` |
| View not found | `php artisan view:clear` |
| Route not found | `php artisan route:clear` |
| Permission denied | `chmod -R 775 storage bootstrap/cache` |
| PDF not generating | Ensure `barryvdh/laravel-dompdf` installed |
| Email not sending | Check mail config in `.env` |

---

## Version History

### v2.2 (Current)
- Separate CSS file for sidebar (`pnl-sidebar.css`)
- Sidebar menu partial blade file
- Clearer installation instructions

### v2.1
- Collapsible dashboard sections
- Pagination controls on all tables
- Fixed currency symbol (£)
- Expense edit form improvements

### v2.0
- Per-organiser VAT/tax system
- Invoice number format: INV-YYYYMM-XXX
- PDF invoice generation
- Email notifications

---

## Done!

Your P&L module is ready with a dedicated sidebar menu.

**Quick Start:**
1. Go to `/pnl/settings` - Set your VAT rate
2. Go to `/pnl/events` - Create your first event
3. Go to `/pnl/vendors` - Add vendors/artists
4. Go to `/pnl/expenses` - Track expenses
5. Go to `/pnl/revenues` - Track ticket sales
6. Go to `/pnl/dashboard` - View your P&L summary
