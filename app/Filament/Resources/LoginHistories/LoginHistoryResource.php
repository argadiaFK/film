<?php

namespace App\Filament\Resources\LoginHistories;

use App\Filament\Resources\LoginHistories\Pages\ListLoginHistories;
use App\Filament\Resources\LoginHistories\Tables\LoginHistoriesTable;
use App\Models\LoginHistory;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class LoginHistoryResource extends Resource
{
    protected static ?string $model = LoginHistory::class;

    protected static ?string $recordTitleAttribute = 'ip_address';

    protected static ?string $navigationLabel = 'Login History';

    protected static ?string $modelLabel = 'Login';

    protected static ?string $pluralModelLabel = 'Login History';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-finger-print';
    }

    public static function getNavigationGroup(): string
    {
        return 'Security';
    }

    public static function getNavigationSort(): int
    {
        return 5;
    }

    public static function table(Table $table): Table
    {
        return LoginHistoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoginHistories::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
