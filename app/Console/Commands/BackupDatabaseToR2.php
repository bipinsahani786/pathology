<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BackupDatabaseToR2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup-to-r2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database and upload it to Cloudflare R2';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = "backup-" . Carbon::now()->format('Y-m-d-H-i-s') . ".sql";
        $tempPath = storage_path('app/' . $filename);
        
        $this->info("Starting database backup: {$filename}");

        // PostgreSQL dump command
        $command = sprintf(
            'PGPASSWORD="%s" pg_dump -h %s -U %s %s > %s',
            config('database.connections.pgsql.password'),
            config('database.connections.pgsql.host'),
            config('database.connections.pgsql.username'),
            config('database.connections.pgsql.database'),
            $tempPath
        );

        $output = [];
        $returnVar = null;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error("Database dump failed!");
            Log::error("Database backup failed for {$filename}");
            return 1;
        }

        $this->info("Backup file created locally. Uploading to R2...");

        try {
            $disk = config('filesystems.default') === 'r2' ? 'r2' : 'r2'; // Force R2 if configured
            $fileContents = file_get_contents($tempPath);
            
            Storage::disk($disk)->put('backups/' . $filename, $fileContents);
            
            $this->info("Successfully uploaded backup to R2: backups/{$filename}");
            Log::info("Database backup successful: {$filename}");

            // Delete the local temp file
            unlink($tempPath);
            
            // Optional: Clean up old backups (older than 30 days)
            $this->cleanupOldBackups($disk);

            return 0;
        } catch (\Exception $e) {
            $this->error("Upload to R2 failed: " . $e->getMessage());
            Log::error("Database backup upload failed: " . $e->getMessage());
            if (file_exists($tempPath)) unlink($tempPath);
            return 1;
        }
    }

    protected function cleanupOldBackups($disk)
    {
        $files = Storage::disk($disk)->allFiles('backups');
        $now = time();
        $daysToKeep = 30;

        foreach ($files as $file) {
            $timestamp = Storage::disk($disk)->lastModified($file);
            if ($now - $timestamp > ($daysToKeep * 24 * 60 * 60)) {
                Storage::disk($disk)->delete($file);
                $this->info("Deleted old backup: {$file}");
            }
        }
    }
}
