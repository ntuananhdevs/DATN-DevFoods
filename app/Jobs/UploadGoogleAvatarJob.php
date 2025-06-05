<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\AvatarUploadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UploadGoogleAvatarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120; // 2 minutes timeout

    protected $userId;
    protected $photoURL;
    protected $userEmail;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $photoURL, $userEmail)
    {
        $this->userId = $userId;
        $this->photoURL = $photoURL;
        $this->userEmail = $userEmail;
    }

    /**
     * Execute the job.
     */
    public function handle(AvatarUploadService $avatarUploadService): void
    {
        try {
            $user = User::find($this->userId);
            
            if (!$user) {
                return;
            }

            if (empty($this->photoURL)) {
                return;
            }

            // Upload avatar to S3 - returns filename only
            $filename = $avatarUploadService->uploadGoogleAvatar($this->photoURL, $this->userEmail);

            if ($filename) {
                // Save filename to user avatar field
                $user->avatar = $filename;
                $user->save();
            } else {
                throw new \Exception('Avatar upload failed');
            }

        } catch (\Exception $e) {
            Log::error('Avatar upload job failed', [
                'user_id' => $this->userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Avatar upload job failed permanently', [
            'user_id' => $this->userId,
            'photo_url' => $this->photoURL,
            'error' => $exception->getMessage(),
            'max_attempts' => $this->tries
        ]);
    }
}
