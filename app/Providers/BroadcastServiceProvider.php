<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Đăng ký các route xác thực với middleware cho cả web và driver
        Broadcast::routes(['middleware' => ['web']]);

        // 2. Tải các định nghĩa kênh từ file channels.php
        require base_path('routes/channels.php');
    }
}