<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMeta extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'seoable_type',
        'seoable_id',
        'title',
        'description',
        'keywords',
        'og_image',
        'canonical_url',
        'no_index',
        'no_follow',
    ];

    protected $casts = [
        'no_index' => 'boolean',
        'no_follow' => 'boolean',
    ];

    /**
     * Get the parent seoable model.
     */
    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get robots meta content.
     */
    public function getRobotsAttribute(): string
    {
        $robots = [];

        if ($this->no_index) {
            $robots[] = 'noindex';
        } else {
            $robots[] = 'index';
        }

        if ($this->no_follow) {
            $robots[] = 'nofollow';
        } else {
            $robots[] = 'follow';
        }

        return implode(', ', $robots);
    }
}
