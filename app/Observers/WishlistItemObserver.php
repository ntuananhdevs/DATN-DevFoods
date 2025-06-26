<?php

namespace App\Observers;

use App\Events\Customer\FavoriteUpdated;
use App\Models\WishlistItem;
use Illuminate\Support\Facades\Auth;

class WishlistItemObserver
{
    /**
     * Handle the WishlistItem "created" event.
     *
     * @param  \App\Models\WishlistItem  $wishlistItem
     * @return void
     */
    public function created(WishlistItem $wishlistItem)
    {
        $user = $wishlistItem->user;
        if ($user) {
            event(new FavoriteUpdated($user->id, $wishlistItem->product_id, true, $user->wishlist()->count()));
        }
    }

    /**
     * Handle the WishlistItem "deleted" event.
     *
     * @param  \App\Models\WishlistItem  $wishlistItem
     * @return void
     */
    public function deleted(WishlistItem $wishlistItem)
    {
        // When a deleted event is fired, the model is already gone from the database,
        // so we need to get the user from the authenticated session.
        $user = Auth::user();
        if ($user) {
            event(new FavoriteUpdated($user->id, $wishlistItem->product_id, false, $user->wishlist()->count()));
        }
    }
} 