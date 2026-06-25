<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('create', $model->getActivityDescription('created'));
        });

        static::updated(function ($model) {
            $old = $model->getOriginal();
            $new = $model->getAttributes();
            $changed = [];
            foreach (array_diff_assoc($new, $old) as $key => $value) {
                if (!in_array($key, $model->getHidden())) {
                    $changed[$key] = ['old' => $old[$key] ?? null, 'new' => $value];
                }
            }
            if (!empty($changed)) {
                $model->logActivity('update', $model->getActivityDescription('updated'), $old, $new);
            }
        });

        static::deleted(function ($model) {
            $model->logActivity('delete', $model->getActivityDescription('deleted'), $model->getOriginal());
        });
    }

    protected function logActivity(string $action, string $description, $oldValues = null, $newValues = null)
    {
        $admin = auth('sanctum')->user();
        if (!$admin) return;

        ActivityLog::create([
            'admin_id'     => $admin->id,
            'action'       => $action,
            'subject_type' => get_class($this),
            'subject_id'   => $this->id,
            'description'  => $description,
            'old_values'   => $oldValues ? json_encode($oldValues) : null,
            'new_values'   => $newValues ? json_encode($newValues) : null,
            'ip_address'   => Request::ip(),
            'user_agent'   => Request::userAgent(),
        ]);
    }

    protected function getActivityDescription(string $event): string
    {
        $name = $this->name ?? $this->username ?? $this->title ?? "{$event} " . class_basename($this);
        return strtolower(class_basename($this)) . " {$event}: {$name}";
    }
}
