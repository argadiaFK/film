<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Series;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return view('pages.search', [
                'query' => $query,
                'films' => collect(),
                'series' => collect(),
            ]);
        }

        $films = Film::where('status', 'published')
            ->where(function ($q) use ($query) {
                $q->where('title', 'ilike', "%{$query}%")
                    ->orWhere('synopsis', 'ilike', "%{$query}%");
            })
            ->with('genres')
            ->take(20)
            ->get();

        $series = Series::whereIn('status', ['ongoing', 'completed'])
            ->where(function ($q) use ($query) {
                $q->where('title', 'ilike', "%{$query}%")
                    ->orWhere('synopsis', 'ilike', "%{$query}%");
            })
            ->with(['genres', 'episodes'])
            ->take(20)
            ->get();

        return view('pages.search', compact('query', 'films', 'series'));
    }
}
