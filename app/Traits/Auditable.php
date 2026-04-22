<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->logAudit('created');
        });

        static::updated(function ($model) {
            $model->logAudit('updated');
        });

        static::deleted(function ($model) {
            $model->logAudit('deleted');
        });
    }

    protected function logAudit(string $event)
    {
        $oldValues = null;
        $newValues = $this->getAttributes();

        if ($event === 'updated') {
            $newValues = $this->getChanges();
            $oldValues = array_intersect_key($this->getOriginal(), $newValues);
        } elseif ($event === 'deleted') {
            $oldValues = $this->getOriginal();
            $newValues = null;
        }

        // Hide sensitive fields
        $hidden = ['password', 'remember_token'];
        if ($oldValues) {
            foreach ($hidden as $field) unset($oldValues[$field]);
        }
        if ($newValues) {
            foreach ($hidden as $field) unset($newValues[$field]);
        }

        AuditLog::create([
            'company_id' => $this->company_id ?? (Auth::check() ? Auth::user()->company_id : null),
            'user_id' => Auth::id(),
            'event' => $event,
            'auditable_type' => get_class($this),
            'auditable_id' => $this->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'url' => request()->fullUrl(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
