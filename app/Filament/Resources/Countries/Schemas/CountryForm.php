<?php

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CountryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->required()
                    ->maxLength(3)
                    ->unique(ignoreRecord: true)
                    ->helperText('ISO 3166-1 alpha-2 or alpha-3 code (e.g. US, ID, JP)'),
            ]);
    }
}
