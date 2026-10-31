<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use ZipArchive;

class ChapelRestoreCommand extends Command
{
    protected $signature = 'chapel:restore {file : Path to the backup zip file} {--force : Force overwrite existing records}';
    protected $description = 'Restore Kwang-Lim Chapel database and Records Vault from a verified backup archive';

    public function handle(): int
    {
        $backupPath = $this->argument('file');

        if (!File::exists($backupPath)) {
            $this->error("Backup file not found at: {$backupPath}");
            return 1;
        }

        if (!$this->option('force') && !$this->confirm('This will overwrite current database records and vault files. Proceed?')) {
            $this->info('Restore aborted.');
            return 0;
        }

        $this->info("Verifying and extracting backup archive: {$backupPath}...");
        $zip = new ZipArchive();

        if ($zip->open($backupPath) !== true) {
            $this->error('Failed to open backup archive. File might be corrupted.');
            return 1;
        }

        $extractDir = storage_path('app/restore_temp');
        if (File::exists($extractDir)) {
            File::deleteDirectory($extractDir);
        }
        File::makeDirectory($extractDir, 0755, true);

        $zip->extractTo($extractDir);
        $zip->close();

        // 1. Restore Database (SQLite file, PostgreSQL custom dump or MySQL SQL dump)
        $dump = collect(File::glob("{$extractDir}/database/*"))->first();
        if ($dump && !$this->restoreDatabase($dump)) {
            File::deleteDirectory($extractDir);
            $this->error('Database restore failed; vault files were not touched.');
            return 1;
        }

        // 2. Restore Vault Files (private records and legacy public uploads)
        $restoredPrivate = "{$extractDir}/private_files";
        if (File::exists($restoredPrivate)) {
            File::copyDirectory($restoredPrivate, storage_path('app/private'));
            $this->line('✓ Private Records Vault files restored.');
        }

        $restoredVault = "{$extractDir}/vault_files";
        if (File::exists($restoredVault)) {
            File::copyDirectory($restoredVault, storage_path('app/public'));
            $this->line('✓ Legacy public document files restored.');
        }

        File::deleteDirectory($extractDir);

        $this->info('✓ Kwang-Lim Chapel Restoration successfully verified and completed.');
        return 0;
    }

    private function restoreDatabase(string $dump): bool
    {
        $db = config('database.connections.' . config('database.default'));
        $extension = pathinfo($dump, PATHINFO_EXTENSION);

        if ($db['driver'] === 'sqlite' && $extension === 'sqlite') {
            File::copy($dump, $db['database']);
            $this->line('✓ SQLite database restored.');
            return true;
        }

        $process = match (true) {
            $db['driver'] === 'pgsql' && $extension === 'pgdump' => new Process(
                ['pg_restore', '--host=' . $db['host'], '--port=' . $db['port'], '--username=' . $db['username'], '--dbname=' . $db['database'], '--clean', '--if-exists', '--no-owner', $dump],
                null,
                ['PGPASSWORD' => (string) $db['password']]
            ),
            in_array($db['driver'], ['mysql', 'mariadb'], true) && $extension === 'sql' => Process::fromShellCommandline(
                'mysql --host="$DB_HOST" --port="$DB_PORT" --user="$DB_USER" "$DB_NAME" < "$DUMP"',
                null,
                ['DB_HOST' => $db['host'], 'DB_PORT' => (string) $db['port'], 'DB_USER' => $db['username'], 'DB_NAME' => $db['database'], 'DUMP' => $dump, 'MYSQL_PWD' => (string) $db['password']]
            ),
            default => null,
        };

        if ($process === null) {
            $this->error("This backup's database file ({$extension}) does not match the configured {$db['driver']} connection.");
            return false;
        }

        $process->setTimeout(1800)->run();
        if (!$process->isSuccessful()) {
            $this->error(trim($process->getErrorOutput()));
            return false;
        }

        $this->line("✓ {$db['driver']} database restored.");
        return true;
    }
}
