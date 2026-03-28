<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\DownloadLink;
use App\Models\Film;
use App\Models\Series;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Generate sparkline data for films (last 6 months)
        $filmChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $filmChart[] = Film::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // Generate sparkline data for downloads (last 7 days)
        $downloadChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $downloadChart[] = max(1, DownloadLink::whereDate('updated_at', Carbon::now()->subDays($i)->toDateString())
                ->sum('click_count'));
        }

        // Generate sparkline data for comments (last 6 months)
        $commentChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $commentChart[] = Comment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // Generate sparkline data for users (last 6 months)
        $userChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $userChart[] = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        $totalFilms = Film::count();
        $totalSeries = Series::count();

        return [
            Stat::make('Total Konten', $totalFilms + $totalSeries)
                ->description('Film: ' . $totalFilms . ' · Series: ' . $totalSeries)
                ->descriptionIcon('heroicon-m-film')
                ->color('primary')
                ->chart($filmChart),

            Stat::make('Total Downloads', number_format(DownloadLink::sum('click_count')))
                ->description('Dari ' . DownloadLink::count() . ' link')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('success')
                ->chart($downloadChart),

            Stat::make('Komentar', Comment::count())
                ->description('Pending: ' . Comment::pending()->count())
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('warning')
                ->chart($commentChart),

            Stat::make('Users', User::count())
                ->description('Pengguna terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->chart($userChart),
        ];
    }
}
