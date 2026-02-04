<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database {--filename=}';

    protected $description = 'Create a backup of the database using plain SQL format';

    public function handle(): int
    {
        $filename = $this->option('filename') ?? 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $backupPath = storage_path('app/backups');

        // Ensure backup directory exists
        if (!File::isDirectory($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $fullPath = $backupPath . '/' . $filename;

        // Get database config
        $host = config('database.connections.pgsql.host');
        $port = config('database.connections.pgsql.port');
        $database = config('database.connections.pgsql.database');
        $username = config('database.connections.pgsql.username');
        $password = config('database.connections.pgsql.password');

        // Set password environment variable
        putenv("PGPASSWORD={$password}");

        // Use plain text format (-F p) which is compatible across versions
        $command = sprintf(
            'pg_dump -h %s -p %s -U %s -d %s --no-owner --no-acl -F p -f %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($fullPath)
        );

        $this->info('Creating database backup...');

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        // Check if backup was created even with warnings
        if (File::exists($fullPath) && File::size($fullPath) > 0) {
            $this->info("Backup created: {$fullPath}");
            $this->info('Size: ' . $this->formatBytes(filesize($fullPath)));

            // Clean old backups (keep last 10)
            $this->cleanOldBackups($backupPath, 10);

            return Command::SUCCESS;
        }

        if ($returnCode !== 0) {
            $this->error('Backup failed: ' . implode("\n", $output));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    protected function cleanOldBackups(string $path, int $keep): void
    {
        $files = collect(File::files($path))
            ->filter(fn($file) => str_ends_with($file->getFilename(), '.sql'))
            ->sortByDesc(fn($file) => $file->getMTime());

        if ($files->count() > $keep) {
            $toDelete = $files->slice($keep);
            foreach ($toDelete as $file) {
                File::delete($file->getPathname());
                $this->info('Deleted old backup: ' . $file->getFilename());
            }
        }
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes === 0)
            return '0 B';
        $units = ['B', 'KB', 'MB', 'GB'];
        $pow = floor(log($bytes, 1024));
        return round($bytes / (1024 ** $pow), 2) . ' ' . $units[$pow];
    }
}
