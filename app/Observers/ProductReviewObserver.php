<?php

namespace App\Observers;

use App\Events\Customer\ReviewHelpfulUpdated;
use App\Events\Customer\ReviewDeleted;
use App\Events\Customer\ReviewCreated;
use App\Events\Branch\NewReviewNotification;
use App\Events\Branch\ReviewDeletedNotification;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Event;

class ProductReviewObserver
{
    public function created(ProductReview $review)
    {
        // Event cho customer
        event(new ReviewCreated($review));
        
        // Event cho branch nếu có branch_id
        if ($review->branch_id) {
            event(new NewReviewNotification($review));
        }
    }

    public function updated(ProductReview $review)
    {
        if ($review->wasChanged('helpful_count')) {
            event(new ReviewHelpfulUpdated($review));
        }
    }

    public function deleted(ProductReview $review)
    {
        // Event cho customer
        event(new ReviewDeleted($review));
        
        // Event cho branch nếu có branch_id
        if ($review->branch_id) {
            event(new ReviewDeletedNotification($review));
        }
    }
} 