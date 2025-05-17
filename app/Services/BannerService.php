<?php

namespace App\Services;

use App\Models\Banner;

class BannerService
{
    public function getActiveBanners()
    {
        return Banner::where('is_active', 1)
            ->orderBy('order', 'asc')
            ->get();
    }
}