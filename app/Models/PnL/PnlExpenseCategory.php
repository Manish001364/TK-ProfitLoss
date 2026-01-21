<?php
/**
 * PnlExpenseCategory Model
 * 
 * Represents expense categories for the P&L module.
 * 
 * Table: pnl_expense_categories
 * - user_id = NULL: System default categories (read-only)
 * - user_id = {id}: User's custom categories (editable)
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
     */
    public static function getAllForUser($userId)
    {
        // Get system categories (user_id = NULL) + user's custom categories (user_id = $userId)
        $categories = self::where(function($query) use ($userId) {
                $query->whereNull('user_id')           // System defaults
                      ->orWhere('user_id', $userId);   // User's custom
            })
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        // If no categories found, return hardcoded defaults
        if ($categories->isEmpty()) {
            return self::getHardcodedDefaults();
        }
        
        return $categories;
    }

    /**
     * Get only user's CUSTOM categories (for management page)
     * Used on Configuration page
     */
    public static function getUserCategories($userId)
    {
        return self::where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get only SYSTEM categories (for reference display)
     * Used on Configuration page
     */
    public static function getSystemCategories()
    {
        return self::whereNull('user_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Hardcoded default categories (fallback)
     */
    private static function getHardcodedDefaults()
    {
        return collect([
            (object)['id' => 'default_1', 'name' => 'Artist/Performer Fee', 'type' => 'fixed', 'color' => '#dc3545', 'icon' => 'fas fa-microphone'],
            (object)['id' => 'default_2', 'name' => 'DJ Fee', 'type' => 'fixed', 'color' => '#6f42c1', 'icon' => 'fas fa-headphones'],
            (object)['id' => 'default_3', 'name' => 'Venue Hire', 'type' => 'fixed', 'color' => '#0d6efd', 'icon' => 'fas fa-building'],
            (object)['id' => 'default_4', 'name' => 'Equipment Rental', 'type' => 'variable', 'color' => '#198754', 'icon' => 'fas fa-sliders-h'],
            (object)['id' => 'default_5', 'name' => 'Catering', 'type' => 'variable', 'color' => '#fd7e14', 'icon' => 'fas fa-utensils'],
            (object)['id' => 'default_6', 'name' => 'Security', 'type' => 'fixed', 'color' => '#6c757d', 'icon' => 'fas fa-shield-alt'],
            (object)['id' => 'default_7', 'name' => 'Marketing', 'type' => 'variable', 'color' => '#e91e8c', 'icon' => 'fas fa-bullhorn'],
            (object)['id' => 'default_8', 'name' => 'Staff Wages', 'type' => 'variable', 'color' => '#17a2b8', 'icon' => 'fas fa-users'],
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
