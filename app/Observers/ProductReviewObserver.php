<?php

namespace App\Observers;

use App\Events\Customer\ReviewHelpfulUpdated;
use App\Events\Customer\ReviewDeleted;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Event;

class ProductReviewObserver
{
    public function updated(ProductReview $review)
    {
        if ($review->wasChanged('helpful_count')) {
            event(new ReviewHelpfulUpdated($review));
        }
    }

    public function deleted(ProductReview $review)
    {
        event(new ReviewDeleted($review));
    }
} 