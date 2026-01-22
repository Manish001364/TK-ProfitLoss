<?php

namespace App\Models\PnL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PnlCurrencyRate extends Model
{
    use HasFactory;

    protected $table = 'pnl_currency_rates';

    protected $fillable = [
        'user_id',
        'from_currency',
        'to_currency',
        'rate',
    ];

    protected $casts = [
        'rate' => 'decimal:6',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get conversion rate for a user between two currencies
     */
    public static function getRate($userId, $fromCurrency, $toCurrency): ?float
    {
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        $rate = self::where('user_id', $userId)
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->first();

        if ($rate) {
            return (float) $rate->rate;
        }

        // Try reverse rate
        $reverseRate = self::where('user_id', $userId)
            ->where('from_currency', $toCurrency)
            ->where('to_currency', $fromCurrency)
            ->first();

        if ($reverseRate && $reverseRate->rate > 0) {
            return 1 / (float) $reverseRate->rate;
        }

        return null;
    }

    /**
     * Convert amount between currencies
     */
    public static function convert($userId, $amount, $fromCurrency, $toCurrency): ?float
    {
        $rate = self::getRate($userId, $fromCurrency, $toCurrency);
        
        if ($rate === null) {
            return null;
        }

        return $amount * $rate;
    }

    /**
     * Set or update a conversion rate
     */
    public static function setRate($userId, $fromCurrency, $toCurrency, $rate): self
    {
        return self::updateOrCreate(
            [
                'user_id' => $userId,
                'from_currency' => $fromCurrency,
                'to_currency' => $toCurrency,
            ],
            ['rate' => $rate]
        );
    }

    /**
     * Get all rates for a user
     */
    public static function getAllForUser($userId)
    {
        return self::where('user_id', $userId)->get();
    }
}
