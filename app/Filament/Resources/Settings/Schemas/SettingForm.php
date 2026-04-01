<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Unique key (e.g. site_logo, site_name, enable_comments)')
                    ->live()
                    ->columnSpan(1),
                Select::make('type')
                    ->options([
                        'string' => 'String',
                        'boolean' => 'Boolean',
                        'integer' => 'Integer',
                        'json' => 'JSON',
                    ])
                    ->default('string')
                    ->required()
                    ->columnSpan(1),
                Select::make('group')
                    ->options([
                        'general' => 'General',
                        'comments' => 'Comments',
                        'seo' => 'SEO',
                        'analytics' => 'Analytics',
                        'footer' => 'Footer',
                        'social' => 'Social Media',
                        'scripts' => 'Scripts',
                        'donation' => 'Donation',
                    ])
                    ->default('general')
                    ->required()
                    ->columnSpan(1),
                Grid::make(1)
                    ->schema(function (?\App\Models\Setting $record) {
                        if ($record && in_array($record->key, ['site_logo', 'site_favicon', 'backdrop', 'poster'])) {
                            return [
                                FileUpload::make('value')
                                    ->image()
                                    ->disk('public')
                                    ->directory('settings')
                                    ->helperText('Upload an image file. Leave empty to use default.')
                                    ->columnSpanFull(),
                            ];
                        }

                        return [
                            Textarea::make('value')
                                ->rows(3)
                                ->columnSpanFull(),
                        ];
                    })
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }
}
