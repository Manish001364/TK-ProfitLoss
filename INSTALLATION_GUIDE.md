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

## TicketKart Integration (Recommended)

This section explains how to connect the P&L module with your existing TicketKart database tables (`events`, `eventtickets`, `orders`, etc.) so you can:

1. **View TicketKart events directly** in the P&L module (no need to recreate events)
2. **Auto-import revenue** from ticket sales in the `orders` table
3. **Link expenses** to your actual TicketKart events

### Step 1: Add TicketKart Event Link Column

Run this SQL to add a column linking P&L events to your main TicketKart events:

```sql
-- Add column to link pnl_events with main TicketKart events table
ALTER TABLE `pnl_events` 
    ADD COLUMN `ticketkart_event_id` BIGINT UNSIGNED NULL AFTER `user_id`,
    ADD INDEX `idx_pnl_events_tk_event` (`ticketkart_event_id`);
```

### Step 2: Fetch TicketKart Events in Controller

Update `app/Http/Controllers/PnL/EventController.php` to fetch events from your TicketKart `events` table:

```php
<?php
// In EventController.php

public function create()
{
    // Fetch user's events from main TicketKart events table
    $ticketkartEvents = \DB::table('events')
        ->where(function($query) {
            $query->where('user_id', auth()->id())
                  ->orWhere('organiser_id', auth()->id());
        })
        ->whereNull('deleted_at')
        ->orderBy('start_date', 'desc')
        ->get(['id', 'name', 'start_date', 'venue']);
    
    return view('pnl.events.create', compact('ticketkartEvents'));
}

public function store(Request $request)
{
    $validated = $request->validate([
        'ticketkart_event_id' => 'nullable|exists:events,id',
        'name' => 'required_without:ticketkart_event_id|string|max:255',
        'event_date' => 'required|date',
        // ... other validation rules
    ]);

    // If linking to TicketKart event, auto-fill details
    if ($request->ticketkart_event_id) {
        $tkEvent = \DB::table('events')->find($request->ticketkart_event_id);
        if ($tkEvent) {
            $validated['name'] = $tkEvent->name;
            $validated['venue'] = $tkEvent->venue ?? null;
            $validated['event_date'] = $tkEvent->start_date;
            $validated['ticketkart_event_id'] = $tkEvent->id;
        }
    }

    $validated['user_id'] = auth()->id();
    $event = PnlEvent::create($validated);

    return redirect()->route('pnl.events.show', $event)
        ->with('success', 'Event created successfully!');
}
```

### Step 3: Update Create Event View

Update `resources/views/pnl/events/create.blade.php` to show TicketKart events dropdown:

```html
<!-- Add this at the top of the form -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-primary text-white border-0 py-3">
        <h6 class="mb-0"><i class="fas fa-link me-2"></i>Link to TicketKart Event</h6>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label class="form-label small">Select TicketKart Event (Optional)</label>
            <select name="ticketkart_event_id" id="ticketkart_event_id" class="form-select">
                <option value="">-- Create new P&L event manually --</option>
                @foreach($ticketkartEvents as $tkEvent)
                    <option value="{{ $tkEvent->id }}" 
                            data-name="{{ $tkEvent->name }}"
                            data-date="{{ $tkEvent->start_date }}"
                            data-venue="{{ $tkEvent->venue }}">
                        {{ $tkEvent->name }} ({{ \Carbon\Carbon::parse($tkEvent->start_date)->format('d M Y') }})
                    </option>
                @endforeach
            </select>
            <small class="text-muted">Select an existing TicketKart event to auto-fill details and link revenue</small>
        </div>
    </div>
</div>

<script>
// Auto-fill form when TicketKart event is selected
document.getElementById('ticketkart_event_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (selected.value) {
        document.querySelector('[name="name"]').value = selected.dataset.name || '';
        document.querySelector('[name="venue"]').value = selected.dataset.venue || '';
        document.querySelector('[name="event_date"]').value = selected.dataset.date ? selected.dataset.date.split(' ')[0] : '';
    }
});
</script>
```

### Step 4: Auto-Import Revenue from Orders

Create a command to import ticket sales revenue from your `orders` table:

**File: `app/Console/Commands/ImportTicketKartRevenue.php`**

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PnL\PnlEvent;
use App\Models\PnL\PnlRevenue;
use Illuminate\Support\Facades\DB;

class ImportTicketKartRevenue extends Command
{
    protected $signature = 'pnl:import-revenue {--event_id= : Specific PnL event ID}';
    protected $description = 'Import ticket sales revenue from TicketKart orders table';

    public function handle()
    {
        $query = PnlEvent::whereNotNull('ticketkart_event_id');
        
        if ($eventId = $this->option('event_id')) {
            $query->where('id', $eventId);
        }

        $events = $query->get();

        foreach ($events as $pnlEvent) {
            $this->importRevenueForEvent($pnlEvent);
        }

        $this->info('Revenue import completed!');
    }

    private function importRevenueForEvent(PnlEvent $pnlEvent)
    {
        // Get ticket sales from orders/eventtickets tables
        // Adjust table/column names to match your TicketKart schema
        $ticketSales = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.event_id', $pnlEvent->ticketkart_event_id)
            ->where('orders.status', 'completed') // Only completed orders
            ->whereNull('orders.deleted_at')
            ->select(
                'order_items.ticket_id',
                DB::raw('SUM(order_items.quantity) as tickets_sold'),
                DB::raw('SUM(order_items.unit_price * order_items.quantity) as gross_revenue'),
                DB::raw('SUM(order_items.booking_fee) as total_fees')
            )
            ->groupBy('order_items.ticket_id')
            ->get();

        foreach ($ticketSales as $sale) {
            // Get ticket details
            $ticket = DB::table('tickets')->find($sale->ticket_id);
            
            // Check if revenue entry already exists
            $existingRevenue = PnlRevenue::where('event_id', $pnlEvent->id)
                ->where('ticket_type', $ticket->name ?? 'General Admission')
                ->first();

            if ($existingRevenue) {
                // Update existing
                $existingRevenue->update([
                    'tickets_sold' => $sale->tickets_sold,
                    'gross_revenue' => $sale->gross_revenue,
                    'platform_fee' => $sale->total_fees,
                ]);
            } else {
                // Create new revenue entry
                PnlRevenue::create([
                    'event_id' => $pnlEvent->id,
                    'user_id' => $pnlEvent->user_id,
                    'ticket_type' => $ticket->name ?? 'General Admission',
                    'ticket_price' => $ticket->price ?? 0,
                    'tickets_sold' => $sale->tickets_sold,
                    'gross_revenue' => $sale->gross_revenue,
                    'platform_fee' => $sale->total_fees,
                    'gateway_fee' => 0,
                    'tax_amount' => 0,
                    'refunds' => 0,
                ]);
            }
        }

        $this->info("Imported revenue for: {$pnlEvent->name}");
    }
}
```

### Step 5: Register the Command

Add to `app/Console/Kernel.php`:

```php
protected $commands = [
    // ... other commands
    \App\Console\Commands\ImportTicketKartRevenue::class,
];

protected function schedule(Schedule $schedule)
{
    // Auto-sync revenue every hour
    $schedule->command('pnl:import-revenue')->hourly();
}
```

### Step 6: Run Revenue Import

**Manual import:**
```bash
php artisan pnl:import-revenue
```

**Import for specific event:**
```bash
php artisan pnl:import-revenue --event_id=abc-123-xyz
```

---

### TicketKart Database Reference

Based on your TicketKart schema, here are the key tables and columns:

| Table | Key Columns | Use in P&L |
|-------|-------------|------------|
| `events` | `id`, `name`, `start_date`, `venue`, `user_id`, `organiser_id` | Link P&L events |
| `tickets` | `id`, `event_id`, `name`, `price` | Ticket types |
| `eventtickets` | `id`, `event_id`, `ticket_id`, `booking_id` | Individual ticket sales |
| `orders` | `id`, `user_id`, `total_amount`, `status` | Revenue data |
| `order_items` | `order_id`, `event_id`, `ticket_id`, `quantity`, `unit_price` | Line items |

**Note:** Adjust the column names in the code above to match your exact TicketKart database schema.

---

### Quick Integration Checklist

- [ ] Run SQL to add `ticketkart_event_id` column
- [ ] Update `EventController.php` to fetch TicketKart events
- [ ] Update `events/create.blade.php` with event selector
- [ ] Create `ImportTicketKartRevenue.php` command
- [ ] Register command in Kernel.php
- [ ] Run `php artisan pnl:import-revenue` to test
- [ ] Optionally schedule hourly sync

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
| Revenue not importing | Check table/column names match your TicketKart schema |

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
