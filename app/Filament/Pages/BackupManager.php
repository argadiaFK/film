<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class BackupManager extends Page
{
    protected static ?int $navigationSort = 20;

    public function getView(): string
    {
        return 'filament.pages.backup-manager';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-archive-box';
    }

    public static function getNavigationGroup(): string
    {
        return 'Security';
    }

    public static function getNavigationLabel(): string
    {
        return 'Backup';
    }

    public function getTitle(): string
    {
        return 'Database Backup';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_backup')
                ->label('Create Backup')
                ->icon('heroicon-o-plus')
                ->color('warning')
                ->action(fn() => $this->createBackup()),
        ];
    }

    public function getBackupsList(): array
    {
        $backupPath = storage_path('app/backups');

        if (!File::isDirectory($backupPath)) {
            return [];
        }

        return collect(File::files($backupPath))
            ->filter(fn($file) => str_ends_with($file->getFilename(), '.sql'))
            ->map(fn($file) => [
                'name' => $file->getFilename(),
                'size' => $this->formatBytes($file->getSize()),
                'date' => date('Y-m-d H:i:s', $file->getMTime()),
            ])
            ->sortByDesc('date')
            ->values()
            ->toArray();
    }

    public function createBackup(): void
    {
        try {
            $exitCode = Artisan::call('backup:database');

            if ($exitCode === 0) {
                Notification::make()
                    ->title('Backup created successfully')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Backup failed')
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Backup failed: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteBackup(string $filename): void
    {
        $path = storage_path('app/backups/' . $filename);

        if (File::exists($path)) {
            File::delete($path);

            Notification::make()
                ->title('Backup deleted')
                ->success()
                ->send();
        }
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB'];
        $pow = floor(log($bytes, 1024));
        return round($bytes / (1024 ** $pow), 2) . ' ' . $units[$pow];
    }
}
