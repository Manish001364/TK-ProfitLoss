<?php

namespace App\Models\PnL;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PnlVendor extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'pnl_vendors';

    protected $fillable = [
        'user_id',
        'full_name',
        'business_name',
        'type',
        'email',
        'phone',
        'alternate_phone',
        'business_address',
        'home_address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'bank_ifsc_code',
        'bank_branch',
        'tax_vat_reference',
        'pan_number',
        'gst_number',
        'notes',
        'preferred_payment_cycle',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'bank_account_number', // Sensitive data
    ];

    // Type options
    public static function getTypes(): array
    {
        return [
            'artist' => 'Artist',
            'dj' => 'DJ',
            'vendor' => 'Vendor',
            'staff' => 'Staff',
            'venue' => 'Venue',
            'equipment' => 'Equipment',
            'catering' => 'Catering',
            'security' => 'Security',
            'other' => 'Other',
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(PnlExpense::class, 'vendor_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PnlPayment::class, 'vendor_id');
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
    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->where('status', 'paid')->sum('amount');
    }

    public function getTotalPendingAttribute(): float
    {
        return $this->payments()->whereIn('status', ['pending', 'scheduled'])->sum('amount');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->business_name ?: $this->full_name;
    }

    public function getMaskedBankAccountAttribute(): string
    {
        if (!$this->bank_account_number) return 'N/A';
        return 'XXXX' . substr($this->bank_account_number, -4);
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

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('full_name', 'like', "%{$search}%")
              ->orWhere('business_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }
}
