<?php

namespace App\Observers;

use App\Models\ReviewReply;
use App\Events\Customer\ReviewReplyCreated;
use App\Events\Customer\ReviewReplyDeleted;

class ReviewReplyObserver
{
    public function created(ReviewReply $reply)
    {
        event(new ReviewReplyCreated($reply));
    }

    public function deleted(ReviewReply $reply)
    {
        event(new ReviewReplyDeleted($reply));
    }
} 