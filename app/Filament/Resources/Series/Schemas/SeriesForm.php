<?php

namespace App\Filament\Resources\Series\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SeriesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->maxLength(255)
                            ->helperText('Leave empty to auto-generate'),
                        TextInput::make('year')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(2100)
                            ->placeholder(date('Y')),
                        TextInput::make('duration_minutes')
                            ->label('Duration per ep (min)')
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('total_seasons')
                            ->label('Total Seasons')
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                    ])->columns(2),

                Section::make('Synopsis')
                    ->schema([
                        Textarea::make('synopsis')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Media')
                    ->description('Poster untuk thumbnail, Backdrop untuk slider/hero section')
                    ->schema([
                        FileUpload::make('poster')
                            ->label('Poster (Portrait)')
                            ->image()
                            ->disk('public')
                            ->directory('series/posters')
                            ->helperText('Ukuran: 300x450px'),
                        FileUpload::make('backdrop')
                            ->label('Backdrop (Landscape)')
                            ->image()
                            ->disk('public')
                            ->directory('series/backdrops')
                            ->helperText('Untuk slider. Ukuran: 1920x1080px'),
                        TextInput::make('trailer_url')
                            ->label('Trailer URL')
                            ->url()
                            ->placeholder('https://youtube.com/watch?v=...')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Categories')
                    ->schema([
                        Select::make('genres')
                            ->multiple()
                            ->relationship('genres', 'name')
                            ->preload()
                            ->searchable(),
                        Select::make('countries')
                            ->multiple()
                            ->relationship('countries', 'name')
                            ->preload()
                            ->searchable(),
                    ])->columns(2),

                Section::make('Status & Visibility')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'ongoing' => 'Ongoing',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required(),
                        Toggle::make('is_featured')
                            ->label('Featured on Homepage')
                            ->helperText('Tampilkan di slider homepage'),
                    ])->columns(2),
            ]);
    }
}
