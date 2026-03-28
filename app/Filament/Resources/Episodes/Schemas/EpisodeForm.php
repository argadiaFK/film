<?php

namespace App\Filament\Resources\Episodes\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EpisodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Episode Information')
                    ->schema([
                        Select::make('series_id')
                            ->label('Series')
                            ->relationship('series', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(2),
                        TextInput::make('season_number')
                            ->label('Season')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        TextInput::make('episode_number')
                            ->label('Episode #')
                            ->numeric()
                            ->required(),
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        TextInput::make('slug')
                            ->maxLength(255)
                            ->helperText('Leave empty to auto-generate'),
                        TextInput::make('duration_minutes')
                            ->label('Duration (min)')
                            ->numeric()
                            ->placeholder('45'),
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                            ])
                            ->default('draft')
                            ->required(),
                    ])->columns(4),

                Section::make('Content')
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->image()
                            ->disk('public')
                            ->directory('episodes/thumbnails')
                            ->helperText('Thumbnail episode'),
                        Textarea::make('synopsis')
                            ->rows(3),
                    ])->columns(2),

                Section::make('Streaming Sources')
                    ->collapsible()
                    ->schema([
                        Repeater::make('streamingSources')
                            ->relationship()
                            ->label('')
                            ->schema([
                                TextInput::make('name')
                                    ->placeholder('Server 1')
                                    ->required(),
                                TextInput::make('url')
                                    ->url()
                                    ->placeholder('https://...')
                                    ->required()
                                    ->columnSpan(2),
                                Select::make('type')
                                    ->options([
                                        'embed' => 'Embed',
                                        'hls' => 'HLS',
                                        'direct' => 'Direct',
                                    ])
                                    ->default('embed'),
                                Toggle::make('is_active')
                                    ->default(true)
                                    ->inline(false),
                            ])
                            ->columns(5)
                            ->defaultItems(0)
                            ->addActionLabel('+ Add Source')
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['name'] ?? 'New Source'),
                    ]),

                Section::make('Download Links')
                    ->collapsible()
                    ->schema([
                        Repeater::make('downloadLinks')
                            ->relationship()
                            ->label('')
                            ->schema([
                                TextInput::make('name')
                                    ->placeholder('Google Drive')
                                    ->required(),
                                TextInput::make('url')
                                    ->url()
                                    ->placeholder('https://...')
                                    ->required()
                                    ->columnSpan(2),
                                TextInput::make('quality')
                                    ->placeholder('720p'),
                                TextInput::make('size')
                                    ->placeholder('500 MB'),
                                Toggle::make('is_active')
                                    ->default(true)
                                    ->inline(false),
                            ])
                            ->columns(6)
                            ->defaultItems(0)
                            ->addActionLabel('+ Add Download')
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['name'] ?? 'New Link'),
                    ]),
            ]);
    }
}
