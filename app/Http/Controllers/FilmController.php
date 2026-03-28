<?php

namespace App\Http\Controllers;

use App\Models\Film;

class FilmController extends Controller
{
    public function show(string $slug)
    {
        $film = Film::where('slug', $slug)
            ->where('status', 'published')
            ->with([
                'genres',
                'countries',
                'streamingSources',
                'downloadLinks',
                'seoMeta',
                'comments' => fn($q) => $q->where('status', 'approved')->with('user.roles')->latest(),
            ])
            ->firstOrFail();

        // Related films by genre
        $relatedFilms = Film::where('status', 'published')
            ->where('id', '!=', $film->id)
            ->whereHas('genres', function ($q) use ($film) {
                $q->whereIn('genres.id', $film->genres->pluck('id'));
            })
            ->with('genres')
            ->take(6)
            ->get();

        return view('pages.film-detail', compact('film', 'relatedFilms'));
    }
}
