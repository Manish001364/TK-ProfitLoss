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
        'gross_revenue',
        'platform_fees',
        'payment_gateway_fees',
        'taxes',
        'net_revenue',
        'tickets_refunded',
        'refund_amount',
        'net_revenue_after_refunds',
        'notes',
    ];

    protected $casts = [
        'ticket_price' => 'decimal:2',
        'gross_revenue' => 'decimal:2',
        'platform_fees' => 'decimal:2',
        'payment_gateway_fees' => 'decimal:2',
        'taxes' => 'decimal:2',
        'net_revenue' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'net_revenue_after_refunds' => 'decimal:2',
    ];

    // Ticket type options
    public static function getTicketTypes(): array
    {
        return [
            'regular' => 'Regular',
            'vip' => 'VIP',
            'early_bird' => 'Early Bird',
            'group' => 'Group',
            'complimentary' => 'Complimentary',
            'other' => 'Other',
        ];
    }

    // Auto-calculate revenues
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($revenue) {
            $revenue->gross_revenue = $revenue->tickets_sold * $revenue->ticket_price;
            $revenue->net_revenue = $revenue->gross_revenue - $revenue->platform_fees - $revenue->payment_gateway_fees - $revenue->taxes;
            $revenue->net_revenue_after_refunds = $revenue->net_revenue - $revenue->refund_amount;
        });
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

    // Calculated Attributes
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
