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
     * Separates USER custom entries from SYSTEM defaults
     */
    public function index()
    {
        $userId = auth()->id();
        
        // Get user's CUSTOM expense categories only
        $userCategories = PnlExpenseCategory::where('user_id', $userId)
            ->withCount('expenses')
            ->ordered()
            ->get();
        
        // Get SYSTEM expense categories (for reference)
        $systemCategories = $this->getSystemExpenseCategories();
        
        // Get user's CUSTOM service types only
        $userServiceTypes = collect();
        try {
            $userServiceTypes = DB::table('pnl_service_types_user')
                ->where('user_id', $userId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            // Table might not exist
        }
        
        // Get SYSTEM service types (for reference)
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
     */
    private function getSystemExpenseCategories()
    {
        $categories = collect();
        
        // Try new system table first
        try {
            $categories = DB::table('pnl_expense_categories_system')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        } catch (\Exception $e) {
            // Table doesn't exist, use legacy
        }
        
        // If empty, get from legacy table (where user_id is null = system)
        if ($categories->isEmpty()) {
            $categories = PnlExpenseCategory::whereNull('user_id')
                ->where('is_active', true)
                ->ordered()
                ->get();
        }
        
        // If still empty, return hardcoded defaults
        if ($categories->isEmpty()) {
            $categories = collect([
                (object)['name' => 'Artist/Performer Fee', 'icon' => 'fas fa-microphone', 'color' => '#dc3545'],
                (object)['name' => 'DJ Fee', 'icon' => 'fas fa-headphones', 'color' => '#6f42c1'],
                (object)['name' => 'Venue Hire', 'icon' => 'fas fa-building', 'color' => '#0d6efd'],
                (object)['name' => 'Equipment Rental', 'icon' => 'fas fa-sliders-h', 'color' => '#198754'],
                (object)['name' => 'Catering', 'icon' => 'fas fa-utensils', 'color' => '#fd7e14'],
                (object)['name' => 'Security', 'icon' => 'fas fa-shield-alt', 'color' => '#6c757d'],
                (object)['name' => 'Marketing', 'icon' => 'fas fa-bullhorn', 'color' => '#e91e8c'],
                (object)['name' => 'Staff Wages', 'icon' => 'fas fa-users', 'color' => '#17a2b8'],
                (object)['name' => 'Transport', 'icon' => 'fas fa-truck', 'color' => '#20c997'],
                (object)['name' => 'Decorations', 'icon' => 'fas fa-paint-brush', 'color' => '#ffc107'],
                (object)['name' => 'Insurance', 'icon' => 'fas fa-file-contract', 'color' => '#495057'],
                (object)['name' => 'Licensing', 'icon' => 'fas fa-certificate', 'color' => '#6610f2'],
                (object)['name' => 'Photography/Video', 'icon' => 'fas fa-camera', 'color' => '#e83e8c'],
                (object)['name' => 'Other', 'icon' => 'fas fa-ellipsis-h', 'color' => '#adb5bd'],
            ]);
        }
        
        return $categories;
    }

    /**
     * Get system service types
     */
    private function getSystemServiceTypes()
    {
        $types = collect();
        
        // Try new system table first
        try {
            $types = DB::table('pnl_service_types_system')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        } catch (\Exception $e) {
            // Table doesn't exist
        }
        
        // If empty, return hardcoded defaults
        if ($types->isEmpty()) {
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
        }
        
        return $types;
    }
}
