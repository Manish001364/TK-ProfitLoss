# TicketKart P&L Module - Installation Guide

## Prerequisites ✅
- Laravel 10.48.12 ✓
- AdminLTE installed ✓
- Authentication setup ✓
- MySQL database ✓

---

## Step 1: Install Required Packages

Open terminal in your Laravel project root and run:

```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

---

## Step 2: Copy Module Files

Download/copy these folders from the P&L module to your Laravel project:

### 2.1 Copy Models
```bash
# Copy to: app/Models/PnL/
# Files to copy:
# - PnlEvent.php
# - PnlVendor.php
# - PnlExpenseCategory.php
# - PnlExpense.php
# - PnlPayment.php
# - PnlRevenue.php
# - PnlAttachment.php
# - PnlAuditLog.php
```

### 2.2 Copy Controllers
```bash
# Copy to: app/Http/Controllers/PnL/
# Files to copy:
# - DashboardController.php
# - EventController.php
# - VendorController.php
# - ExpenseController.php
# - ExpenseCategoryController.php
# - PaymentController.php
# - RevenueController.php
# - AttachmentController.php
# - ExportController.php
# - AuditLogController.php
```

### 2.3 Copy Other Files
```bash
# Copy to: app/Mail/
# - PaymentReminderMail.php

# Copy to: app/Exports/
# - VendorsExport.php
# - PnlSummaryExport.php
# - EventPnlExport.php

# Copy to: app/Policies/
# - PnlEventPolicy.php
# - PnlVendorPolicy.php

# Copy to: app/Providers/
# - PnLServiceProvider.php

# Copy to: app/Traits/
# - HasAuditLog.php

# Copy to: app/Console/Commands/
# - SendPaymentReminders.php
```

### 2.4 Copy Migrations
```bash
# Copy to: database/migrations/
# All 8 migration files (2024_01_01_000001 to 2024_01_01_000008)
```

### 2.5 Copy Views
```bash
# Copy to: resources/views/pnl/
# The entire pnl folder with all blade templates
```

### 2.6 Copy Routes
```bash
# Copy: routes/pnl.php
```

---

## Step 3: Register Service Provider

Edit `config/app.php`, add to the `providers` array:

```php
'providers' => [
    // ... other providers
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

## Step 5: Run Migrations

```bash
php artisan migrate
```

This creates 8 new tables:
- pnl_events
- pnl_vendors
- pnl_expense_categories
- pnl_expenses
- pnl_payments
- pnl_revenues
- pnl_attachments
- pnl_audit_logs

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
        [
            'text' => 'Audit Log',
            'url'  => 'pnl/audit',
            'icon' => 'fas fa-history',
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
    // ... existing schedules
    
    // P&L Payment Reminders - runs daily at 9 AM
    $schedule->command('pnl:send-payment-reminders')->dailyAt('09:00');
}
```

---

## Step 8: Configure Email (for reminders)

Make sure your `.env` has email settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@ticketkart.com
MAIL_FROM_NAME="TicketKart"
```

---

## Step 9: Clear Cache & Test

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

## Step 10: Access P&L Module

Visit: `https://ppe.ticketkart.com/pnl/dashboard`

---

## Quick Copy Commands (Linux/Mac)

If you have the P&L module folder, run these from your Laravel project root:

```bash
# Assuming P&L module is in /path/to/pnl-module/

# Copy all at once
cp -r /path/to/pnl-module/app/Models/PnL app/Models/
cp -r /path/to/pnl-module/app/Http/Controllers/PnL app/Http/Controllers/
cp -r /path/to/pnl-module/app/Mail/* app/Mail/
cp -r /path/to/pnl-module/app/Exports/* app/Exports/
cp -r /path/to/pnl-module/app/Policies/* app/Policies/
cp -r /path/to/pnl-module/app/Providers/* app/Providers/
cp -r /path/to/pnl-module/app/Traits app/
cp -r /path/to/pnl-module/app/Console/Commands/* app/Console/Commands/
cp -r /path/to/pnl-module/database/migrations/* database/migrations/
cp -r /path/to/pnl-module/resources/views/pnl resources/views/
cp /path/to/pnl-module/routes/pnl.php routes/

# Create directories if they don't exist
mkdir -p app/Models/PnL
mkdir -p app/Http/Controllers/PnL
mkdir -p app/Exports
mkdir -p app/Traits
```

---

## Troubleshooting

### Error: Class not found
```bash
composer dump-autoload
```

### Error: View not found
```bash
php artisan view:clear
```

### Error: Route not found
```bash
php artisan route:clear
php artisan route:cache
```

### Error: Permission denied (storage)
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

## Support

For any issues, check:
1. All files are copied correctly
2. Service provider is registered
3. Routes are included
4. Migrations ran successfully

