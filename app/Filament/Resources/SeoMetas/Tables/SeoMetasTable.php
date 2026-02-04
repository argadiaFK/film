<?php

namespace App\Filament\Resources\SeoMetas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SeoMetasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('seoable_type')
                    ->label('Type')
                    ->formatStateUsing(fn($state) => class_basename($state))
                    ->badge()
                    ->sortable(),
                TextColumn::make('seoable.title')
                    ->label('Item')
                    ->default(fn($record) => $record->seoable?->name ?? '-')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('title')
                    ->label('SEO Title')
                    ->limit(30)
                    ->default('Auto-generated')
                    ->color(fn($state) => $state ? null : 'gray'),
                IconColumn::make('no_index')
                    ->label('No Index')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('seoable_type')
                    ->label('Type')
                    ->options([
                        'App\\Models\\Film' => 'Film',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }
}
