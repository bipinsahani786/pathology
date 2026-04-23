<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class MigrateStorageToR2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:migrate-to-r2 {--disk=r2 : The destination disk} {--force : Overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate files from local public storage to Cloudflare R2';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $destDisk = $this->option('disk');
        $force = $this->option('force');

        $this->info("Starting migration from 'public' disk to '{$destDisk}'...");

        $files = Storage::disk('public')->allFiles();
        $total = count($files);

        if ($total === 0) {
            $this->warn("No files found on 'public' disk.");
            return;
        }

        $this->info("Found {$total} files to migrate.");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($files as $file) {
            try {
                if ($force || !Storage::disk($destDisk)->exists($file)) {
                    $content = Storage::disk('public')->get($file);
                    Storage::disk($destDisk)->put($file, $content);
                }
            } catch (\Exception $e) {
                $this->error("\nFailed to migrate: {$file}. Error: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nMigration completed successfully!");
    }
}
