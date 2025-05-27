<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendEmailJob;
use App\Mail\NotificationMail;

class CheckQueueHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:health {--test-email= : Test email address to send test email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check queue health and optionally send test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking Queue Health...');
        $this->newLine();

        // Check pending jobs
        $pendingJobs = DB::table('jobs')->count();
        $this->info("📋 Pending jobs in queue: {$pendingJobs}");

        // Check failed jobs
        $failedJobs = DB::table('failed_jobs')->count();
        if ($failedJobs > 0) {
            $this->warn("❌ Failed jobs: {$failedJobs}");
        } else {
            $this->info("✅ Failed jobs: {$failedJobs}");
        }

        // Check queue configuration
        $queueConnection = config('queue.default');
        $this->info("⚙️  Queue connection: {$queueConnection}");

        // Check email configuration
        $mailDriver = config('mail.default');
        $this->info("📧 Mail driver: {$mailDriver}");

        $this->newLine();

        // Test email if requested
        if ($this->option('test-email')) {
            $this->testEmail($this->option('test-email'));
        }

        // Show recommendations
        $this->showRecommendations($pendingJobs, $failedJobs);
    }

    private function testEmail($email)
    {
        $this->info("📤 Sending test email to: {$email}");

        try {
            $testData = [
                'driver' => [
                    'full_name' => 'Test User',
                    'email' => $email,
                    'phone_number' => '0123456789',
                    'id' => 999
                ],
                'newPassword' => 'TestPassword123!',
                'reason' => 'Queue health check test',
                'resetDate' => now()->format('d/m/Y H:i:s'),
                'loginUrl' => route('driver.login'),
                'supportEmail' => config('mail.support_email', 'support@devfoods.com'),
                'companyName' => config('app.name', 'DevFoods')
            ];

            $mailable = new NotificationMail(
                'driver_password_reset',
                $testData,
                "Queue Health Check - Test Email"
            );

            SendEmailJob::dispatch($email, $mailable);
            $this->info("✅ Test email queued successfully");
        } catch (\Exception $e) {
            $this->error("❌ Failed to queue test email: " . $e->getMessage());
        }
    }

    private function showRecommendations($pendingJobs, $failedJobs)
    {
        $this->info('💡 Recommendations:');

        if ($pendingJobs > 100) {
            $this->warn('  • Consider adding more queue workers to handle pending jobs');
        }

        if ($failedJobs > 0) {
            $this->warn('  • Review failed jobs with: php artisan queue:failed');
            $this->warn('  • Retry failed jobs with: php artisan queue:retry all');
        }

        $this->info('  • Start queue worker with: php artisan queue:work --queue=emails,default');
        $this->info('  • Monitor queue with: php artisan queue:monitor emails,default');
        $this->info('  • For production, use supervisor or similar process manager');

        $this->newLine();
        $this->info('📚 Queue Status Summary:');
        $this->table(['Metric', 'Value', 'Status'], [
            ['Pending Jobs', $pendingJobs, $pendingJobs < 50 ? '✅ Good' : '⚠️  High'],
            ['Failed Jobs', $failedJobs, $failedJobs == 0 ? '✅ Good' : '❌ Needs attention'],
            ['Queue Driver', config('queue.default'), '✅ Configured'],
            ['Mail Driver', config('mail.default'), '✅ Configured'],
        ]);
    }
}
