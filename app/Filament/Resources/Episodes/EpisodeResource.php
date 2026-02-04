<?php

namespace App\Filament\Resources\Episodes;

use App\Filament\Resources\Episodes\Pages\ListEpisodes;
use App\Filament\Resources\Episodes\Pages\CreateEpisode;
use App\Filament\Resources\Episodes\Pages\EditEpisode;
use App\Filament\Resources\Episodes\Schemas\EpisodeForm;
use App\Filament\Resources\Episodes\Tables\EpisodesTable;
use App\Models\Episode;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class EpisodeResource extends Resource
{
    protected static ?string $model = Episode::class;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-play-circle';
    }

    public static function getNavigationGroup(): string
    {
        return 'Content';
    }

    public static function getNavigationSort(): int
    {
        return 3;
    }

    public static function getNavigationParentItem(): ?string
    {
        return 'Series';
    }

    public static function form(Schema $schema): Schema
    {
        return EpisodeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EpisodesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEpisodes::route('/'),
            'create' => CreateEpisode::route('/create'),
            'edit' => EditEpisode::route('/{record}/edit'),
        ];
    }
}
