<?php

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
        
        // Get expense categories (system + user)
        $expenseCategories = $this->getExpenseCategories($userId);
        
        // Get service types (system + user)
        $serviceTypes = PnlServiceType::getAllForUser($userId);
        
        return view('pnl.configuration.index', compact('expenseCategories', 'serviceTypes'));
    }

    /**
     * Get all expense categories for a user (system + custom)
     */
    private function getExpenseCategories($userId)
    {
        // Get system default categories (read-only)
        $systemCategories = collect();
        try {
            $systemCategories = DB::table('pnl_expense_categories_system')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(function ($item) {
                    $item->is_system = true;
                    $item->expenses_count = DB::table('pnl_expenses')
                        ->where('category_id', $item->id)
                        ->count();
                    return $item;
                });
        } catch (\Exception $e) {
            // Table might not exist yet
        }

        // Get user's custom categories
        $userCategories = PnlExpenseCategory::where('user_id', $userId)
            ->withCount('expenses')
            ->ordered()
            ->get()
            ->map(function ($item) {
                $item->is_system = false;
                return $item;
            });

        // Also get legacy categories with NULL user_id (system defaults from old schema)
        $legacyDefaults = PnlExpenseCategory::whereNull('user_id')
            ->where('is_active', true)
            ->withCount('expenses')
            ->ordered()
            ->get()
            ->map(function ($item) {
                $item->is_system = true;
                return $item;
            });

        // Combine all categories (system first, then user)
        return $systemCategories->merge($legacyDefaults)->merge($userCategories);
    }
}
