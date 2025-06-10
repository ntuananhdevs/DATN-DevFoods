<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestDriverApplicationAccess extends Command
{
    protected $signature = 'test:driver-application-access';
    protected $description = 'Test if driver application success page is properly protected';

    public function handle()
    {
        $this->info('Testing driver application success page protection...');
        
        // Get the base URL (you might need to adjust this)
        $baseUrl = config('app.url', 'http://localhost:8000');
        $successUrl = $baseUrl . '/hiring-driver/success';
        
        try {
            // Test direct access without session
            $response = Http::withoutRedirecting()->get($successUrl);
            
            if ($response->status() === 302) {
                $location = $response->header('Location');
                if (str_contains($location, '/hiring-driver/apply')) {
                    $this->info('✅ SUCCESS: Direct access to success page is properly blocked');
                    $this->info('   → Redirects to: ' . $location);
                } else {
                    $this->warn('⚠️  WARNING: Redirects but not to application form: ' . $location);
                }
            } else {
                $this->error('❌ FAILURE: Success page is accessible without session');
                $this->error('   HTTP Status: ' . $response->status());
            }
            
        } catch (\Exception $e) {
            $this->error('Error testing URL: ' . $e->getMessage());
            $this->info('Note: This might be expected if the local server is not running');
        }
        
        $this->newLine();
        $this->info('Test completed. The success page should only be accessible after form submission.');
        
        return 0;
    }
} 