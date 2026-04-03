<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Genre;
use App\Models\Series;

class HomeController extends Controller
{
    public function index()
    {
        // Featured films for slider
        $featuredFilms = Film::where('is_featured', true)
            ->where('status', 'published')
            ->with('genres')
            ->latest()
            ->take(5)
            ->get();

        // Featured series for slider
        $featuredSeries = Series::where('is_featured', true)
            ->whereIn('status', ['ongoing', 'completed'])
            ->with('genres')
            ->withCount('episodes')
            ->latest()
            ->take(5)
            ->get();

        // Combine featured content for slider
        $featuredContent = collect();
        foreach ($featuredFilms as $film) {
            $film->content_type = 'film';
            $featuredContent->push($film);
        }
        foreach ($featuredSeries as $series) {
            $series->content_type = 'series';
            $featuredContent->push($series);
        }
        // Shuffle and limit to 5
        $featuredContent = $featuredContent->shuffle()->take(5);

        $latestFilms = Film::where('status', 'published')
            ->with('genres')
            ->latest()
            ->take(12)
            ->get();

        $latestSeries = Series::whereIn('status', ['ongoing', 'completed'])
            ->with(['genres', 'episodes'])
            ->withCount('episodes')
            ->latest()
            ->take(12)
            ->get();

        $genres = Genre::withCount('films')
            ->orderByDesc('films_count')
            ->take(8)
            ->get();

        return view('pages.home', compact(
            'featuredContent',
            'latestFilms',
            'latestSeries',
            'genres'
        ));
    }
}
