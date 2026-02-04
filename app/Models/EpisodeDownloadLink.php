<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EpisodeDownloadLink extends Model
{
    use HasUuids;

    protected $fillable = [
        'episode_id',
        'name',
        'url',
        'quality',
        'size',
        'click_count',
        'is_active',
    ];

    protected $casts = [
        'click_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }

    public function incrementClick(): void
    {
        $this->increment('click_count');
    }
}
