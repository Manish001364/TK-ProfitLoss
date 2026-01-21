<?php
/**
 * Configuration Controller
 * 
 * Shows combined Categories & Service Types management page.
 * 
 * IMPORTANT: Data sources:
 * - User categories: pnl_expense_categories WHERE user_id = {current_user}
 * - System categories: pnl_expense_categories WHERE user_id IS NULL
 *   OR pnl_expense_categories_system table (if exists)
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
        
        // Get user's CUSTOM expense categories ONLY
        // These are categories where user_id = current user (NOT NULL)
        $userCategories = PnlExpenseCategory::where('user_id', $userId)
            ->where('is_active', true)
            ->withCount('expenses')
            ->orderBy('sort_order')
            ->get();
        
        // Get SYSTEM expense categories (for reference display)
        $systemCategories = $this->getSystemExpenseCategories();
        
        // Get user's CUSTOM service types ONLY
        $userServiceTypes = $this->getUserServiceTypes($userId);
        
        // Get SYSTEM service types (for reference display)
        $systemServiceTypes = $this->getSystemServiceTypes();
        
        return view('pnl.configuration.index', compact(
            'userCategories', 
            'systemCategories',
            'userServiceTypes',
            'systemServiceTypes'
        ));
    }

    /**
     * Get system expense categories
     * Source: pnl_expense_categories WHERE user_id IS NULL
     * OR pnl_expense_categories_system table
     */
    private function getSystemExpenseCategories()
    {
        // First try the legacy table (user_id = NULL = system)
        $categories = PnlExpenseCategory::whereNull('user_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        // If found, return those
        if ($categories->isNotEmpty()) {
            return $categories;
        }
        
        // Try new system table
        try {
            $categories = DB::table('pnl_expense_categories_system')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
            
            if ($categories->isNotEmpty()) {
                return $categories;
            }
        } catch (\Exception $e) {
            // Table doesn't exist
        }
        
        // Return hardcoded defaults as fallback
        return collect([
            (object)['name' => 'Artist/Performer Fee', 'icon' => 'fas fa-microphone', 'color' => '#dc3545'],
            (object)['name' => 'DJ Fee', 'icon' => 'fas fa-headphones', 'color' => '#6f42c1'],
            (object)['name' => 'Venue Hire', 'icon' => 'fas fa-building', 'color' => '#0d6efd'],
            (object)['name' => 'Equipment Rental', 'icon' => 'fas fa-sliders-h', 'color' => '#198754'],
            (object)['name' => 'Catering', 'icon' => 'fas fa-utensils', 'color' => '#fd7e14'],
            (object)['name' => 'Security', 'icon' => 'fas fa-shield-alt', 'color' => '#6c757d'],
            (object)['name' => 'Marketing', 'icon' => 'fas fa-bullhorn', 'color' => '#e91e8c'],
            (object)['name' => 'Staff Wages', 'icon' => 'fas fa-users', 'color' => '#17a2b8'],
        ]);
    }

    /**
     * Get user's custom service types
     * Source: pnl_service_types_user WHERE user_id = current user
     */
    private function getUserServiceTypes($userId)
    {
        try {
            return DB::table('pnl_service_types_user')
                ->where('user_id', $userId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get system service types
     * Source: Hardcoded defaults from PnlServiceType
     */
    private function getSystemServiceTypes()
    {
        // First try the system table
        try {
            $types = DB::table('pnl_service_types_system')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
            
            if ($types->isNotEmpty()) {
                return $types;
            }
        } catch (\Exception $e) {
            // Table doesn't exist
        }
        
        // Return hardcoded defaults
        $defaults = PnlServiceType::getDefaultTypes();
        $types = collect();
        
        foreach ($defaults as $slug => $data) {
            $types->push((object)[
                'slug' => $slug,
                'name' => $data['name'],
                'icon' => $data['icon'],
                'color' => $data['color'],
            ]);
        }
        
        return $types;
    }
}
