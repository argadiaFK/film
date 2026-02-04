<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingComments extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Comment::query()->pending()->latest()->limit(5))
            ->columns([
                TextColumn::make('film.title')
                    ->label('Film')
                    ->limit(30),
                TextColumn::make('author')
                    ->getStateUsing(fn($record) => $record->author),
                TextColumn::make('content')
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Action::make('approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn($record) => $record->update(['status' => 'approved'])),
                Action::make('spam')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->action(fn($record) => $record->update(['status' => 'spam'])),
            ])
            ->paginated(false)
            ->emptyStateHeading('No pending comments')
            ->emptyStateDescription('All comments have been moderated.');
    }
}
