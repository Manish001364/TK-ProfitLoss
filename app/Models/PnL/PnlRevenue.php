<?php

namespace App\Models\PnL;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PnlRevenue extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'pnl_revenues';

    protected $fillable = [
        'event_id',
        'user_id',
        'ticket_type',
        'ticket_name',
        'tickets_available',
        'tickets_sold',
        'ticket_price',
        'platform_fees',
        'payment_gateway_fees',
        'taxes',
        'tickets_refunded',
        'refund_amount',
        'notes',
    ];

    protected $casts = [
        'ticket_price' => 'decimal:2',
        'platform_fees' => 'decimal:2',
        'payment_gateway_fees' => 'decimal:2',
        'taxes' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'tickets_available' => 'integer',
        'tickets_sold' => 'integer',
        'tickets_refunded' => 'integer',
    ];

    // Ticket type options
    public static function getTicketTypes(): array
    {
        return [
            'general' => 'General',
            'vip' => 'VIP',
            'early_bird' => 'Early Bird',
            'group' => 'Group',
            'premium' => 'Premium',
            'student' => 'Student',
            'custom' => 'Custom',
        ];
    }

    // Relationships
    public function event(): BelongsTo
    {
        return $this->belongsTo(PnlEvent::class, 'event_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // Calculated Attributes (computed, not stored)
    public function getGrossRevenueAttribute(): float
    {
        return (float) ($this->tickets_sold * $this->ticket_price);
    }

    public function getNetRevenueAttribute(): float
    {
        return $this->gross_revenue - $this->platform_fees - $this->payment_gateway_fees - $this->taxes;
    }

    public function getNetRevenueAfterRefundsAttribute(): float
    {
        return $this->net_revenue - $this->refund_amount;
    }

    public function getSellThroughRateAttribute(): float
    {
        if ($this->tickets_available <= 0) return 0;
        return ($this->tickets_sold / $this->tickets_available) * 100;
    }

    public function getRefundRateAttribute(): float
    {
        if ($this->tickets_sold <= 0) return 0;
        return ($this->tickets_refunded / $this->tickets_sold) * 100;
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->ticket_name ?: ucfirst($this->ticket_type);
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

    public function scopeOfType($query, $type)
    {
        return $query->where('ticket_type', $type);
    }
}
