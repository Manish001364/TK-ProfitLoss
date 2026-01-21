<?php

/*
|--------------------------------------------------------------------------
| P&L Module Routes
|--------------------------------------------------------------------------
|
| Add this to your routes/web.php:
| require __DIR__.'/pnl.php';
|
| Or copy the contents below into your web.php file.
|
*/

use App\Http\Controllers\PnL\DashboardController;
use App\Http\Controllers\PnL\EventController;
use App\Http\Controllers\PnL\VendorController;
use App\Http\Controllers\PnL\ExpenseController;
use App\Http\Controllers\PnL\ExpenseCategoryController;
use App\Http\Controllers\PnL\PaymentController;
use App\Http\Controllers\PnL\RevenueController;
use App\Http\Controllers\PnL\AttachmentController;
use App\Http\Controllers\PnL\ExportController;
use App\Http\Controllers\PnL\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('pnl')->name('pnl.')->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/cashflow', [DashboardController::class, 'cashFlow'])->name('dashboard.cashflow');

    // Events
    Route::resource('events', EventController::class);
    Route::post('events/{event}/duplicate', [EventController::class, 'duplicate'])->name('events.duplicate');

    // Vendors (Artists/DJs/etc.)
    Route::get('vendors/export', [VendorController::class, 'export'])->name('vendors.export');
    Route::resource('vendors', VendorController::class);

    // Expense Categories
    Route::post('categories/reorder', [ExpenseCategoryController::class, 'reorder'])->name('categories.reorder');
    Route::resource('categories', ExpenseCategoryController::class)->except(['show']);

    // Expenses
    Route::get('expenses/{expense}/pdf', [ExpenseController::class, 'generatePdf'])->name('expenses.pdf');
    Route::post('expenses/{expense}/email', [ExpenseController::class, 'sendInvoiceEmail'])->name('expenses.email');
    Route::resource('expenses', ExpenseController::class);

    // Payments
    Route::get('payments/upcoming', [PaymentController::class, 'upcoming'])->name('payments.upcoming');
    Route::get('payments/overdue', [PaymentController::class, 'overdue'])->name('payments.overdue');
    Route::post('payments/{payment}/mark-paid', [PaymentController::class, 'markAsPaid'])->name('payments.mark-paid');
    Route::post('payments/{payment}/send-reminder', [PaymentController::class, 'sendReminder'])->name('payments.send-reminder');
    Route::resource('payments', PaymentController::class)->except(['create', 'store', 'destroy']);

    // Revenue
    Route::resource('revenues', RevenueController::class);

    // Attachments
    Route::post('attachments', [AttachmentController::class, 'store'])->name('attachments.store');
    Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    // Exports
    Route::get('export/pnl-summary', [ExportController::class, 'pnlSummary'])->name('export.pnl-summary');
    Route::get('export/event/{event}', [ExportController::class, 'eventPnl'])->name('export.event');
    Route::get('export/vendors', [ExportController::class, 'vendors'])->name('export.vendors');
    Route::get('export/category-expenses', [ExportController::class, 'categoryExpenses'])->name('export.category-expenses');

    // Audit Logs
    Route::get('audit', [AuditLogController::class, 'index'])->name('audit.index');
    Route::get('audit/{auditLog}', [AuditLogController::class, 'show'])->name('audit.show');
});
