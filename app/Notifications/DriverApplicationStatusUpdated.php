<?php

namespace App\Notifications;

use App\Models\DriverApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DriverApplicationStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;
    protected $status;

    public function __construct(DriverApplication $application, string $status)
    {
        $this->application = $application;
        $this->status = $status;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = $this->status === 'approved'
            ? 'Your driver application has been approved!'
            : 'Your driver application has been rejected.';

        $mail = (new MailMessage)
            ->subject('Driver Application Status Update')
            ->greeting('Hello ' . $this->application->full_name)
            ->line($message);

        if ($this->status === 'rejected' && $this->application->admin_notes) {
            $mail->line('Reason: ' . $this->application->admin_notes);
        }

        if ($this->status === 'approved') {
            $mail->line('You can now log in to your account and start accepting delivery requests.')
                ->action('Login to Dashboard', url('/driver/dashboard'));
        }

        return $mail;
    }

    public function toArray($notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'status' => $this->status,
            'message' => $this->status === 'approved'
                ? 'Your driver application has been approved!'
                : 'Your driver application has been rejected.',
            'reason' => $this->application->admin_notes
        ];
    }
} 