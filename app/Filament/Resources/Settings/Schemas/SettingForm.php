<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                    ->helperText('Unique key (e.g. site_name, enable_comments)')
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
                    ])
                    ->default('general')
                    ->required()
                    ->columnSpan(1),
                Textarea::make('value')
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }
}
