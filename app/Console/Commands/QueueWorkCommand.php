<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class QueueWorkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:process {--daemon : Chạy liên tục trong nền}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xử lý các email trong hàng đợi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daemon = $this->option('daemon');
        
        if ($daemon) {
            $this->info('Đang chạy queue worker trong chế độ daemon...');
            Artisan::call('queue:work', [
                '--tries' => 3,
                '--timeout' => 60,
                '--sleep' => 3,
                '--max-jobs' => 1000,
                '--max-time' => 3600,
            ]);
        } else {
            $this->info('Đang xử lý hàng đợi một lần...');
            Artisan::call('queue:work', [
                '--once' => true,
                '--tries' => 3,
                '--timeout' => 60,
            ]);
        }
        
        $this->info('Hoàn thành xử lý hàng đợi.');
    }
}
