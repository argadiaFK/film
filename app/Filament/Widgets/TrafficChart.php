<?php

namespace App\Filament\Widgets;

use App\Models\Film;
use App\Models\Comment;
use App\Models\DownloadLink;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class TrafficChart extends ChartWidget
{
    protected ?string $heading = 'Download Tren (7 Hari Terakhir)';
    protected static ?int $sort = 5;
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $days = collect();
        $downloadData = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days->push($date->translatedFormat('D, d M'));

            $downloadData->push(
                DownloadLink::whereDate('updated_at', $date->toDateString())
                    ->sum('click_count')
            );
        }

        return [
            'datasets' => [
                [
                    'label' => 'Downloads',
                    'data' => $downloadData->toArray(),
                    'backgroundColor' => 'rgba(168, 85, 247, 0.2)',
                    'borderColor' => 'rgba(168, 85, 247, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.3,
                    'pointBackgroundColor' => 'rgba(168, 85, 247, 1)',
                    'pointRadius' => 4,
                ],
            ],
            'labels' => $days->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
