<?php

namespace App\Listeners;

use App\Events\Customer\ReviewCreated;
use App\Events\Customer\ReviewReportUpdated;
use App\Models\Branch;
use App\Notifications\BranchNewReviewNotification;
use App\Notifications\BranchReviewReportedNotification;

class NotifyBranchManagerReview
{
    /**
     * Handle the event when a new review is created.
     *
     * @param  ReviewCreated  $event
     * @return void
     */
    public function handleReviewCreated(ReviewCreated $event)
    {
        $review = $event->review;
        if ($review->branch_id) {
            $branch = Branch::find($review->branch_id);
            if ($branch) {
                // Chống duplicate notification
                $exists = $branch->notifications()
                    ->where('type', BranchNewReviewNotification::class)
                    ->where('data->review_id', $review->id)
                    ->exists();
                if (!$exists) {
                    $branch->notify(new BranchNewReviewNotification($review));
                }
            }
        }
    }

    /**
     * Handle the event when a review is reported.
     *
     * @param  ReviewReportUpdated  $event
     * @return void
     */
    public function handleReviewReportUpdated(ReviewReportUpdated $event)
    {
        // Lấy review từ event
        $review = \App\Models\ProductReview::find($event->review_id);
        if ($review && $review->branch_id) {
            $branch = Branch::find($review->branch_id);
            if ($branch) {
                // Chống duplicate notification
                $exists = $branch->notifications()
                    ->where('type', BranchReviewReportedNotification::class)
                    ->where('data->review_id', $review->id)
                    ->exists();
                if (!$exists) {
                    $branch->notify(new BranchReviewReportedNotification($review));
                }
            }
        }
    }
} 