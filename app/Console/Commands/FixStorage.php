<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix storage symlink, permissions, and directory structure issues.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Storage Diagnosis and Fix...');

        $publicStorage = public_path('storage');
        $storageAppPublic = storage_path('app/public');

        // 1. Check if source directory exists
        if (!File::exists($storageAppPublic)) {
            $this->error("❌ Source storage path does not exist: $storageAppPublic");
            $this->info("Creating it now...");
            File::makeDirectory($storageAppPublic, 0775, true);
        } else {
            $this->info("✅ Source storage path exists.");
        }

        // 2. Check public/storage status
        if (File::exists($publicStorage)) {
            if (is_link($publicStorage)) {
                $this->info("⚠️  'public/storage' is already a symlink.");
                $target = readlink($publicStorage);
                $this->info("   Target: $target");
                
                $this->info("Removing existing symlink to regenerate...");
                // On Windows/Linux toggle
                @unlink($publicStorage);
            } elseif (is_dir($publicStorage)) {
                $this->warn("⚠️  'public/storage' is a REAL DIRECTORY, not a symlink!");
                $this->warn("   This is likely why images are not updating or persisting.");
                
                $backupName = 'storage_backup_' . date('Y_m_d_His');
                $this->info("   Backing up this directory to 'public/$backupName'...");
                File::move($publicStorage, public_path($backupName));
            }
        } else {
            $this->info("ℹ️  'public/storage' does not exist yet.");
        }

        // 3. Run structure check for photos
        $photosPath = $storageAppPublic . '/join_requests_photos';
        if (!File::exists($photosPath)) {
            $this->info("Creating 'join_requests_photos' directory...");
            File::makeDirectory($photosPath, 0775, true);
        }

        // 4. Execute storage:link
        $this->info("🔗 Running 'php artisan storage:link'...");
        $this->call('storage:link');

        $this->info("\n✅ Fix Complete!");
        $this->comment("If images were uploaded to a transient filesystem (like Heroku/Railway without volumes), they might be permanently lost.");
        $this->comment("However, new uploads and persistent volume content should now work correctly.");
    }
}
