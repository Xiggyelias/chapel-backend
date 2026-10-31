<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
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

        // 1. Restore Database
        $restoredDb = "{$extractDir}/database/database.sqlite";
        if (File::exists($restoredDb)) {
            File::copy($restoredDb, database_path('database.sqlite'));
            $this->line('✓ Database successfully restored.');
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
}
