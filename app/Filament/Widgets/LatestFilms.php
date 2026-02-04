<?php

namespace App\Filament\Widgets;

use App\Models\Film;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestFilms extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Film::query()->latest()->limit(5))
            ->columns([
                ImageColumn::make('poster')
                    ->circular()
                    ->size(40),
                TextColumn::make('title')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('year'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                        'archived' => 'danger',
                    }),
                TextColumn::make('genres.name')
                    ->badge()
                    ->separator(',')
                    ->limit(3),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
