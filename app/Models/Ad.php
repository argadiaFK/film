<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class Ad extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'slot',
        'type',
        'content',
        'link',
        'target',
        'start_date',
        'end_date',
        'impressions',
        'clicks',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'impressions' => 'integer',
        'clicks' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Available ad slots.
     */
    public static array $slots = [
        'header_banner' => 'Header Banner',
        'sidebar_top' => 'Sidebar Top',
        'sidebar_bottom' => 'Sidebar Bottom',
        'before_player' => 'Before Player',
        'after_player' => 'After Player',
        'between_episodes' => 'Between Episodes',
        'before_download' => 'Before Download',
        'after_download' => 'After Download',
        'popup' => 'Pop-up',
        'footer' => 'Footer',
    ];

    /**
     * Get active ads for a specific slot.
     */
    public static function getBySlot(string $slot): ?HtmlString
    {
        $ad = static::query()
            ->where('slot', $slot)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->orderBy('sort_order')
            ->first();

        if (!$ad) {
            return null;
        }

        // Increment impressions
        $ad->increment('impressions');

        return $ad->render();
    }

    /**
     * Render the ad HTML.
     */
    public function render(): HtmlString
    {
        $html = match ($this->type) {
            'image' => $this->renderImage(),
            'script' => $this->content,
            'html' => $this->content,
            default => '',
        };

        return new HtmlString($html);
    }

    /**
     * Render image ad with tracking link.
     */
    protected function renderImage(): string
    {
        $img = sprintf(
            '<img src="%s" alt="%s" class="ad-image" loading="lazy">',
            e($this->content),
            e($this->name)
        );

        if ($this->link) {
            return sprintf(
                '<a href="%s" target="%s" class="ad-link" data-ad-id="%s" onclick="trackAdClick(\'%s\')">%s</a>',
                e(route('ad.click', $this->id)),
                e($this->target),
                $this->id,
                $this->id,
                $img
            );
        }

        return $img;
    }

    /**
     * Track a click.
     */
    public function trackClick(): void
    {
        $this->increment('clicks');
    }

    /**
     * Get CTR (Click Through Rate).
     */
    public function getCtrAttribute(): float
    {
        if ($this->impressions === 0) {
            return 0;
        }

        return round(($this->clicks / $this->impressions) * 100, 2);
    }

    /**
     * Scope for active ads.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            });
    }
}
