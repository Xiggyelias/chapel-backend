<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use ZipArchive;

class ChapelBackupCommand extends Command
{
    protected $signature = 'chapel:backup {--destination= : Custom secondary backup destination}';
    protected $description = 'Create a compressed backup of the Kwang-Lim Chapel database and Records Vault';

    public function handle(): int
    {
        $this->info('Starting Kwang-Lim Chapel Institutional Backup...');

        $timestamp = date('Y-m-d_His');
        $backupDir = storage_path('app/backups');
        File::ensureDirectoryExists($backupDir, 0750);

        $backupFile = "{$backupDir}/AU_Chapel_Backup_{$timestamp}.zip";
        $zip = new ZipArchive();

        if ($zip->open($backupFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->error('Failed to create backup archive.');
            return self::FAILURE;
        }

        // 1. Database
        $dumpFile = $this->dumpDatabase($backupDir, $timestamp);
        if ($dumpFile === null) {
            $zip->close();
            File::delete($backupFile);
            $this->error('Backup aborted: the database could not be exported.');
            return self::FAILURE;
        }
        $zip->addFile($dumpFile, 'database/' . basename($dumpFile));

        // 2. Vault records and attachments (private) plus any legacy public uploads
        $sources = [
            storage_path('app/private') => 'private_files',
            storage_path('app/public') => 'vault_files',
        ];
        foreach ($sources as $directory => $prefix) {
            if (!File::exists($directory)) {
                continue;
            }
            $files = File::allFiles($directory);
            foreach ($files as $file) {
                $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($directory) + 1));
                $zip->addFile($file->getPathname(), "{$prefix}/{$relative}");
            }
            $this->line('✓ Included ' . count($files) . " files from {$prefix}.");
        }

        $zip->close();
        if ($dumpFile !== config('database.connections.sqlite.database')) {
            File::delete($dumpFile);
        }

        // 3. Optional secondary (off-server) copy
        $secondary = $this->option('destination') ?: config('chapel.backup.secondary_dir');
        if ($secondary && File::isDirectory($secondary)) {
            File::copy($backupFile, rtrim($secondary, '/\\') . DIRECTORY_SEPARATOR . basename($backupFile));
            $this->info("✓ Copied backup to secondary location: {$secondary}");
        } elseif ($secondary) {
            $this->warn("! Secondary location not found: {$secondary}");
        }

        $this->pruneOldBackups($backupDir);

        $this->info("Institutional Backup Complete: {$backupFile}");
        $this->line('SHA-256 Checksum: ' . hash_file('sha256', $backupFile));

        return self::SUCCESS;
    }

    /**
     * Export the configured database to a file. SQLite is copied; PostgreSQL and MySQL/MariaDB use their native dump tools.
     */
    private function dumpDatabase(string $dir, string $timestamp): ?string
    {
        $name = config('database.default');
        $db = config("database.connections.{$name}");

        if ($db['driver'] === 'sqlite') {
            $path = $db['database'];
            if (!File::exists($path)) {
                $this->error("SQLite database not found at {$path}");
                return null;
            }
            $this->line('✓ SQLite database included.');
            return $path;
        }

        $target = "{$dir}/database_{$timestamp}." . ($db['driver'] === 'pgsql' ? 'pgdump' : 'sql');

        $process = match ($db['driver']) {
            'pgsql' => new Process(
                ['pg_dump', '--host=' . $db['host'], '--port=' . $db['port'], '--username=' . $db['username'], '--format=custom', '--no-owner', '--file=' . $target, $db['database']],
                null,
                ['PGPASSWORD' => (string) $db['password']]
            ),
            'mysql', 'mariadb' => new Process(
                ['mysqldump', '--host=' . $db['host'], '--port=' . $db['port'], '--user=' . $db['username'], '--single-transaction', '--routines', '--result-file=' . $target, $db['database']],
                null,
                ['MYSQL_PWD' => (string) $db['password']]
            ),
            default => null,
        };

        if ($process === null) {
            $this->error("Unsupported database driver for backup: {$db['driver']}");
            return null;
        }

        $process->setTimeout(900)->run();

        if (!$process->isSuccessful() || !File::exists($target) || File::size($target) === 0) {
            $this->error(trim($process->getErrorOutput()) ?: 'Database dump produced no output.');
            return null;
        }

        $this->line("✓ {$db['driver']} database exported (" . number_format(File::size($target) / 1024, 1) . ' KB).');
        return $target;
    }

    private function pruneOldBackups(string $dir): void
    {
        $keep = max(1, (int) config('chapel.backup.keep', 14));
        $archives = collect(File::glob("{$dir}/AU_Chapel_Backup_*.zip"))->sort()->values();

        $archives->slice(0, max(0, $archives->count() - $keep))->each(function ($old) {
            File::delete($old);
            $this->line('✓ Pruned old backup ' . basename($old));
        });
    }
}
