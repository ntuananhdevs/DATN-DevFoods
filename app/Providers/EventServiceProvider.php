<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Models\WishlistItem;
use App\Observers\WishlistItemObserver;
use App\Events\Order\OrderConfirmed;
use App\Listeners\Order\FindDriverForOrder;
use App\Events\Order\DriverAssigned;
use App\Listeners\Order\NotifyDriverAssigned;
use App\Events\Customer\ReviewCreated;
use App\Events\Customer\ReviewReportUpdated;
use App\Listeners\NotifyBranchManagerReview;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        OrderConfirmed::class => [
            FindDriverForOrder::class,
        ],
        DriverAssigned::class => [
            NotifyDriverAssigned::class,
        ],
        ReviewCreated::class => [
            [NotifyBranchManagerReview::class, 'handleReviewCreated'],
        ],
        ReviewReportUpdated::class => [
            [NotifyBranchManagerReview::class, 'handleReviewReportUpdated'],
        ],
    ];

    /**
     * The model observers for your application.
     *
     * @var array
     */
    protected $observers = [
        WishlistItem::class => [WishlistItemObserver::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
