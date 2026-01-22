<?php

namespace App\Models\PnL;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class PnlAttachment extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pnl_attachments';

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'filename',
        'original_filename',
        'mime_type',
        'size',
        'path',
        'uploaded_by',
    ];

    // Attachment type options
    public static function getTypes(): array
    {
        return [
            'invoice' => 'Invoice',
            'contract' => 'Contract',
            'receipt' => 'Receipt',
            'proof_of_payment' => 'Proof of Payment',
            'other' => 'Other',
        ];
    }

    // Relationships
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // Calculated Attributes
    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function getIsPdfAttribute(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    // Methods
    public function deleteFile(): bool
    {
        return Storage::delete($this->path);
    }

    // Scopes
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
