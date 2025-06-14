<?php

namespace App\Notifications;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BranchDisabled extends Notification implements ShouldQueue
{
    use Queueable;

    protected $branch;
    protected $manager;

    /**
     * Create a new notification instance.
     */
    public function __construct(Branch $branch, User $manager)
    {
        $this->branch = $branch;
        $this->manager = $manager;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Thông báo chi nhánh bị vô hiệu hóa - ' . config('app.name'))
            ->view('emails.notifications.branch-disabled', [
                'branch' => $this->branch,
                'manager' => $this->manager
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'branch_id' => $this->branch->id,
            'branch_name' => $this->branch->name,
            'manager_id' => $this->manager->id,
            'manager_name' => $this->manager->full_name,
            'type' => 'branch_disabled',
            'message' => 'Chi nhánh ' . $this->branch->name . ' đã bị vô hiệu hóa',
        ];
    }
}