<?php

namespace App\Filament\Resources\SeoMetas;

use App\Filament\Resources\SeoMetas\Pages\ListSeoMetas;
use App\Filament\Resources\SeoMetas\Pages\EditSeoMeta;
use App\Filament\Resources\SeoMetas\Schemas\SeoMetaForm;
use App\Filament\Resources\SeoMetas\Tables\SeoMetasTable;
use App\Models\SeoMeta;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SeoMetaResource extends Resource
{
    protected static ?string $model = SeoMeta::class;

    protected static ?string $navigationLabel = 'SEO';

    protected static ?string $modelLabel = 'SEO Meta';

    protected static ?string $pluralModelLabel = 'SEO Metas';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-magnifying-glass';
    }

    public static function getNavigationGroup(): string
    {
        return 'Settings';
    }

    public static function getNavigationSort(): int
    {
        return 3;
    }

    public static function form(Schema $schema): Schema
    {
        return SeoMetaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SeoMetasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeoMetas::route('/'),
            'edit' => EditSeoMeta::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
