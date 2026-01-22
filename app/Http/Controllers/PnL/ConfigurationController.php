<?php
/**
 * Configuration Controller
 * 
 * Shows combined Categories & Service Types management page.
 * 
 * Data sources:
 * - User categories: pnl_expense_categories WHERE user_id = {current_user}
 * - System categories: pnl_expense_categories WHERE user_id IS NULL
 * - User service types: pnl_service_types_user WHERE user_id = {current_user}
 * - System service types: Hardcoded defaults from PnlServiceType::getDefaultTypes()
 */

namespace App\Http\Controllers\PnL;

use App\Http\Controllers\Controller;
use App\Models\PnL\PnlExpenseCategory;
use App\Models\PnL\PnlServiceType;
use Illuminate\Support\Facades\DB;

class ConfigurationController extends Controller
{
    /**
     * Show combined Categories & Service Types page
     */
    public function index()
    {
        $userId = auth()->id();
        
        // User's CUSTOM expense categories (user_id = current user)
        $userCategories = PnlExpenseCategory::where('user_id', $userId)
            ->where('is_active', true)
            ->withCount('expenses')
            ->orderBy('sort_order')
            ->get();
        
        // SYSTEM expense categories (user_id = NULL)
        $systemCategories = PnlExpenseCategory::whereNull('user_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        // If no system categories in DB, use hardcoded defaults
        if ($systemCategories->isEmpty()) {
            $systemCategories = collect(PnlExpenseCategory::getDefaultCategories())
                ->map(fn($cat) => (object) $cat);
        }
        
        // User's CUSTOM service types
        $userServiceTypes = collect();
        try {
            $userServiceTypes = DB::table('pnl_service_types_user')
                ->where('user_id', $userId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            // Table doesn't exist yet
        }
        
        // SYSTEM service types (hardcoded defaults)
        $systemServiceTypes = collect();
        $defaults = PnlServiceType::getDefaultTypes();
        foreach ($defaults as $slug => $data) {
            $systemServiceTypes->push((object)[
                'slug' => $slug,
                'name' => $data['name'],
                'icon' => $data['icon'],
                'color' => $data['color'],
            ]);
        }
        
        return view('pnl.configuration.index', compact(
            'userCategories', 
            'systemCategories',
            'userServiceTypes',
            'systemServiceTypes'
        ));
    }
}
