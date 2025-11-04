<?php

namespace App\Listeners;

use App\Events\RefundRequestStatusUpdated;
use App\Models\User;
use App\Notifications\RefundRequestStatusUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendRefundStatusUpdateNotification implements ShouldQueue
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
    public function handle(RefundRequestStatusUpdated $event): void
    {
        try {
            $refundRequest = $event->refundRequest;
            $oldStatus = $event->oldStatus;
            $newStatus = $event->newStatus;
            
            // Gửi thông báo cho Customer về việc cập nhật status
            $refundRequest->customer->notify(
                new RefundRequestStatusUpdatedNotification($refundRequest, $oldStatus, $newStatus, 'customer')
            );
            
            // Gửi thông báo cho Admin nếu status quan trọng
            if (in_array($newStatus, ['approved', 'rejected', 'completed'])) {
                $admins = User::whereHas('roles', function ($query) {
                    $query->where('name', 'admin');
                })->get();
                
                foreach ($admins as $admin) {
                    $admin->notify(
                        new RefundRequestStatusUpdatedNotification($refundRequest, $oldStatus, $newStatus, 'admin')
                    );
                }
            }
            
            // Gửi thông báo cho Branch nếu cần thiết
            if (in_array($newStatus, ['under_review', 'approved', 'rejected'])) {
                if ($refundRequest->branch && $refundRequest->branch->manager) {
                    $refundRequest->branch->manager->notify(
                        new RefundRequestStatusUpdatedNotification($refundRequest, $oldStatus, $newStatus, 'branch')
                    );
                }
                
                // Gửi cho Branch Staff
                $branchStaff = User::whereHas('roles', function ($query) {
                    $query->where('name', 'branch_staff');
                })->whereHas('branches', function ($query) use ($refundRequest) {
                    $query->where('branches.id', $refundRequest->branch_id);
                })->get();
                
                foreach ($branchStaff as $staff) {
                    $staff->notify(
                        new RefundRequestStatusUpdatedNotification($refundRequest, $oldStatus, $newStatus, 'branch')
                    );
                }
            }
            
            Log::info('Refund status update notifications sent successfully', [
                'refund_request_id' => $refundRequest->id,
                'refund_code' => $refundRequest->refund_code,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send refund status update notifications', [
                'refund_request_id' => $event->refundRequest->id,
                'old_status' => $event->oldStatus,
                'new_status' => $event->newStatus,
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
    public function failed(RefundRequestStatusUpdated $event, \Throwable $exception): void
    {
        Log::error('RefundStatusUpdateNotification listener failed', [
            'refund_request_id' => $event->refundRequest->id,
            'old_status' => $event->oldStatus,
            'new_status' => $event->newStatus,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}