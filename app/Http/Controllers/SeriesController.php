<?php

namespace App\Http\Controllers;

use App\Models\Series;
use App\Models\Episode;

class SeriesController extends Controller
{
    public function show(string $slug)
    {
        $series = Series::where('slug', $slug)
            ->whereIn('status', ['ongoing', 'completed'])
            ->with([
                'genres',
                'countries',
                'episodes' => fn($q) => $q->orderBy('season_number')->orderBy('episode_number'),
                'episodes.streamingSources',
                'episodes.downloadLinks',
                'seoMeta',
                'comments' => fn($q) => $q->where('status', 'approved')->with('user.roles')->latest(),
            ])
            ->firstOrFail();

        // Group episodes by season_number
        $seasons = $series->episodes->groupBy('season_number');

        // Related series
        $relatedSeries = Series::whereIn('status', ['ongoing', 'completed'])
            ->where('id', '!=', $series->id)
            ->whereHas('genres', function ($q) use ($series) {
                $q->whereIn('genres.id', $series->genres->pluck('id'));
            })
            ->with('genres', 'episodes')
            ->take(6)
            ->get();

        return view('pages.series-detail', compact('series', 'seasons', 'relatedSeries'));
    }

    public function episode(string $seriesSlug, int $season, int $episode)
    {
        $series = Series::where('slug', $seriesSlug)
            ->whereIn('status', ['ongoing', 'completed'])
            ->with('genres')
            ->firstOrFail();

        $currentEpisode = Episode::where('series_id', $series->id)
            ->where('season_number', $season)
            ->where('episode_number', $episode)
            ->with(['streamingSources', 'downloadLinks'])
            ->firstOrFail();

        $episodes = Episode::where('series_id', $series->id)
            ->orderBy('season_number')
            ->orderBy('episode_number')
            ->get();

        return view('pages.episode-detail', compact('series', 'currentEpisode', 'episodes'));
    }
}
