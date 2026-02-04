<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogObserver
{
    /**
     * Models to track.
     */
    protected static array $trackedModels = [
        \App\Models\Film::class,
        \App\Models\Genre::class,
        \App\Models\Country::class,
        \App\Models\User::class,
        \App\Models\Comment::class,
        \App\Models\Setting::class,
    ];

    public function created(Model $model): void
    {
        if (!$this->shouldLog($model)) {
            return;
        }

        ActivityLog::log(
            'created',
            $this->getDescription($model, 'created'),
            $model,
            ['attributes' => $model->getAttributes()]
        );
    }

    public function updated(Model $model): void
    {
        if (!$this->shouldLog($model)) {
            return;
        }

        $changes = $model->getChanges();
        unset($changes['updated_at']); // Ignore timestamp changes

        if (empty($changes)) {
            return;
        }

        ActivityLog::log(
            'updated',
            $this->getDescription($model, 'updated'),
            $model,
            [
                'old' => array_intersect_key($model->getOriginal(), $changes),
                'new' => $changes,
            ]
        );
    }

    public function deleted(Model $model): void
    {
        if (!$this->shouldLog($model)) {
            return;
        }

        ActivityLog::log(
            'deleted',
            $this->getDescription($model, 'deleted'),
            $model,
            ['attributes' => $model->getAttributes()]
        );
    }

    protected function shouldLog(Model $model): bool
    {
        // Don't log if no user is authenticated (e.g., during seeding)
        if (!auth()->check()) {
            return false;
        }

        // Don't log ActivityLog itself
        if ($model instanceof ActivityLog) {
            return false;
        }

        return true;
    }

    protected function getDescription(Model $model, string $action): string
    {
        $modelName = class_basename($model);
        $identifier = $model->getAttribute('title')
            ?? $model->getAttribute('name')
            ?? $model->getAttribute('key')
            ?? $model->getKey();

        return ucfirst($action) . " {$modelName}: {$identifier}";
    }
}
