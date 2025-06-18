<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Notifications\NewUserWelcomeNotification;

class TestEmail extends Command
{
    protected $signature = 'test:email {email}';
    protected $description = 'Test email sending';

    public function handle()
    {
        $email = $this->argument('email');
        
        try {
            // Tạo user test
            $user = new User();
            $user->email = $email;
            $user->full_name = 'Test User';
            
            // Gửi notification
            $user->notify(new NewUserWelcomeNotification());
            
            $this->info('Email sent successfully to: ' . $email);
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
        }
    }
}