<?php

namespace App\Filament\Widgets;

use App\Models\Film;
use App\Models\Series;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ContentChart extends ChartWidget
{
    protected ?string $heading = 'Konten per Bulan';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $months = collect();
        $filmData = collect();
        $seriesData = collect();

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->translatedFormat('M Y'));

            $filmData->push(
                Film::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count()
            );

            $seriesData->push(
                Series::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count()
            );
        }

        return [
            'datasets' => [
                [
                    'label' => 'Film',
                    'data' => $filmData->toArray(),
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                    'borderColor' => 'rgba(245, 158, 11, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Series',
                    'data' => $seriesData->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
