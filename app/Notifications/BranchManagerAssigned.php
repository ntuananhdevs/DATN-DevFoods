<?php

namespace App\Notifications;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BranchManagerAssigned extends Notification implements ShouldQueue
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
            ->subject('Phân công quản lý chi nhánh - ' . config('app.name'))
            ->view('emails.notifications.branch-manager-assigned', [
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
            'type' => 'branch_manager_assigned',
            'message' => 'Bạn đã được phân công làm quản lý chi nhánh ' . $this->branch->name,
        ];
    }
}
