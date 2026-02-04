<?php

namespace App\Filament\Resources\Episodes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EpisodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->circular()
                    ->size(40),
                TextColumn::make('series.title')
                    ->label('Series')
                    ->searchable()
                    ->sortable()
                    ->limit(20),
                TextColumn::make('episode_code')
                    ->label('Episode')
                    ->getStateUsing(fn($record) => $record->episode_code),
                TextColumn::make('title')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->suffix(' min'),
                TextColumn::make('air_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                    }),
                TextColumn::make('streaming_sources_count')
                    ->label('Sources')
                    ->counts('streamingSources'),
                TextColumn::make('download_links_count')
                    ->label('Downloads')
                    ->counts('downloadLinks'),
            ])
            ->defaultSort('series_id')
            ->filters([
                SelectFilter::make('series')
                    ->relationship('series', 'title')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
                SelectFilter::make('season_number')
                    ->label('Season')
                    ->options(fn() => collect(range(1, 20))->mapWithKeys(fn($s) => [$s => "Season $s"])),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
