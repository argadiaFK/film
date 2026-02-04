<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\DownloadLink;
use App\Models\Film;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Films', Film::count())
                ->description('Published: ' . Film::published()->count())
                ->descriptionIcon('heroicon-m-film')
                ->color('primary'),

            Stat::make('Total Downloads', DownloadLink::sum('click_count'))
                ->description('From ' . DownloadLink::count() . ' links')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('success'),

            Stat::make('Pending Comments', Comment::pending()->count())
                ->description('Total: ' . Comment::count())
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('warning'),

            Stat::make('Users', User::count())
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}
