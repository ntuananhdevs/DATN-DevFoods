<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FindDriverForOrderJob;
use Illuminate\Support\Facades\Log;

class ReviewStuckOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:review-stuck 
                            {--include-old : Bao gồm đơn hàng cũ hơn 2 giờ}
                            {--cleanup-only : Chỉ dọn dẹp cache, không rà soát đơn hàng}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rà soát và khởi động lại job cho các đơn hàng bị treo hoặc đơn cũ đang tìm tài xế';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $includeOld = $this->option('include-old');
        $cleanupOnly = $this->option('cleanup-only');
        
        $this->info('Bắt đầu rà soát đơn hàng bị treo...');
        
        if ($cleanupOnly) {
            // Chỉ dọn dẹp cache
            $this->info('Chế độ: Chỉ dọn dẹp cache');
            $result = FindDriverForOrderJob::cleanupOrderCache();
            
            if ($result['status'] === 'completed') {
                $this->info("Đã dọn dẹp cache thành công: {$result['cleaned_count']} items được xóa từ {$result['checked_orders']} đơn hàng");
            } elseif ($result['status'] === 'skipped') {
                $this->warn("Bỏ qua: {$result['reason']}");
            } else {
                $this->error("Lỗi: {$result['error']}");
                return 1;
            }
        } else {
            // Rà soát đơn hàng bị treo
            $this->info($includeOld ? 'Chế độ: Bao gồm đơn hàng cũ (7 ngày)' : 'Chế độ: Chỉ đơn hàng trong 2 giờ qua');
            
            $result = FindDriverForOrderJob::reviewStuckOrders($includeOld);
            
            if ($result['status'] === 'completed') {
                $this->info("Rà soát hoàn thành:");
                $this->line("- Tìm thấy: {$result['total_found']} đơn hàng");
                $this->line("- Đã xử lý: {$result['processed']} đơn hàng");
                $this->line("- Khởi động lại job: {$result['restarted']} đơn hàng");
                $this->line("- Thời gian giới hạn: {$result['time_limit']}");
                
                if ($result['restarted'] > 0) {
                    $this->info("Đã khởi động lại {$result['restarted']} job để tìm tài xế");
                }
            } elseif ($result['status'] === 'skipped') {
                $this->warn("Bỏ qua: {$result['reason']}");
            } else {
                $this->error("Lỗi: {$result['error']}");
                return 1;
            }
            
            // Sau khi rà soát, tự động dọn dẹp cache
            $this->info('\nTiến hành dọn dẹp cache...');
            $cleanupResult = FindDriverForOrderJob::cleanupOrderCache();
            
            if ($cleanupResult['status'] === 'completed') {
                $this->info("Dọn dẹp cache: {$cleanupResult['cleaned_count']} items được xóa");
            }
        }
        
        $this->info('Hoàn thành!');
        return 0;
    }
}