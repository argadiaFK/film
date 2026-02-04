<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Episode extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'series_id',
        'season_number',
        'episode_number',
        'title',
        'slug',
        'synopsis',
        'duration_minutes',
        'thumbnail',
        'air_date',
        'status',
    ];

    protected $casts = [
        'season_number' => 'integer',
        'episode_number' => 'integer',
        'duration_minutes' => 'integer',
        'air_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($episode) {
            if (empty($episode->slug)) {
                $episode->slug = Str::slug($episode->title);
            }
        });
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function streamingSources(): HasMany
    {
        return $this->hasMany(EpisodeStreamingSource::class)->orderBy('sort_order');
    }

    public function downloadLinks(): HasMany
    {
        return $this->hasMany(EpisodeDownloadLink::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function getEpisodeCodeAttribute(): string
    {
        return sprintf('S%02dE%02d', $this->season_number, $this->episode_number);
    }

    public function getFullTitleAttribute(): string
    {
        return "{$this->episode_code} - {$this->title}";
    }
}
