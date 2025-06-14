<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestFirebaseConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firebase:test-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Firebase configuration and validate all required fields';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Firebase Configuration...');
        $this->line('');
        
        // Check if Firebase auth is enabled
        $authEnabled = config('firebase.auth.enabled');
        $this->line("Firebase Auth Enabled: " . ($authEnabled ? 'YES' : 'NO'));
        
        if (!$authEnabled) {
            $this->warn('Firebase authentication is disabled in config');
            return;
        }
        
        // Check Google provider
        $googleEnabled = config('firebase.auth.providers.google.enabled');
        $this->line("Google Provider Enabled: " . ($googleEnabled ? 'YES' : 'NO'));
        
        if (!$googleEnabled) {
            $this->warn('Google authentication provider is disabled');
            return;
        }
        
        // Get Firebase config
        $config = config('firebase.web_config');
        
        // Required fields
        $requiredFields = [
            'apiKey' => 'Firebase API Key',
            'authDomain' => 'Auth Domain', 
            'projectId' => 'Project ID',
            'storageBucket' => 'Storage Bucket',
            'messagingSenderId' => 'Messaging Sender ID',
            'appId' => 'App ID'
        ];
        
        $this->line('');
        $this->info('Checking Required Fields:');
        
        $allValid = true;
        $missingFields = [];
        
        foreach ($requiredFields as $key => $label) {
            $value = $config[$key] ?? null;
            $isValid = !empty($value);
            
            if ($isValid) {
                $this->line("✓ {$label}: " . (strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value));
            } else {
                $this->error("✗ {$label}: MISSING");
                $missingFields[] = $key;
                $allValid = false;
            }
        }
        
        // Optional fields
        $optionalFields = [
            'measurementId' => 'Measurement ID (Google Analytics)'
        ];
        
        $this->line('');
        $this->info('Optional Fields:');
        
        foreach ($optionalFields as $key => $label) {
            $value = $config[$key] ?? null;
            if (!empty($value)) {
                $this->line("✓ {$label}: " . (strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value));
            } else {
                $this->comment("- {$label}: Not set");
            }
        }
        
        $this->line('');
        
        if ($allValid) {
            $this->info('✓ All required Firebase configuration fields are present!');
            
            // Test API endpoint
            $this->line('');
            $this->info('Testing API endpoint...');
            
            try {
                $response = app(\App\Http\Controllers\FirebaseConfigController::class)->getConfig();
                $data = json_decode($response->getContent(), true);
                
                if ($data['enabled']) {
                    $this->info('✓ Firebase config API endpoint working correctly');
                } else {
                    $this->error('✗ Firebase config API returned disabled: ' . $data['message']);
                }
            } catch (\Exception $e) {
                $this->error('✗ Error testing API endpoint: ' . $e->getMessage());
            }
            
        } else {
            $this->error('✗ Missing required fields: ' . implode(', ', $missingFields));
            $this->line('');
            $this->warn('Please add the following to your .env file:');
            
            foreach ($missingFields as $field) {
                $envKey = 'FIREBASE_' . strtoupper(preg_replace('/([A-Z])/', '_$1', $field));
                $envKey = str_replace('_I_D', '_ID', $envKey); // Fix ID formatting
                $this->line("{$envKey}=your-{$field}-here");
            }
        }
        
        $this->line('');
        $this->info('For more information, see FIREBASE_SETUP_GUIDE.md');
    }
}
