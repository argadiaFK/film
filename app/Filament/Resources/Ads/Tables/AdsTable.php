<?php

namespace App\Filament\Resources\Ads\Tables;

use App\Models\Ad;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AdsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slot')
                    ->badge()
                    ->formatStateUsing(fn($state) => Ad::$slots[$state] ?? $state),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'script' => 'info',
                        'image' => 'success',
                        'html' => 'warning',
                    }),
                TextColumn::make('impressions')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('clicks')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ctr')
                    ->label('CTR')
                    ->suffix('%')
                    ->color(fn($state) => $state > 1 ? 'success' : 'gray'),
                TextColumn::make('start_date')
                    ->date()
                    ->placeholder('-'),
                TextColumn::make('end_date')
                    ->date()
                    ->placeholder('-'),
                ToggleColumn::make('is_active')
                    ->label('Active'),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('slot')
                    ->options(Ad::$slots),
                SelectFilter::make('type')
                    ->options([
                        'script' => 'Script',
                        'image' => 'Image',
                        'html' => 'HTML',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive'),
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
