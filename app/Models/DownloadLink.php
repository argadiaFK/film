<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DownloadLink extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'film_id',
        'name',
        'url',
        'quality',
        'size',
        'click_count',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'click_count' => 'integer',
    ];

    /**
     * Get the film that owns the download link.
     */
    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

    /**
     * Scope a query to only include active links.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Increment the click count.
     */
    public function incrementClick(): void
    {
        $this->increment('click_count');
    }
}
