<?php

namespace App\Filament\Resources\Comments\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('target')
                    ->label('Target')
                    ->getStateUsing(fn($record) => $record->film ? ('Film: ' . $record->film->title) : ($record->episode ? ('Episode: ' . $record->episode->full_title) : '-'))
                    ->wrap()
                    ->limit(40),
                TextColumn::make('author')
                    ->label('Author')
                    ->getStateUsing(fn($record) => $record->author)
                    ->searchable(query: function ($query, $search) {
                        $query->where('author_name', 'like', "%{$search}%")
                            ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
                    }),
                TextColumn::make('content')
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'spam' => 'danger',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'spam' => 'Spam',
                    ]),
                SelectFilter::make('film')
                    ->relationship('film', 'title')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('episode')
                    ->relationship('episode', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn(Collection $records) => $records->each->update(['status' => 'approved']))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('spam')
                        ->label('Mark as Spam')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(fn(Collection $records) => $records->each->update(['status' => 'spam']))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
