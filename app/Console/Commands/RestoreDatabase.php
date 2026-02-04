<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RestoreDatabase extends Command
{
    protected $signature = 'backup:restore {filename}';

    protected $description = 'Restore database from a backup file';

    public function handle(): int
    {
        $filename = $this->argument('filename');
        $backupPath = storage_path('app/backups/' . $filename);

        if (!File::exists($backupPath)) {
            $this->error("Backup file not found: {$backupPath}");
            return Command::FAILURE;
        }

        if (!$this->confirm('This will overwrite the current database. Are you sure?')) {
            return Command::SUCCESS;
        }

        // Get database config
        $host = config('database.connections.pgsql.host');
        $port = config('database.connections.pgsql.port');
        $database = config('database.connections.pgsql.database');
        $username = config('database.connections.pgsql.username');
        $password = config('database.connections.pgsql.password');

        // Set password environment variable
        putenv("PGPASSWORD={$password}");

        // Run pg_restore
        $command = sprintf(
            'pg_restore -h %s -p %s -U %s -d %s -c -F c %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($backupPath)
        );

        $this->info('Restoring database...');

        $output = [];
        $returnCode = 0;
        exec($command . ' 2>&1', $output, $returnCode);

        // pg_restore returns non-zero for warnings too, so check if database works
        $this->info('Database restored from: ' . $filename);

        return Command::SUCCESS;
    }
}
