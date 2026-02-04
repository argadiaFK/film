<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Film extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'slug',
        'synopsis',
        'poster',
        'backdrop',
        'trailer_url',
        'year',
        'duration_minutes',
        'is_featured',
        'status',
    ];

    protected $casts = [
        'year' => 'integer',
        'duration_minutes' => 'integer',
        'is_featured' => 'boolean',
    ];

    /**
     * Get the genres for the film.
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'film_genre');
    }

    /**
     * Get the countries for the film.
     */
    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'film_country');
    }

    /**
     * Get the streaming sources for the film.
     */
    public function streamingSources(): HasMany
    {
        return $this->hasMany(StreamingSource::class)->orderBy('sort_order');
    }

    /**
     * Get the download links for the film.
     */
    public function downloadLinks(): HasMany
    {
        return $this->hasMany(DownloadLink::class);
    }

    /**
     * Get the SEO meta for the film.
     */
    public function seoMeta(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    /**
     * Get the comments for the film.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get approved comments count.
     */
    public function getApprovedCommentsCountAttribute(): int
    {
        return $this->comments()->approved()->count();
    }

    /**
     * Scope a query to only include published films.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to filter by genre.
     */
    public function scopeByGenre($query, $genreId)
    {
        return $query->whereHas('genres', fn($q) => $q->where('genres.id', $genreId));
    }

    /**
     * Scope a query to filter by country.
     */
    public function scopeByCountry($query, $countryId)
    {
        return $query->whereHas('countries', fn($q) => $q->where('countries.id', $countryId));
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration_minutes) {
            return '-';
        }

        $hours = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }

    /**
     * Get total download clicks.
     */
    public function getTotalDownloadClicksAttribute(): int
    {
        return $this->downloadLinks()->sum('click_count');
    }

    /**
     * Auto-generate SEO title from film title.
     */
    public function getAutoSeoTitleAttribute(): string
    {
        $title = $this->title;
        if ($this->year) {
            $title .= " ({$this->year})";
        }
        return $title . " - Nonton & Download";
    }

    /**
     * Auto-generate SEO description from synopsis.
     */
    public function getAutoSeoDescriptionAttribute(): string
    {
        $desc = $this->synopsis ? substr($this->synopsis, 0, 150) : "Nonton dan download {$this->title}";
        if ($this->year) {
            $desc .= " ({$this->year})";
        }
        return $desc . "...";
    }

    /**
     * Auto-generate SEO keywords from title, genres, countries.
     */
    public function getAutoSeoKeywordsAttribute(): string
    {
        $keywords = [$this->title, 'nonton', 'download', 'streaming'];

        if ($this->relationLoaded('genres') || $this->genres()->exists()) {
            $keywords = array_merge($keywords, $this->genres->pluck('name')->toArray());
        }

        if ($this->relationLoaded('countries') || $this->countries()->exists()) {
            $keywords = array_merge($keywords, $this->countries->pluck('name')->toArray());
        }

        return implode(', ', array_unique($keywords));
    }

    /**
     * Get the effective SEO data (custom or auto-generated).
     */
    public function getEffectiveSeoAttribute(): array
    {
        $seo = $this->seoMeta;

        return [
            'title' => $seo?->title ?: $this->auto_seo_title,
            'description' => $seo?->description ?: $this->auto_seo_description,
            'keywords' => $seo?->keywords ?: $this->auto_seo_keywords,
            'og_image' => $seo?->og_image ?: $this->poster,
            'canonical_url' => $seo?->canonical_url,
            'no_index' => $seo?->no_index ?? false,
            'no_follow' => $seo?->no_follow ?? false,
        ];
    }
}
