<?php

namespace App\Models\PnL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PnlSettings extends Model
{
    use HasFactory;

    protected $table = 'pnl_settings';

    protected $fillable = [
        'user_id',
        'default_tax_rate',
        'invoice_prefix',
        'invoice_next_number',
        'send_email_on_payment_created',
        'send_email_on_payment_paid',
        'send_email_on_payment_scheduled',
        'company_name',
        'company_address',
        'company_vat_number',
        'walkthrough_dismissed',
    ];

    protected $casts = [
        'default_tax_rate' => 'decimal:2',
        'invoice_next_number' => 'integer',
        'send_email_on_payment_created' => 'boolean',
        'send_email_on_payment_paid' => 'boolean',
        'send_email_on_payment_scheduled' => 'boolean',
        'walkthrough_dismissed' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get or create settings for a user
     */
    public static function getOrCreate($userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'default_tax_rate' => 20.00,
                'invoice_prefix' => 'INV',
                'invoice_next_number' => 1,
                'send_email_on_payment_created' => true,
                'send_email_on_payment_paid' => true,
                'send_email_on_payment_scheduled' => true,
            ]
        );
    }

    /**
     * Generate the next invoice number in format: INV-YYYYMM-XXX
     * E.g., INV-202501-001
     */
    public function generateInvoiceNumber(): string
    {
        $yearMonth = now()->format('Ym');
        $prefix = $this->invoice_prefix ?? 'INV';
        $sequence = str_pad($this->invoice_next_number, 3, '0', STR_PAD_LEFT);
        
        // Increment the sequence for next time
        $this->increment('invoice_next_number');
        
        return "{$prefix}-{$yearMonth}-{$sequence}";
    }

    /**
     * Reset the invoice sequence (typically at start of new month/year)
     */
    public function resetInvoiceSequence(int $startNumber = 1): void
    {
        $this->update(['invoice_next_number' => $startNumber]);
    }
}
