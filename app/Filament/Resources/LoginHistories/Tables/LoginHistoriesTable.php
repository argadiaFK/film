<?php

namespace App\Filament\Resources\LoginHistories\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LoginHistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('login_at')
                    ->label('Time')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                    }),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable(),
                TextColumn::make('browser')
                    ->label('Browser'),
                TextColumn::make('platform')
                    ->label('OS'),
                TextColumn::make('device')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('logout_at')
                    ->label('Logout')
                    ->dateTime('H:i')
                    ->placeholder('-'),
            ])
            ->defaultSort('login_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'success' => 'Success',
                        'failed' => 'Failed',
                    ]),
                SelectFilter::make('user')
                    ->relationship('user', 'name'),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
