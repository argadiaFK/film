<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'event',
        'model_type',
        'model_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject model.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo('model');
    }

    /**
     * Log an activity.
     */
    public static function log(
        string $event,
        string $description,
        ?Model $model = null,
        ?array $properties = null
    ): self {
        return static::create([
            'user_id' => auth()->id(),
            'event' => $event,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->getKey(),
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Scope for specific event type.
     */
    public function scopeEvent($query, string $event)
    {
        return $query->where('event', $event);
    }

    /**
     * Scope for specific model.
     */
    public function scopeForModel($query, Model $model)
    {
        return $query->where('model_type', get_class($model))
            ->where('model_id', $model->getKey());
    }

    /**
     * Get the formatted model name.
     */
    public function getModelNameAttribute(): string
    {
        if (!$this->model_type) {
            return '-';
        }
        return class_basename($this->model_type);
    }
}
