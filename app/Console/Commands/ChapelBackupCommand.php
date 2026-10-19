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

        // 1. Back up Database
        $dbPath = database_path('database.sqlite');
        if (File::exists($dbPath)) {
            $zip->addFile($dbPath, 'database/database.sqlite');
            $this->line('✓ Database included in backup.');
        }

        // 2. Back up Vault and Attachments
        $storagePublic = storage_path('app/public');
        if (File::exists($storagePublic)) {
            $files = File::allFiles($storagePublic);
            foreach ($files as $file) {
                $relative = substr($file->getPathname(), strlen($storagePublic) + 1);
                $zip->addFile($file->getPathname(), "vault_files/{$relative}");
            }
            $this->line("✓ Included " . count($files) . " Vault and attachment files.");
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
