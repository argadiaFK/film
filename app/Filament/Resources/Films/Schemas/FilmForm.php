<?php

namespace App\Filament\Resources\Films\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class FilmForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Film')
                    ->tabs([
                        Tabs\Tab::make('Basic Info')
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(
                                        fn($state, callable $set) =>
                                        $set('slug', Str::slug($state))
                                    ),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Textarea::make('synopsis')
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('year')
                                            ->numeric()
                                            ->minValue(1900)
                                            ->maxValue(date('Y') + 5),
                                        TextInput::make('duration_minutes')
                                            ->label('Duration (minutes)')
                                            ->numeric()
                                            ->minValue(1),
                                        Select::make('status')
                                            ->options([
                                                'draft' => 'Draft',
                                                'published' => 'Published',
                                                'archived' => 'Archived',
                                            ])
                                            ->default('draft')
                                            ->required(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Media')
                            ->schema([
                                FileUpload::make('poster')
                                    ->label('Poster (Portrait)')
                                    ->image()
                                    ->directory('films/posters')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('2:3')
                                    ->helperText('Ukuran: 300x450px'),
                                FileUpload::make('backdrop')
                                    ->label('Backdrop (Landscape)')
                                    ->image()
                                    ->directory('films/backdrops')
                                    ->helperText('Untuk slider/hero. Ukuran: 1920x1080px'),
                                TextInput::make('trailer_url')
                                    ->label('Trailer URL')
                                    ->url()
                                    ->placeholder('https://youtube.com/watch?v=...')
                                    ->columnSpanFull(),
                                Toggle::make('is_featured')
                                    ->label('Featured on Homepage')
                                    ->helperText('Tampilkan di slider homepage')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Tabs\Tab::make('Taxonomies')
                            ->schema([
                                Select::make('genres')
                                    ->relationship('genres', 'name')
                                    ->multiple()
                                    ->preload(),
                                Select::make('countries')
                                    ->relationship('countries', 'name')
                                    ->multiple()
                                    ->preload(),
                            ]),

                        Tabs\Tab::make('Streaming Sources')
                            ->schema([
                                Repeater::make('streamingSources')
                                    ->relationship()
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Server Name')
                                            ->required()
                                            ->placeholder('e.g. Server 1, Google Drive'),
                                        Textarea::make('url')
                                            ->label('Embed URL')
                                            ->required()
                                            ->rows(2),
                                        Select::make('type')
                                            ->options([
                                                'embed' => 'Embed',
                                                'iframe' => 'iFrame',
                                                'direct' => 'Direct Link',
                                            ])
                                            ->default('embed'),
                                        Toggle::make('is_active')
                                            ->default(true),
                                        Hidden::make('sort_order'),
                                    ])
                                    ->orderColumn('sort_order')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
                                    ->addActionLabel('Add Streaming Source')
                                    ->columns(2),
                            ]),

                        Tabs\Tab::make('Download Links')
                            ->schema([
                                Repeater::make('downloadLinks')
                                    ->relationship()
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Link Name')
                                            ->required()
                                            ->placeholder('e.g. Google Drive 720p'),
                                        Textarea::make('url')
                                            ->label('Download URL')
                                            ->required()
                                            ->rows(2),
                                        Select::make('quality')
                                            ->options([
                                                '360p' => '360p',
                                                '480p' => '480p',
                                                '720p' => '720p',
                                                '1080p' => '1080p',
                                                '2160p' => '4K (2160p)',
                                            ]),
                                        TextInput::make('size')
                                            ->placeholder('e.g. 1.2 GB'),
                                        Toggle::make('is_active')
                                            ->default(true),
                                        Placeholder::make('click_count')
                                            ->label('Total Clicks')
                                            ->content(fn($record) => $record?->click_count ?? 0)
                                            ->visibleOn('edit'),
                                    ])
                                    ->collapsible()
                                    ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
                                    ->addActionLabel('Add Download Link')
                                    ->columns(2),
                            ]),

                        Tabs\Tab::make('SEO')
                            ->schema([
                                Placeholder::make('seo_info')
                                    ->label('Auto-Generated SEO')
                                    ->content('SEO metadata is automatically generated from film title, synopsis, genres, and countries. Leave fields empty to use auto-generated values, or fill in to override.')
                                    ->columnSpanFull(),
                                Fieldset::make('SEO Override (Optional)')
                                    ->relationship('seoMeta')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Custom SEO Title')
                                            ->maxLength(60)
                                            ->placeholder('Leave empty for auto-generated'),
                                        Textarea::make('description')
                                            ->label('Custom Meta Description')
                                            ->maxLength(160)
                                            ->rows(2)
                                            ->placeholder('Leave empty for auto-generated'),
                                        Grid::make(2)
                                            ->schema([
                                                Toggle::make('no_index')
                                                    ->label('No Index'),
                                                Toggle::make('no_follow')
                                                    ->label('No Follow'),
                                            ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
