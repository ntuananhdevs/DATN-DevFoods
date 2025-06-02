<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DriverApplication;
use App\Models\DriverApplicationNotifiable;
use App\Notifications\DriverApplicationConfirmation;

class TestDriverApplicationEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:driver-email {email} {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test driver application confirmation email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name') ?? 'Test User';

        try {
            // Create a dummy application for testing
            $dummyApplication = new DriverApplication([
                'id' => 999999,
                'full_name' => $name,
                'email' => $email,
                'phone_number' => '0123456789',
                'vehicle_type' => 'motorcycle',
                'license_plate' => '29A-12345',
                'created_at' => now()
            ]);

            // Create notifiable and send email
            $applicant = new DriverApplicationNotifiable($email, $name);
            $applicant->notify(new DriverApplicationConfirmation($dummyApplication));

            $this->info("✅ Test email sent successfully to: {$email}");
            $this->info("📧 Check your email inbox for the confirmation message.");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to send test email: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
