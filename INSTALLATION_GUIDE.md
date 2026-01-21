# P&L Module Installation Guide for TicketKart
## Version 2.1 - With Sidebar Integration & TicketKart Events

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

## Step 6: Add P&L Module to Sidebar Navigation

Edit your sidebar file (typically `resources/views/customer/sidemenu.blade.php` or similar) and add the following code:

### Option A: Dropdown Menu (Recommended - Full P&L Module Links)

```html
<!-- P&L Module Dropdown Menu -->
@php
    $pnlActive = request()->is('pnl/*') || request()->routeIs('pnl.*');
@endphp

<li class="menuLi {{ $pnlActive ? 'leftMenuActive' : '' }}">
    <a href="#pnlSubmenu" data-bs-toggle="collapse" class="d-flex align-items-center icon-color gap-2 {{ $pnlActive ? '' : 'collapsed' }}" aria-expanded="{{ $pnlActive ? 'true' : 'false' }}">
        <i class="fi fi-rr-chart-line icon-color menuIcon fs-5"></i>
        <span class="menuCon d-flex justify-content-between align-items-center gap-2 flex-grow-1">
            <span>P&L</span>
            <i class="fas fa-chevron-down small transition-icon"></i>
        </span>
    </a>
    <ul class="collapse submenu list-unstyled {{ $pnlActive ? 'show' : '' }}" id="pnlSubmenu">
        <li class="{{ request()->routeIs('pnl.dashboard') || request()->routeIs('pnl.dashboard.index') ? 'active' : '' }}">
            <a href="{{ route('pnl.dashboard') }}" class="d-flex align-items-center gap-2">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.events.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.events.index') }}" class="d-flex align-items-center gap-2">
                <i class="fas fa-calendar-alt"></i>
                <span>Events</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.vendors.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.vendors.index') }}" class="d-flex align-items-center gap-2">
                <i class="fas fa-users"></i>
                <span>Vendors & Artists</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.expenses.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.expenses.index') }}" class="d-flex align-items-center gap-2">
                <i class="fas fa-receipt"></i>
                <span>Expenses</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.revenues.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.revenues.index') }}" class="d-flex align-items-center gap-2">
                <i class="fas fa-pound-sign"></i>
                <span>Revenue</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.payments.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.payments.index') }}" class="d-flex align-items-center gap-2">
                <i class="fas fa-credit-card"></i>
                <span>Payments</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.categories.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.categories.index') }}" class="d-flex align-items-center gap-2">
                <i class="fas fa-tags"></i>
                <span>Categories</span>
            </a>
        </li>
        <li class="{{ request()->routeIs('pnl.settings.*') ? 'active' : '' }}">
            <a href="{{ route('pnl.settings.index') }}" class="d-flex align-items-center gap-2">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>
    </ul>
</li>
```

### Option B: Simple Flat Menu Links (Alternative)

If you prefer flat menu items without dropdown:

```html
<!-- P&L Dashboard -->
<li class="menuLi d-flex align-items-center {{ request()->routeIs('pnl.dashboard') ? 'leftMenuActive' : '' }}">
    <a href="{{ route('pnl.dashboard') }}" class="d-flex icon-color gap-2">
        <i class="fi fi-rr-chart-line icon-color menuIcon fs-5"></i>
        <span class="menuCon">P&L Dashboard</span>
    </a>
</li>

<!-- P&L Events -->
<li class="menuLi d-flex align-items-center {{ request()->routeIs('pnl.events.*') ? 'leftMenuActive' : '' }}">
    <a href="{{ route('pnl.events.index') }}" class="d-flex icon-color gap-2">
        <i class="fi fi-rr-calendar icon-color menuIcon fs-5"></i>
        <span class="menuCon">P&L Events</span>
    </a>
</li>

<!-- P&L Vendors -->
<li class="menuLi d-flex align-items-center {{ request()->routeIs('pnl.vendors.*') ? 'leftMenuActive' : '' }}">
    <a href="{{ route('pnl.vendors.index') }}" class="d-flex icon-color gap-2">
        <i class="fi fi-rr-users icon-color menuIcon fs-5"></i>
        <span class="menuCon">Vendors & Artists</span>
    </a>
</li>

<!-- P&L Expenses -->
<li class="menuLi d-flex align-items-center {{ request()->routeIs('pnl.expenses.*') ? 'leftMenuActive' : '' }}">
    <a href="{{ route('pnl.expenses.index') }}" class="d-flex icon-color gap-2">
        <i class="fi fi-rr-document icon-color menuIcon fs-5"></i>
        <span class="menuCon">Expenses</span>
    </a>
</li>

<!-- P&L Revenue -->
<li class="menuLi d-flex align-items-center {{ request()->routeIs('pnl.revenues.*') ? 'leftMenuActive' : '' }}">
    <a href="{{ route('pnl.revenues.index') }}" class="d-flex icon-color gap-2">
        <i class="fi fi-rr-sack-dollar icon-color menuIcon fs-5"></i>
        <span class="menuCon">Revenue</span>
    </a>
</li>

<!-- P&L Payments -->
<li class="menuLi d-flex align-items-center {{ request()->routeIs('pnl.payments.*') ? 'leftMenuActive' : '' }}">
    <a href="{{ route('pnl.payments.index') }}" class="d-flex icon-color gap-2">
        <i class="fi fi-rr-credit-card icon-color menuIcon fs-5"></i>
        <span class="menuCon">Payments</span>
    </a>
</li>

<!-- P&L Settings -->
<li class="menuLi d-flex align-items-center {{ request()->routeIs('pnl.settings.*') ? 'leftMenuActive' : '' }}">
    <a href="{{ route('pnl.settings.index') }}" class="d-flex icon-color gap-2">
        <i class="fi fi-rr-settings icon-color menuIcon fs-5"></i>
        <span class="menuCon">P&L Settings</span>
    </a>
</li>
```

### CSS for Submenu (Add to your stylesheet if not present)

```css
/* P&L Submenu Styles */
.submenu {
    padding-left: 25px;
    margin-top: 5px;
}

.submenu li {
    padding: 8px 15px;
    border-radius: 5px;
    margin: 2px 0;
}

.submenu li a {
    color: #666;
    text-decoration: none;
    font-size: 0.9rem;
}

.submenu li:hover,
.submenu li.active {
    background-color: rgba(220, 53, 69, 0.1);
}

.submenu li.active a,
.submenu li:hover a {
    color: #dc3545;
}

.transition-icon {
    transition: transform 0.3s ease;
}

[aria-expanded="true"] .transition-icon {
    transform: rotate(180deg);
}
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

## TicketKart Integration (Optional)

To link P&L events with your existing TicketKart events table:

### Option 1: Manual Link
When creating a P&L event, manually enter the same event name and date as your TicketKart event.

### Option 2: Database Link (Advanced)
Add a `ticketkart_event_id` column to link P&L events with your `events` table:

```sql
ALTER TABLE pnl_events ADD COLUMN ticketkart_event_id BIGINT UNSIGNED NULL AFTER user_id;
ALTER TABLE pnl_events ADD INDEX idx_pnl_events_tk_id (ticketkart_event_id);
```

Then modify `EventController@create` to fetch TicketKart events:
```php
// In EventController@create
$ticketkartEvents = \DB::table('events')
    ->where('user_id', auth()->id())
    ->orWhere('organiser_id', auth()->id())
    ->orderBy('created_at', 'desc')
    ->get();
```

### Option 3: Auto-Import Revenue from `eventtickets`
Create a command to import ticket sales:

```php
// app/Console/Commands/ImportTicketSales.php
// This would query eventtickets table and create PnlRevenue entries
```

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

### Dashboard Features (New in v2.1)
- **Collapsible Sections**: Click section headers to collapse/expand. State is saved in browser.
- **Pagination Controls**: Show 5/10/25 rows per table
- **Mobile-Friendly Charts**: Smaller, responsive chart sizes
- **Searchable Vendor Table**: Quick filter vendors

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
| `pnl.categories.index` | `/pnl/categories` | Expense Categories |
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
| Sidebar not showing | Clear view cache, check menu file path |

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
├── INSTALLATION_GUIDE.md
└── MIGRATION_GUIDE.md
```

---

## Version History

### v2.1 (Current)
- Collapsible dashboard sections with state persistence
- Pagination controls on all tables
- Fixed currency symbol (£ consistently)
- Expense edit form now matches create form
- Disabled editing for paid expenses
- Improved mobile responsiveness
- Smaller, cleaner charts

### v2.0
- Per-organiser VAT/tax system
- Invoice number format: INV-YYYYMM-XXX
- PDF invoice generation
- Email notifications
- Multi-tenancy with user_id scoping

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
