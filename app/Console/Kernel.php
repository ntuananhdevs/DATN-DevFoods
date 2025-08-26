<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        // Xử lý hàng đợi email mỗi phút
        $schedule->command('queue:process')->everyMinute()->withoutOverlapping();

        // Xóa các job đã hoàn thành sau 7 ngày
        $schedule->command('queue:prune-batches --hours=168')->daily();

        // Xóa các job thất bại sau 30 ngày
        $schedule->command('queue:prune-failed --hours=720')->daily();
        
        // Dọn dẹp file avatar tạm thời mỗi giờ
        $schedule->command('avatar:cleanup-temp --force')->hourly();
        
        // Rà soát đơn hàng bị treo mỗi 5 phút
        $schedule->command('orders:review-stuck')->everyFiveMinutes()->withoutOverlapping();
        
        // Rà soát đơn hàng bị treo bao gồm đơn cũ mỗi 30 phút
        $schedule->command('orders:review-stuck --include-old')->everyThirtyMinutes()->withoutOverlapping();
        
        // Dọn dẹp cache đơn hàng mỗi giờ
        $schedule->command('orders:review-stuck --cleanup-only')->hourly()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
