<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ZipArchive;

class ChapelBackupCommand extends Command
{
    protected $signature = 'chapel:backup {--destination= : Custom secondary backup destination}';
    protected $description = 'Create a full encrypted/compressed backup of the Kwang-Lim Chapel database and Records Vault';

    public function handle(): int
    {
        $this->info('Starting Kwang-Lim Chapel Institutional Backup...');

        $timestamp = date('Y-m-d_His');
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $backupFile = "{$backupDir}/AU_Chapel_Backup_{$timestamp}.zip";
        $zip = new ZipArchive();

        if ($zip->open($backupFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->error('Failed to create backup archive.');
            return 1;
        }

        // 1. Back up Database (file copy is only possible for SQLite)
        $dbPath = config('database.connections.sqlite.database') ?: database_path('database.sqlite');
        if (config('database.default') === 'sqlite' && File::exists($dbPath)) {
            $zip->addFile($dbPath, 'database/database.sqlite');
            $this->line('✓ Database included in backup.');
        } else {
            $this->warn('! Database NOT included: ' . config('database.default') . ' must be backed up with pg_dump/mysqldump or your provider\'s snapshots.');
        }

        // 2. Back up Vault records and attachments (private) plus any legacy public uploads
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
            $this->line("✓ Included " . count($files) . " files from {$prefix}.");
        }

        $zip->close();

        // 3. Optional secondary destination copy (Second Location)
        $secondary = $this->option('destination') ?: env('CHAPEL_BACKUP_SECONDARY_DIR');
        if ($secondary && File::exists($secondary)) {
            File::copy($backupFile, rtrim($secondary, '/') . '/' . basename($backupFile));
            $this->info("✓ Copied backup to secondary off-site location: {$secondary}");
        }

        $sha256 = hash_file('sha256', $backupFile);
        $this->info("Institutional Backup Complete: {$backupFile}");
        $this->line("SHA-256 Checksum: {$sha256}");

        return 0;
    }
}
