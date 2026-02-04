<?php

namespace App\Filament\Resources\SeoMetas\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SeoMetaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('SEO Information')
                    ->schema([
                        Placeholder::make('seoable_type')
                            ->label('Type')
                            ->content(fn($record) => class_basename($record->seoable_type)),
                        Placeholder::make('seoable_id')
                            ->label('Item')
                            ->content(fn($record) => $record->seoable?->title ?? $record->seoable?->name ?? $record->seoable_id),
                    ])->columns(2),

                Section::make('Meta Tags')
                    ->schema([
                        TextInput::make('title')
                            ->label('SEO Title')
                            ->maxLength(60)
                            ->helperText('Leave empty to auto-generate from content'),
                        Textarea::make('description')
                            ->label('Meta Description')
                            ->maxLength(160)
                            ->rows(3)
                            ->helperText('Leave empty to auto-generate from content'),
                        TextInput::make('keywords')
                            ->maxLength(255)
                            ->helperText('Leave empty to auto-generate from content'),
                    ]),

                Section::make('Open Graph')
                    ->schema([
                        FileUpload::make('og_image')
                            ->label('OG Image')
                            ->image()
                            ->directory('seo/og-images'),
                        TextInput::make('canonical_url')
                            ->url()
                            ->maxLength(255),
                    ]),

                Section::make('Indexing')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('no_index')
                                    ->label('No Index')
                                    ->helperText('Prevent search engines from indexing'),
                                Toggle::make('no_follow')
                                    ->label('No Follow')
                                    ->helperText('Prevent search engines from following links'),
                            ]),
                    ]),
            ]);
    }
}
