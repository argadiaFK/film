<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

class Series extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'slug',
        'synopsis',
        'year',
        'poster',
        'backdrop',
        'trailer_url',
        'rating',
        'total_seasons',
        'total_episodes',
        'status',
        'is_featured',
    ];

    protected $casts = [
        'year' => 'integer',
        'rating' => 'decimal:1',
        'total_seasons' => 'integer',
        'total_episodes' => 'integer',
        'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($series) {
            if (empty($series->slug)) {
                $series->slug = Str::slug($series->title);
            }
        });
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'series_genre');
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'series_country');
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class)->orderBy('season_number')->orderBy('episode_number');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function seoMeta(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    public function scopePublished($query)
    {
        return $query->whereIn('status', ['ongoing', 'completed']);
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getEpisodeCountAttribute(): int
    {
        return $this->episodes()->count();
    }

    public function getSeasonCountAttribute(): int
    {
        return $this->episodes()->distinct('season_number')->count('season_number');
    }
}
