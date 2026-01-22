<?php

namespace App\Models\PnL;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

class PnlExpense extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'pnl_expenses';

    protected $fillable = [
        'event_id',
        'category_id',
        'vendor_id',
        'user_id',
        'title',
        'description',
        'amount',
        'currency',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'is_taxable',
        'expense_date',
        'invoice_number',
        'receipt_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_taxable' => 'boolean',
        'expense_date' => 'date',
    ];

    // Auto-calculate total_amount
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($expense) {
            $expense->total_amount = $expense->amount + $expense->tax_amount;
        });
    }

    // Relationships
    public function event(): BelongsTo
    {
        return $this->belongsTo(PnlEvent::class, 'event_id');
    }

    /**
     * Get category relationship (legacy - for Eloquent eager loading)
     * Note: Use getCategoryDataAttribute() for better category resolution
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PnlExpenseCategory::class, 'category_id');
    }

    /**
     * Get category data from any source (system, user, legacy, or hardcoded)
     * This resolves the category from multiple possible tables
     */
    public function getCategoryDataAttribute()
    {
        if (!$this->category_id) {
            return null;
        }

        // Try the standard Eloquent relationship first
        if ($this->relationLoaded('category') && $this->getRelation('category')) {
            return $this->getRelation('category');
        }

        // Check hardcoded defaults
        if (str_starts_with($this->category_id, 'default_')) {
            $defaults = PnlExpenseCategory::getAllForUser($this->user_id);
            foreach ($defaults as $cat) {
                if ($cat->id === $this->category_id) {
                    return $cat;
                }
            }
        }

        // Try system categories table
        try {
            $systemCat = DB::table('pnl_expense_categories_system')
                ->where('id', $this->category_id)
                ->first();
            if ($systemCat) {
                $systemCat->is_system = true;
                return $systemCat;
            }
        } catch (\Exception $e) {
            // Table might not exist
        }

        // Try user categories table
        try {
            $userCat = DB::table('pnl_expense_categories_user')
                ->where('id', $this->category_id)
                ->first();
            if ($userCat) {
                $userCat->is_system = false;
                return $userCat;
            }
        } catch (\Exception $e) {
            // Table might not exist
        }

        // Try legacy pnl_expense_categories table
        try {
            $legacyCat = DB::table('pnl_expense_categories')
                ->where('id', $this->category_id)
                ->first();
            if ($legacyCat) {
                return $legacyCat;
            }
        } catch (\Exception $e) {
            // Table might not exist
        }

        return null;
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(PnlVendor::class, 'vendor_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(PnlPayment::class, 'expense_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(PnlAttachment::class, 'attachable');
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(PnlAuditLog::class, 'auditable');
    }

    // Calculated Attributes
    public function getPaymentStatusAttribute(): string
    {
        return $this->payment?->status ?? 'no_payment';
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->payment?->status === 'paid';
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeForCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopePaid($query)
    {
        return $query->whereHas('payment', function ($q) {
            $q->where('status', 'paid');
        });
    }

    public function scopePending($query)
    {
        return $query->whereDoesntHave('payment')
                     ->orWhereHas('payment', function ($q) {
                         $q->whereIn('status', ['pending', 'scheduled']);
                     });
    }
}
