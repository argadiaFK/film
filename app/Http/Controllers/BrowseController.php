<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Series;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'all'); // film, series, all
        $genreSlug = $request->get('genre');
        $countrySlug = $request->get('country');
        $year = $request->get('year');
        $sort = $request->get('sort', 'latest');

        $films = collect();
        $series = collect();

        // Get films
        if ($type === 'all' || $type === 'film') {
            $filmQuery = Film::where('status', 'published')->with('genres');

            if ($genreSlug) {
                $filmQuery->whereHas('genres', fn($q) => $q->where('slug', $genreSlug));
            }
            if ($countrySlug) {
                $filmQuery->whereHas('countries', fn($q) => $q->where('code', $countrySlug));
            }
            if ($year) {
                $filmQuery->where('year', $year);
            }

            $filmQuery = match ($sort) {
                'title' => $filmQuery->orderBy('title'),
                'oldest' => $filmQuery->oldest(),
                'year_desc' => $filmQuery->orderByDesc('year')->latest(),
                'year_asc' => $filmQuery->orderBy('year')->latest(),
                default => $filmQuery->latest(),
            };

            $films = $filmQuery->paginate(24);
        }

        // Get series (uses different status)
        if ($type === 'all' || $type === 'series') {
            $seriesQuery = Series::whereIn('status', ['ongoing', 'completed'])->with(['genres', 'episodes']);

            if ($genreSlug) {
                $seriesQuery->whereHas('genres', fn($q) => $q->where('slug', $genreSlug));
            }
            if ($countrySlug) {
                $seriesQuery->whereHas('countries', fn($q) => $q->where('code', $countrySlug));
            }
            if ($year) {
                $seriesQuery->where('year', $year);
            }

            $seriesQuery = match ($sort) {
                'title' => $seriesQuery->orderBy('title'),
                'oldest' => $seriesQuery->oldest(),
                'year_desc' => $seriesQuery->orderByDesc('year')->latest(),
                'year_asc' => $seriesQuery->orderBy('year')->latest(),
                default => $seriesQuery->latest(),
            };

            $series = $type === 'series'
                ? $seriesQuery->paginate(24)
                : $seriesQuery->take(12)->get();
        }

        $genres = Genre::orderBy('name')->get();
        $countries = Country::orderBy('name')->get();
        $years = range(date('Y'), 2000);

        return view('pages.browse', compact(
            'films',
            'series',
            'genres',
            'countries',
            'years',
            'type',
            'genreSlug',
            'countrySlug',
            'year',
            'sort'
        ));
    }
}
