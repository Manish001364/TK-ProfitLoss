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
use App\Policies\PnlEventPolicy;
use App\Policies\PnlVendorPolicy;
use App\Policies\PnlExpensePolicy;
use App\Policies\PnlExpenseCategoryPolicy;
use App\Policies\PnlRevenuePolicy;
use App\Policies\PnlPaymentPolicy;

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
        Gate::policy(PnlExpense::class, PnlExpensePolicy::class);
        Gate::policy(PnlExpenseCategory::class, PnlExpenseCategoryPolicy::class);
        Gate::policy(PnlRevenue::class, PnlRevenuePolicy::class);
        Gate::policy(PnlPayment::class, PnlPaymentPolicy::class);
    }
}
