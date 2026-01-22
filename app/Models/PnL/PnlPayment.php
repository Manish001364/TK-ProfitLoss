<?php

namespace App\Models\PnL;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PnlPayment extends Model
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditLog;

    protected $table = 'pnl_payments';

    protected $fillable = [
        'expense_id',
        'vendor_id',
        'user_id',
        'amount',
        'status',
        'scheduled_date',
        'actual_paid_date',
        'payment_method',
        'transaction_reference',
        'internal_notes',
        'reminder_enabled',
        'reminder_days_before',
        'reminder_on_due_date',
        'last_reminder_sent_at',
        'reminder_count',
        'send_email_to_vendor',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'scheduled_date' => 'date',
        'actual_paid_date' => 'date',
        'reminder_enabled' => 'boolean',
        'reminder_on_due_date' => 'boolean',
        'send_email_to_vendor' => 'boolean',
        'last_reminder_sent_at' => 'datetime',
    ];

    // Status options
    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'scheduled' => 'Scheduled',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
        ];
    }

    // Payment method options
    public static function getPaymentMethods(): array
    {
        return [
            'bank_transfer' => 'Bank Transfer',
            'cash' => 'Cash',
            'cheque' => 'Cheque',
            'upi' => 'UPI',
            'other' => 'Other',
        ];
    }

    // Relationships
    public function expense(): BelongsTo
    {
        return $this->belongsTo(PnlExpense::class, 'expense_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(PnlVendor::class, 'vendor_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
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
    public function getDaysUntilDueAttribute(): ?int
    {
        if (!$this->scheduled_date) return null;
        return now()->startOfDay()->diffInDays($this->scheduled_date, false);
    }

    public function getIsOverdueAttribute(): bool
    {
        if (!$this->scheduled_date || $this->status === 'paid') return false;
        return $this->scheduled_date->isPast();
    }

    public function getShouldSendReminderAttribute(): bool
    {
        if (!$this->reminder_enabled || $this->status === 'paid') return false;
        if (!$this->scheduled_date) return false;

        $daysUntilDue = $this->days_until_due;
        
        // Reminder X days before
        if ($daysUntilDue === $this->reminder_days_before) return true;
        
        // Reminder on due date
        if ($this->reminder_on_due_date && $daysUntilDue === 0) return true;

        return false;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'paid' => 'success',
            'scheduled' => 'warning',
            'pending' => 'info',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'scheduled']);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeOverdue($query)
    {
        return $query->whereIn('status', ['pending', 'scheduled'])
                     ->whereNotNull('scheduled_date')
                     ->where('scheduled_date', '<', now()->toDateString());
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->whereIn('status', ['pending', 'scheduled'])
                     ->whereNotNull('scheduled_date')
                     ->whereBetween('scheduled_date', [now()->toDateString(), now()->addDays($days)->toDateString()]);
    }

    public function scopeNeedsReminder($query)
    {
        return $query->where('reminder_enabled', true)
                     ->whereIn('status', ['pending', 'scheduled'])
                     ->whereNotNull('scheduled_date');
    }
}
