<?php

namespace App\Models\PnL;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PnlEvent extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'pnl_events';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'venue',
        'location',
        'event_date',
        'event_time',
        'budget',
        'status',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i',
        'budget' => 'decimal:2',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(PnlExpense::class, 'event_id');
    }

    public function revenues(): HasMany
    {
        return $this->hasMany(PnlRevenue::class, 'event_id');
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(PnlAuditLog::class, 'auditable');
    }

    // Calculated Attributes
    public function getTotalExpensesAttribute(): float
    {
        return (float) $this->expenses()->sum('total_amount');
    }

    public function getTotalRevenueAttribute(): float
    {
        // Calculate: (ticket_price * tickets_sold) - fees - refunds
        $revenues = $this->revenues()->get();
        $total = 0;
        foreach ($revenues as $revenue) {
            $gross = $revenue->ticket_price * $revenue->tickets_sold;
            $net = $gross - $revenue->platform_fees - $revenue->payment_gateway_fees - $revenue->taxes - $revenue->refund_amount;
            $total += $net;
        }
        return (float) $total;
    }

    public function getGrossRevenueAttribute(): float
    {
        // Calculate: ticket_price * tickets_sold
        $revenues = $this->revenues()->get();
        $total = 0;
        foreach ($revenues as $revenue) {
            $total += $revenue->ticket_price * $revenue->tickets_sold;
        }
        return (float) $total;
    }

    public function getNetProfitAttribute(): float
    {
        return $this->total_revenue - $this->total_expenses;
    }

    public function getTotalTicketsSoldAttribute(): int
    {
        return $this->revenues()->sum('tickets_sold');
    }

    public function getProfitStatusAttribute(): string
    {
        $profit = $this->net_profit;
        if ($profit > 0) return 'profit';
        if ($profit < 0) return 'loss';
        return 'break-even';
    }

    public function getBudgetUtilizationAttribute(): float
    {
        if ($this->budget <= 0) return 0;
        return ($this->total_expenses / $this->budget) * 100;
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now()->toDateString());
    }

    public function scopePast($query)
    {
        return $query->where('event_date', '<', now()->toDateString());
    }
}
