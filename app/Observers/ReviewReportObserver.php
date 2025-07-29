<?php

namespace App\Observers;

use App\Models\ReviewReport;
use App\Models\ProductReview;
use App\Events\Customer\ReviewReportUpdated;

class ReviewReportObserver
{
    public function created(ReviewReport $report)
    {
        $review = ProductReview::find($report->review_id);
        if ($review) {
            $review->report_count = $review->report_count + 1;
            $review->save();
            event(new ReviewReportUpdated($review));
        }
    }

    public function deleted(ReviewReport $report)
    {
        $review = ProductReview::find($report->review_id);
        if ($review && $review->report_count > 0) {
            $review->report_count = $review->report_count - 1;
            $review->save();
            event(new ReviewReportUpdated($review));
        }
    }
} 