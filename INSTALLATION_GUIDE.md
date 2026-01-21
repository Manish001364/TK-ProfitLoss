# P&L Module Installation Guide for TicketKart
## Version 2.7 - Bug Fixes: Cash Flow Calculations & Expense Currency

Follow these steps to add the P&L module to your Laravel project.

---

## What's New in v2.7

- **CRITICAL FIX: Cash Flow Calculations** - Profit/Loss now correctly calculated from actual ticket sales revenue (not manual estimates)
- **Expense Currency Support** - Each expense can now store its own currency code
- **Combined Categories & Services Page** - Single page showing both Expense Categories and Vendor Service Types in two columns (cleaner sidebar navigation)

## What's in v2.6

- **Service Types Management** - 13 system defaults + custom vendor categories
- **Default Service Types**: Artist, DJ, Venue, Catering, Security, Equipment Hire, Marketing, Staff, Transport, Photography, Decor, MC/Host, Other
- **Custom Service Types**: Create your own vendor categories for unique needs
- **Improved Sidebar**: Configuration section with Expense Categories, Service Types, Settings

## What's in v2.5

- **International Phone Numbers** - Country flag dropdown with validation (intl-tel-input)
- **Address Enhancement** - Country & postcode fields with validation
- **Multi-Currency Support** - Set default currency, per-event currency, exchange rates
- **Cash Flow Projections** - New page for visualising upcoming payments & revenue
- **Expected Revenue** - Track projected income per event

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
| `app/Console/Commands/` | `your-project/app/Console/Commands/` |
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

### Tables Created (12 total):

| Table | Description |
|-------|-------------|
| `pnl_settings` | Per-user settings (VAT rate, invoice prefix, **currency**) |
| `pnl_currency_rates` | **User-defined exchange rates** (NEW) |
| `pnl_events` | Event details with **currency** field |
| `pnl_vendors` | Artists/DJs/Vendors with **phone country codes & address** |
| `pnl_expense_categories` | Legacy expense categories |
| `pnl_expense_categories_system` | System default categories (read-only) |
| `pnl_expense_categories_user` | User-created categories |
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

The P&L module includes its own sidebar navigation on every page:

- 📊 **Dashboard** - Overview of all P&L data
- 📈 **Cash Flow** - Projected payments & revenue (NEW v2.5)
- 📅 **Events** - Manage event P&L
- 👥 **Vendors & Artists** - Manage vendors, artists, DJs
- 🧾 **Expenses** - Track all expenses
- 💷 **Revenue** - Track ticket sales
- 💳 **Payments** - Payment tracking & status
- 🏷️ **Categories** - Expense categories
- ⚙️ **Settings** - Currency, VAT, invoice settings

The sidebar automatically highlights the current section and is mobile-responsive.

---

## Key Features in v2.5

### International Phone Numbers
Vendor phone fields now include:
- Country flag dropdown (200+ countries)
- Auto-detection of number format
- Real-time validation
- Stored as separate `phone_country_code` + national number

### Address Enhancement
- Country dropdown (UK, US, India, Germany, France, etc.)
- Postcode field with format hints per country:
  - UK: `SW1A 1AA`
  - US: `12345` or `12345-6789`
  - India: `110001`

### Multi-Currency Support
- **Default Currency**: Set in Settings (GBP, USD, EUR, INR, AUD, etc.)
- **Per-Event Currency**: Each event can have its own currency
- **Exchange Rates**: Define your own conversion rates in Settings
- Supported currencies: GBP, USD, EUR, INR, AUD, CAD, AED, SGD, CHF, JPY, CNY

### Cash Flow Projections
New dedicated page showing:
- Summary cards: Overdue, Due in 7/14/30/60/90 days
- Interactive chart: Inflows vs Outflows
- Upcoming payments list with urgency badges
- Expected revenue from upcoming events
- Net position calculation

---

## Files Structure

After installation, you should have:

```
your-project/
├── app/
│   ├── Console/Commands/
│   │   ├── SendPaymentReminders.php
│   │   └── ImportTicketKartRevenue.php
│   ├── Exports/ (3 files)
│   ├── Http/Controllers/PnL/ (11 controllers)
│   ├── Mail/ (2 files)
│   ├── Models/PnL/ (10 models including PnlCurrencyRate)
│   ├── Policies/ (6 files)
│   ├── Providers/PnLServiceProvider.php
│   └── Traits/HasAuditLog.php
├── resources/views/pnl/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── partials/
│   │   ├── sidebar.blade.php
│   │   ├── walkthrough.blade.php
│   │   └── tips.blade.php
│   ├── dashboard/
│   │   ├── index.blade.php
│   │   └── cashflow.blade.php (NEW)
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
| `pnl.dashboard.cashflow` | `/pnl/cashflow` | Cash Flow Projections (NEW) |
| `pnl.events.index` | `/pnl/events` | Events List |
| `pnl.vendors.index` | `/pnl/vendors` | Vendors List |
| `pnl.expenses.index` | `/pnl/expenses` | Expenses List |
| `pnl.revenues.index` | `/pnl/revenues` | Revenue List |
| `pnl.payments.index` | `/pnl/payments` | Payments List |
| `pnl.payments.upcoming` | `/pnl/payments/upcoming` | Upcoming Payments |
| `pnl.payments.overdue` | `/pnl/payments/overdue` | Overdue Payments |
| `pnl.categories.index` | `/pnl/categories` | Expense Categories |
| `pnl.settings.index` | `/pnl/settings` | Settings (Currency, VAT, Invoice) |
| `pnl.audit.index` | `/pnl/audit` | Audit Logs |

---

## TicketKart Integration (Recommended)

This section explains how to connect the P&L module with your existing TicketKart database tables (`events`, `eventtickets`, `orders`, etc.) so you can:

1. **View TicketKart events directly** in the P&L module
2. **Auto-import revenue** from ticket sales
3. **Link expenses** to your actual TicketKart events

### Step 1: Link Column Already Included

The `pnl_events` table already includes `ticketkart_event_id` column for linking.

### Step 2: Import Revenue Command

Use the included command to import ticket sales:

```bash
# Import all linked events
php artisan pnl:import-revenue

# Import specific event
php artisan pnl:import-revenue --event_id=abc-123-xyz
```

### Step 3: Schedule Auto-Import (Optional)

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Auto-sync revenue every hour
    $schedule->command('pnl:import-revenue')->hourly();
}
```

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
| Phone flags not showing | Check CDN access to intl-tel-input |
| Currency not saving | Run v2.5 migration from MIGRATION_GUIDE.md |

---

## Version History

### v2.5 (Current)
- **International phone numbers** with country flags (intl-tel-input)
- **Address enhancement** with country & postcode validation
- **Multi-currency support** with user-defined exchange rates
- **Cash Flow Projections** page
- **Expected revenue** field for events

### v2.4
- Dashboard walkthrough for new users
- Smart budget tips (UK English "Utilisation")
- Chart period filters (3/6/12 months, YTD)
- Expense categories split into system/user tables

### v2.3
- Built-in sidebar navigation on all P&L pages
- Self-contained P&L layout system
- Mobile-responsive sidebar

### v2.2
- TicketKart integration support
- Revenue import command

### v2.1
- Collapsible dashboard sections
- Pagination controls
- Currency symbol fixes

### v2.0
- Per-organiser VAT/tax system
- Invoice number format
- PDF invoice generation
- Email notifications

---

## Done!

Your P&L module is ready with all v2.5 features.

**Quick Start:**
1. Go to `/pnl/settings` - **Set your default currency and exchange rates**
2. Go to `/pnl/dashboard` - View your P&L summary
3. Go to `/pnl/cashflow` - **View cash flow projections**
4. Go to `/pnl/events` - Create your first event (with currency)
5. Go to `/pnl/vendors` - Add vendors/artists (with international phone)
6. Go to `/pnl/expenses` - Track expenses
7. Go to `/pnl/revenues` - Track ticket sales
