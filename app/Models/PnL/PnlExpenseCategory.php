<?php

namespace App\Models\PnL;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    // Default categories for new users
    public static function getDefaultCategories(): array
    {
        return [
            ['name' => 'Artist Fee', 'type' => 'fixed', 'color' => '#ef4444', 'icon' => 'fas fa-music', 'sort_order' => 1],
            ['name' => 'Venue', 'type' => 'fixed', 'color' => '#f97316', 'icon' => 'fas fa-building', 'sort_order' => 2],
            ['name' => 'Marketing', 'type' => 'variable', 'color' => '#eab308', 'icon' => 'fas fa-bullhorn', 'sort_order' => 3],
            ['name' => 'Staff', 'type' => 'variable', 'color' => '#22c55e', 'icon' => 'fas fa-users', 'sort_order' => 4],
            ['name' => 'Equipment & Tech', 'type' => 'variable', 'color' => '#3b82f6', 'icon' => 'fas fa-cogs', 'sort_order' => 5],
            ['name' => 'Catering', 'type' => 'variable', 'color' => '#8b5cf6', 'icon' => 'fas fa-utensils', 'sort_order' => 6],
            ['name' => 'Security', 'type' => 'fixed', 'color' => '#ec4899', 'icon' => 'fas fa-shield-alt', 'sort_order' => 7],
            ['name' => 'Miscellaneous', 'type' => 'variable', 'color' => '#6b7280', 'icon' => 'fas fa-ellipsis-h', 'sort_order' => 8],
        ];
    }

    public static function createDefaultsForUser($userId): void
    {
        foreach (self::getDefaultCategories() as $category) {
            self::create(array_merge($category, ['user_id' => $userId]));
        }
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(PnlExpense::class, 'category_id');
    }

    // Calculated Attributes
    public function getTotalSpentAttribute(): float
    {
        return $this->expenses()->sum('total_amount');
    }

    public function getTotalSpentForEventAttribute($eventId): float
    {
        return $this->expenses()->where('event_id', $eventId)->sum('total_amount');
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
