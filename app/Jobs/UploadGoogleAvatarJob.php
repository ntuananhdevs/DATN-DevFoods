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
                Log::warning('User not found for avatar upload job', ['user_id' => $this->userId]);
                return;
            }

            if (empty($this->photoURL)) {
                Log::info('No photo URL provided for avatar upload', ['user_id' => $this->userId]);
                return;
            }

            Log::info('Starting background avatar upload', [
                'user_id' => $this->userId,
                'email' => $this->userEmail,
                'photo_url' => $this->photoURL
            ]);

            // Upload avatar to S3
            $s3Url = $avatarUploadService->uploadGoogleAvatar($this->photoURL, $this->userEmail);

            if ($s3Url && $s3Url !== $this->photoURL) {
                // Successfully uploaded to S3, update user
                $oldAvatar = $user->avatar;
                
                $user->avatar = $s3Url;
                $user->save();

                // Delete old avatar if it was also an S3 URL
                if ($oldAvatar && $oldAvatar !== $s3Url && str_contains($oldAvatar, 's3.amazonaws.com')) {
                    $avatarUploadService->deleteAvatar($oldAvatar);
                }

                Log::info('Avatar upload job completed successfully', [
                    'user_id' => $this->userId,
                    'old_avatar' => $oldAvatar,
                    'new_avatar' => $s3Url
                ]);
            } else {
                Log::info('Avatar upload job completed - using original URL', [
                    'user_id' => $this->userId,
                    'photo_url' => $this->photoURL
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Avatar upload job failed', [
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw to trigger retry mechanism
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
