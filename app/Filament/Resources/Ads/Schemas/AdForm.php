<?php

namespace App\Filament\Resources\Ads\Schemas;

use App\Models\Ad;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ad Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Google Adsense - Sidebar'),
                        Select::make('slot')
                            ->options(Ad::$slots)
                            ->required()
                            ->searchable(),
                        Select::make('type')
                            ->options([
                                'script' => 'Script (Google Ads, etc)',
                                'image' => 'Image Banner',
                                'html' => 'Custom HTML',
                            ])
                            ->default('script')
                            ->required()
                            ->live(),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower = higher priority'),
                    ])->columns(2),

                Section::make('Ad Content')
                    ->schema([
                        Textarea::make('content')
                            ->label(fn($get) => match ($get('type')) {
                                'image' => 'Image URL',
                                'script' => 'Ad Script',
                                'html' => 'HTML Code',
                                default => 'Content',
                            })
                            ->required()
                            ->rows(5)
                            ->placeholder(fn($get) => match ($get('type')) {
                                'image' => 'https://example.com/banner.jpg',
                                'script' => '<script async src="..."></script>',
                                'html' => '<div class="ad">...</div>',
                                default => '',
                            })
                            ->columnSpanFull(),
                        TextInput::make('link')
                            ->label('Click URL')
                            ->url()
                            ->placeholder('https://example.com/landing-page')
                            ->helperText('Only for image ads')
                            ->visible(fn($get) => $get('type') === 'image'),
                        Select::make('target')
                            ->options([
                                '_blank' => 'New Tab',
                                '_self' => 'Same Tab',
                            ])
                            ->default('_blank')
                            ->visible(fn($get) => $get('type') === 'image'),
                    ]),

                Section::make('Schedule')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->placeholder('Leave empty for immediate'),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->placeholder('Leave empty for no end'),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])->columns(3),
            ]);
    }
}
