<?php

namespace App\Traits;

use App\Models\PnL\PnlAuditLog;
use Illuminate\Database\Eloquent\Model;

trait HasAuditLog
{
    protected static function bootHasAuditLog(): void
    {
        // Log creation
        static::created(function (Model $model) {
            PnlAuditLog::log($model, 'created', null, $model->toArray());
        });

        // Log updates
        static::updated(function (Model $model) {
            $oldValues = array_intersect_key(
                $model->getOriginal(),
                $model->getChanges()
            );
            $newValues = $model->getChanges();
            
            if (!empty($newValues)) {
                PnlAuditLog::log($model, 'updated', $oldValues, $newValues);
            }
        });

        // Log deletion
        static::deleted(function (Model $model) {
            PnlAuditLog::log($model, 'deleted', $model->toArray(), null);
        });

        // Log restoration (for soft deletes)
        if (method_exists(static::class, 'restored')) {
            static::restored(function (Model $model) {
                PnlAuditLog::log($model, 'restored', null, $model->toArray());
            });
        }
    }

    /**
     * Log a status change with a reason
     */
    public function logStatusChange(string $oldStatus, string $newStatus, ?string $reason = null): PnlAuditLog
    {
        return PnlAuditLog::log(
            $this,
            'status_changed',
            ['status' => $oldStatus],
            ['status' => $newStatus],
            $reason
        );
    }
}
