<?php

/*
|--------------------------------------------------------------------------
| P&L Module Service Provider
|--------------------------------------------------------------------------
|
| Register this in config/app.php under 'providers' array:
| App\Providers\PnLServiceProvider::class,
|
*/

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\PnL\PnlEvent;
use App\Models\PnL\PnlVendor;
use App\Models\PnL\PnlExpense;
use App\Models\PnL\PnlExpenseCategory;
use App\Models\PnL\PnlPayment;
use App\Models\PnL\PnlRevenue;
use App\Models\PnL\PnlAttachment;
use App\Models\PnL\PnlAuditLog;
use App\Policies\PnlEventPolicy;
use App\Policies\PnlVendorPolicy;

class PnLServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(PnlEvent::class, PnlEventPolicy::class);
        Gate::policy(PnlVendor::class, PnlVendorPolicy::class);
        
        // For other models, use simple ownership check
        Gate::define('view-pnl-expense', function ($user, PnlExpense $expense) {
            return $user->id === $expense->user_id;
        });
        Gate::define('update-pnl-expense', function ($user, PnlExpense $expense) {
            return $user->id === $expense->user_id;
        });
        Gate::define('delete-pnl-expense', function ($user, PnlExpense $expense) {
            return $user->id === $expense->user_id;
        });

        Gate::define('view-pnl-payment', function ($user, PnlPayment $payment) {
            return $user->id === $payment->user_id;
        });
        Gate::define('update-pnl-payment', function ($user, PnlPayment $payment) {
            return $user->id === $payment->user_id;
        });

        Gate::define('view-pnl-revenue', function ($user, PnlRevenue $revenue) {
            return $user->id === $revenue->user_id;
        });
        Gate::define('update-pnl-revenue', function ($user, PnlRevenue $revenue) {
            return $user->id === $revenue->user_id;
        });
        Gate::define('delete-pnl-revenue', function ($user, PnlRevenue $revenue) {
            return $user->id === $revenue->user_id;
        });

        Gate::define('view-pnl-category', function ($user, PnlExpenseCategory $category) {
            return $user->id === $category->user_id;
        });
        Gate::define('update-pnl-category', function ($user, PnlExpenseCategory $category) {
            return $user->id === $category->user_id;
        });
        Gate::define('delete-pnl-category', function ($user, PnlExpenseCategory $category) {
            return $user->id === $category->user_id;
        });

        Gate::define('view-pnl-attachment', function ($user, PnlAttachment $attachment) {
            return $user->id === $attachment->user_id;
        });
        Gate::define('delete-pnl-attachment', function ($user, PnlAttachment $attachment) {
            return $user->id === $attachment->user_id;
        });

        Gate::define('view-pnl-audit', function ($user, PnlAuditLog $log) {
            return $user->id === $log->user_id;
        });
    }
}
