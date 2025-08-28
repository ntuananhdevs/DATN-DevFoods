<?php

namespace App\Listeners;

use App\Events\RefundRequestCreated;
use App\Models\User;
use App\Notifications\RefundRequestCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendRefundRequestNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RefundRequestCreated $event): void
    {
        try {
            $refundRequest = $event->refundRequest;
            
            // Gửi thông báo cho Admin
            $admins = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->get();
            
            foreach ($admins as $admin) {
                $admin->notify(new RefundRequestCreatedNotification($refundRequest, 'admin'));
            }
            
            // Gửi thông báo cho Branch Manager
            if ($refundRequest->branch && $refundRequest->branch->manager) {
                $refundRequest->branch->manager->notify(
                    new RefundRequestCreatedNotification($refundRequest, 'branch')
                );
            }
            
            // Gửi thông báo cho Branch Staff
            $branchStaff = User::whereHas('roles', function ($query) {
                $query->where('name', 'branch_staff');
            })->whereHas('branches', function ($query) use ($refundRequest) {
                $query->where('branches.id', $refundRequest->branch_id);
            })->get();
            
            foreach ($branchStaff as $staff) {
                $staff->notify(new RefundRequestCreatedNotification($refundRequest, 'branch'));
            }
            
            // Gửi thông báo xác nhận cho Customer
            $refundRequest->customer->notify(
                new RefundRequestCreatedNotification($refundRequest, 'customer')
            );
            
            Log::info('Refund request notifications sent successfully', [
                'refund_request_id' => $refundRequest->id,
                'refund_code' => $refundRequest->refund_code
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send refund request notifications', [
                'refund_request_id' => $event->refundRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw để retry nếu cần
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(RefundRequestCreated $event, \Throwable $exception): void
    {
        Log::error('RefundRequestNotification listener failed', [
            'refund_request_id' => $event->refundRequest->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}