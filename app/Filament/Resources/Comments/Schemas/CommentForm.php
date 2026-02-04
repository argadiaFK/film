<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Comment Details')
                    ->schema([
                        Placeholder::make('film')
                            ->label('Film')
                            ->content(fn($record) => $record->film?->title ?? '-'),
                        Placeholder::make('author')
                            ->label('Author')
                            ->content(fn($record) => $record->author),
                        Placeholder::make('created_at')
                            ->label('Posted At')
                            ->content(fn($record) => $record->created_at?->format('d M Y H:i')),
                        Textarea::make('content')
                            ->label('Comment')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columns(3),

                Section::make('Moderation')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'spam' => 'Spam',
                            ])
                            ->required(),
                    ]),
            ]);
    }
}
