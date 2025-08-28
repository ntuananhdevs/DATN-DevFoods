<?php

namespace App\Observers;

use App\Models\ReviewReply;
use App\Events\Customer\ReviewReplyCreated;
use App\Events\Customer\ReviewReplyDeleted;
use App\Events\Branch\NewReplyNotification;

class ReviewReplyObserver
{
    public function created(ReviewReply $reply)
    {
        // Event cho customer
        event(new ReviewReplyCreated($reply));
        
        // Event cho branch nếu reply từ branch (is_official = true)
        if ($reply->is_official && $reply->review && $reply->review->branch_id) {
            event(new NewReplyNotification($reply));
        }
    }

    public function deleted(ReviewReply $reply)
    {
        event(new ReviewReplyDeleted($reply));
    }
} 