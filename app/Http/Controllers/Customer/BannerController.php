<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function getBannersByPosition($position)
    {
        return Banner::where('position', $position)
                     ->where('is_active', true)
                     ->orderBy('order', 'ASC')
                     ->get();
    }

}
