<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AvatarUploadService;

class CleanupTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avatar:cleanup-temp {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up temporary avatar files older than 1 hour';

    /**
     * The avatar upload service instance.
     */
    protected $avatarUploadService;

    /**
     * Create a new command instance.
     */
    public function __construct(AvatarUploadService $avatarUploadService)
    {
        parent::__construct();
        $this->avatarUploadService = $avatarUploadService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting temporary avatar files cleanup...');

        if (!$this->option('force')) {
            if (!$this->confirm('This will delete temporary avatar files older than 1 hour. Continue?')) {
                $this->info('Cleanup cancelled.');
                return 0;
            }
        }

        $cleanedCount = $this->avatarUploadService->cleanupTempFiles();

        if ($cleanedCount > 0) {
            $this->info("Successfully cleaned up {$cleanedCount} temporary files.");
        } else {
            $this->info('No temporary files found to clean up.');
        }

        return 0;
    }
}
