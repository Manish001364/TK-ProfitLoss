<?php

namespace App\Models\PnL;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
        'tax_amount',
        'total_amount',
        'expense_date',
        'invoice_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(PnlExpenseCategory::class, 'category_id');
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
