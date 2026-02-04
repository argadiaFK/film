<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Country extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * Get the films for the country.
     */
    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class, 'film_country');
    }

    /**
     * Get the films count.
     */
    public function getFilmsCountAttribute(): int
    {
        return $this->films()->count();
    }
}
