<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailable;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $mailable;
    
    /**
     * Số lần thử lại nếu job thất bại
     *
     * @var int
     */
    public $tries = 3;
    
    /**
     * Thời gian chờ trước khi timeout (giây)
     *
     * @var int
     */
    public $timeout = 120;
    
    /**
     * Thời gian delay giữa các lần retry (giây)
     *
     * @var int
     */
    public $backoff = 30;

    /**
     * Create a new job instance.
     *
     * @param string $email Địa chỉ email nhận
     * @param Mailable $mailable Đối tượng mail được gửi
     */
    public function __construct($email, Mailable $mailable)
    {
        $this->email = $email;
        $this->mailable = $mailable;
        
        // Sử dụng default queue để queue worker có thể xử lý
        // $this->onQueue('emails'); // Commented out để sử dụng default queue
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Starting email send job', [
                'to' => $this->email,
                'mailable' => get_class($this->mailable),
                'attempt' => $this->attempts()
            ]);
            
            Mail::to($this->email)->send($this->mailable);
            
            Log::info('Email sent successfully', [
                'to' => $this->email,
                'mailable' => get_class($this->mailable),
                'attempt' => $this->attempts()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send email', [
                'to' => $this->email,
                'mailable' => get_class($this->mailable),
                'attempt' => $this->attempts(),
                'max_tries' => $this->tries,
                'error' => $e->getMessage(),
                'exception' => $e
            ]);
            
            // Re-throw exception to trigger retry mechanism
            throw $e;
        }
    }
    
    /**
     * Xử lý khi job thất bại hoàn toàn
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error('Email job failed after all retries', [
            'email' => $this->email,
            'mailable' => get_class($this->mailable),
            'attempts' => $this->tries,
            'error' => $exception->getMessage(),
            'exception' => $exception
        ]);
        
        // Optionally, you can send a notification to admins about failed emails
        // Or store failed email info in database for manual retry
    }
    
    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array|int
     */
    public function backoff()
    {
        // Exponential backoff: 30s, 60s, 120s
        return [30, 60, 120];
    }
}