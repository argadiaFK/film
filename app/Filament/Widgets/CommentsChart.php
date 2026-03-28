<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class CommentsChart extends ChartWidget
{
    protected ?string $heading = 'Komentar per Bulan';
    protected static ?int $sort = 4;
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $months = collect();
        $approvedData = collect();
        $pendingData = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->translatedFormat('M Y'));

            $approvedData->push(
                Comment::where('status', 'approved')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count()
            );

            $pendingData->push(
                Comment::where('status', 'pending')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count()
            );
        }

        return [
            'datasets' => [
                [
                    'label' => 'Approved',
                    'data' => $approvedData->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.6)',
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Pending',
                    'data' => $pendingData->toArray(),
                    'backgroundColor' => 'rgba(234, 179, 8, 0.6)',
                    'borderColor' => 'rgba(234, 179, 8, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
