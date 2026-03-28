<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Film;
use App\Models\Series;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = collect();

        // Search films
        $films = Film::where('status', 'published')
            ->where(function ($q) use ($query) {
                $q->where('title', 'ilike', "%{$query}%")
                    ->orWhere('synopsis', 'ilike', "%{$query}%");
            })
            ->select('id', 'title', 'slug', 'year', 'poster')
            ->take(5)
            ->get()
            ->map(function ($film) {
                return [
                    'id' => $film->id,
                    'title' => $film->title,
                    'slug' => $film->slug,
                    'year' => $film->year,
                    'poster' => $film->poster,
                    'type' => 'film',
                ];
            });

        // Search series
        $series = Series::whereIn('status', ['ongoing', 'completed'])
            ->where(function ($q) use ($query) {
                $q->where('title', 'ilike', "%{$query}%")
                    ->orWhere('synopsis', 'ilike', "%{$query}%");
            })
            ->select('id', 'title', 'slug', 'year', 'poster')
            ->take(5)
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'title' => $s->title,
                    'slug' => $s->slug,
                    'year' => $s->year,
                    'poster' => $s->poster,
                    'type' => 'series',
                ];
            });

        $results = $films->merge($series)->take(8);

        return response()->json($results);
    }
}
