<?php
/**
 * PnlExpenseCategory Model
 * 
 * Represents expense categories for the P&L module.
 * 
 * Data Sources (checked in order):
 * 1. pnl_expense_categories_system - System default categories (read-only)
 * 2. pnl_expense_categories_user - User's custom categories (editable)
 * 3. pnl_expense_categories (legacy) - For backward compatibility
 * 4. Hardcoded defaults - Fallback when tables don't exist
 */

namespace App\Models\PnL;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class PnlExpenseCategory extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pnl_expense_categories';

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'description',
        'default_budget_limit',
        'color',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'default_budget_limit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Check if this is a system category (user_id = NULL)
     */
    public function getIsSystemAttribute(): bool
    {
        return $this->user_id === null;
    }

    /**
     * Get ALL categories for expense dropdown (system + user custom)
     * Used when creating/editing expenses
     * Similar approach to PnlServiceType::getAllForUser()
     */
    public static function getAllForUser($userId)
    {
        $allCategories = collect();
        $hasSystemTable = false;
        $hasUserTable = false;
        
        // 1. Try to get from pnl_expense_categories_system table
        try {
            $systemCategories = DB::table('pnl_expense_categories_system')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(function ($item) {
                    $item->is_system = true;
                    return $item;
                });
            if ($systemCategories->isNotEmpty()) {
                $hasSystemTable = true;
                $allCategories = $allCategories->merge($systemCategories);
            }
        } catch (\Exception $e) {
            // Table might not exist
        }
        
        // 2. Try to get from pnl_expense_categories_user table
        try {
            $userCategories = DB::table('pnl_expense_categories_user')
                ->where('user_id', $userId)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(function ($item) {
                    $item->is_system = false;
                    return $item;
                });
            if ($userCategories->isNotEmpty()) {
                $hasUserTable = true;
                $allCategories = $allCategories->merge($userCategories);
            }
        } catch (\Exception $e) {
            // Table might not exist
        }
        
        // 3. ALWAYS try legacy pnl_expense_categories table for additional categories
        // This ensures custom categories created via the legacy system are included
        try {
            $legacyCategories = DB::table('pnl_expense_categories')
                ->where(function($query) use ($userId) {
                    $query->whereNull('user_id')
                          ->orWhere('user_id', $userId);
                })
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(function ($item) {
                    $item->is_system = ($item->user_id === null);
                    return $item;
                });
            
            // Merge but avoid duplicates by ID
            $existingIds = $allCategories->pluck('id')->toArray();
            foreach ($legacyCategories as $cat) {
                if (!in_array($cat->id, $existingIds)) {
                    $allCategories->push($cat);
                }
            }
        } catch (\Exception $e) {
            // Table might not exist
        }
        
        // 4. If still empty, return hardcoded defaults
        if ($allCategories->isEmpty()) {
            return self::getHardcodedDefaults();
        }
        
        return $allCategories;
    }

    /**
     * Get only user's CUSTOM categories (for management page)
     * Used on Configuration page
     */
    public static function getUserCategories($userId)
    {
        $userCategories = collect();
        
        // Try new table first
        try {
            $userCategories = DB::table('pnl_expense_categories_user')
                ->where('user_id', $userId)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(function ($item) {
                    $item->is_system = false;
                    return $item;
                });
        } catch (\Exception $e) {
            // Table might not exist
        }
        
        // Fallback to legacy table
        if ($userCategories->isEmpty()) {
            try {
                $userCategories = DB::table('pnl_expense_categories')
                    ->where('user_id', $userId)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get()
                    ->map(function ($item) {
                        $item->is_system = false;
                        return $item;
                    });
            } catch (\Exception $e) {
                // Table might not exist
            }
        }
        
        return $userCategories;
    }

    /**
     * Get only SYSTEM categories (for reference display)
     * Used on Configuration page
     */
    public static function getSystemCategories()
    {
        $systemCategories = collect();
        
        // Try new table first
        try {
            $systemCategories = DB::table('pnl_expense_categories_system')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(function ($item) {
                    $item->is_system = true;
                    return $item;
                });
        } catch (\Exception $e) {
            // Table might not exist
        }
        
        // Fallback to legacy table (where user_id is NULL)
        if ($systemCategories->isEmpty()) {
            try {
                $systemCategories = DB::table('pnl_expense_categories')
                    ->whereNull('user_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get()
                    ->map(function ($item) {
                        $item->is_system = true;
                        return $item;
                    });
            } catch (\Exception $e) {
                // Table might not exist
            }
        }
        
        // Fallback to hardcoded
        if ($systemCategories->isEmpty()) {
            return self::getHardcodedDefaults();
        }
        
        return $systemCategories;
    }

    /**
     * Hardcoded default categories (fallback)
     */
    private static function getHardcodedDefaults()
    {
        return collect([
            (object)['id' => 'default_artist', 'name' => 'Artist/Performer Fee', 'type' => 'fixed', 'color' => '#dc3545', 'icon' => 'fas fa-microphone', 'is_system' => true],
            (object)['id' => 'default_dj', 'name' => 'DJ Fee', 'type' => 'fixed', 'color' => '#6f42c1', 'icon' => 'fas fa-headphones', 'is_system' => true],
            (object)['id' => 'default_venue', 'name' => 'Venue Hire', 'type' => 'fixed', 'color' => '#0d6efd', 'icon' => 'fas fa-building', 'is_system' => true],
            (object)['id' => 'default_equipment', 'name' => 'Equipment Rental', 'type' => 'variable', 'color' => '#198754', 'icon' => 'fas fa-sliders-h', 'is_system' => true],
            (object)['id' => 'default_catering', 'name' => 'Catering', 'type' => 'variable', 'color' => '#fd7e14', 'icon' => 'fas fa-utensils', 'is_system' => true],
            (object)['id' => 'default_security', 'name' => 'Security', 'type' => 'fixed', 'color' => '#6c757d', 'icon' => 'fas fa-shield-alt', 'is_system' => true],
            (object)['id' => 'default_marketing', 'name' => 'Marketing', 'type' => 'variable', 'color' => '#e91e8c', 'icon' => 'fas fa-bullhorn', 'is_system' => true],
            (object)['id' => 'default_staff', 'name' => 'Staff Wages', 'type' => 'variable', 'color' => '#17a2b8', 'icon' => 'fas fa-users', 'is_system' => true],
            (object)['id' => 'default_other', 'name' => 'Other', 'type' => 'variable', 'color' => '#adb5bd', 'icon' => 'fas fa-tag', 'is_system' => true],
        ]);
    }

    /**
     * Default categories definition (for seeding/migration)
     */
    public static function getDefaultCategories(): array
    {
        return [
            ['name' => 'Artist/Performer Fee', 'type' => 'fixed', 'color' => '#dc3545', 'icon' => 'fas fa-microphone'],
            ['name' => 'DJ Fee', 'type' => 'fixed', 'color' => '#6f42c1', 'icon' => 'fas fa-headphones'],
            ['name' => 'Venue Hire', 'type' => 'fixed', 'color' => '#0d6efd', 'icon' => 'fas fa-building'],
            ['name' => 'Equipment Rental', 'type' => 'variable', 'color' => '#198754', 'icon' => 'fas fa-sliders-h'],
            ['name' => 'Catering', 'type' => 'variable', 'color' => '#fd7e14', 'icon' => 'fas fa-utensils'],
            ['name' => 'Security', 'type' => 'fixed', 'color' => '#6c757d', 'icon' => 'fas fa-shield-alt'],
            ['name' => 'Marketing', 'type' => 'variable', 'color' => '#e91e8c', 'icon' => 'fas fa-bullhorn'],
            ['name' => 'Staff Wages', 'type' => 'variable', 'color' => '#17a2b8', 'icon' => 'fas fa-users'],
        ];
    }

    // ===== RELATIONSHIPS =====

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(PnlExpense::class, 'category_id');
    }

    // ===== SCOPES =====

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSystemDefaults($query)
    {
        return $query->whereNull('user_id');
    }

    // ===== HELPERS =====

    public function isCustom(): bool
    {
        return $this->user_id !== null;
    }
}
