# P&L Module Installation Guide for TicketKart
## Version 2.3 - With Built-in Sidebar Navigation

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

## Step 6: Add P&L Link to Main Sidebar (Optional)

The P&L module has its own **built-in sidebar navigation** that appears on all P&L pages. However, you may want to add a single link to your main TicketKart sidebar for easy access.

Open your sidebar file (`resources/views/customer/sidemenu.blade.php`) and add:

```html
<!-- P&L Module Link -->
<li class="menuLi d-flex align-items-center {{ request()->is('pnl/*') ? 'leftMenuActive' : '' }}">
    <a href="{{ route('pnl.dashboard') }}" class="d-flex icon-color gap-2">
        <i class="fi fi-rr-chart-line icon-color menuIcon fs-5"></i>
        <span class="menuCon">P&L</span>
    </a>
</li>
```

**Note:** This is optional. Users can always access the P&L module directly at `/pnl/dashboard`.

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

You should see the P&L Dashboard with the built-in sidebar navigation!

---

## Built-in Sidebar Navigation

**NEW in v2.3:** The P&L module now includes its own sidebar navigation on every page:

- 📊 **Dashboard** - Overview of all P&L data
- 📅 **Events** - Manage event P&L
- 👥 **Vendors & Artists** - Manage vendors, artists, DJs
- 🧾 **Expenses** - Track all expenses
- 💷 **Revenue** - Track ticket sales
- 💳 **Payments** - Payment tracking & status
- 🏷️ **Categories** - Expense categories
- ⚙️ **Settings** - VAT, invoice settings

The sidebar automatically highlights the current section and is mobile-responsive.

---

## Files Structure

After installation, you should have:

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
├── resources/views/pnl/
│   ├── layouts/
│   │   └── app.blade.php          ← P&L layout with sidebar
│   ├── partials/
│   │   └── sidebar.blade.php      ← Built-in sidebar navigation
│   ├── dashboard/
│   ├── events/
│   ├── vendors/
│   ├── expenses/
│   ├── revenues/
│   ├── payments/
│   ├── categories/
│   ├── settings/
│   ├── audit/
│   ├── exports/
│   └── emails/
└── routes/pnl.php
```

---

## Available Routes

| Route | URL | Description |
|-------|-----|-------------|
| `pnl.dashboard` | `/pnl/dashboard` | Main Dashboard |
| `pnl.events.index` | `/pnl/events` | Events List |
| `pnl.vendors.index` | `/pnl/vendors` | Vendors List |
| `pnl.expenses.index` | `/pnl/expenses` | Expenses List |
| `pnl.revenues.index` | `/pnl/revenues` | Revenue List |
| `pnl.payments.index` | `/pnl/payments` | Payments List |
| `pnl.payments.upcoming` | `/pnl/payments/upcoming` | Upcoming Payments |
| `pnl.payments.overdue` | `/pnl/payments/overdue` | Overdue Payments |
| `pnl.categories.index` | `/pnl/categories` | Expense Categories |
| `pnl.settings.index` | `/pnl/settings` | Settings |
| `pnl.audit.index` | `/pnl/audit` | Audit Logs |

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
| Sidebar not showing | Ensure `pnl/layouts/app.blade.php` and `pnl/partials/sidebar.blade.php` are copied |

---

## Version History

### v2.3 (Current)
- **Built-in sidebar navigation** on all P&L pages
- No need to manually add sidebar code to main TicketKart menu
- Self-contained P&L layout system
- Mobile-responsive sidebar

### v2.2
- Separate CSS file for sidebar
- Sidebar menu partial blade file

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

Your P&L module is ready with built-in navigation.

**Quick Start:**
1. Go to `/pnl/dashboard` - View your P&L summary
2. Go to `/pnl/settings` - Set your VAT rate
3. Go to `/pnl/events` - Create your first event
4. Go to `/pnl/vendors` - Add vendors/artists
5. Go to `/pnl/expenses` - Track expenses
6. Go to `/pnl/revenues` - Track ticket sales
