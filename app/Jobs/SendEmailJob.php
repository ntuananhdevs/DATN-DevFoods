<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
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
    public $timeout = 60;

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
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send($this->mailable);
    }
    
    /**
     * Xử lý khi job thất bại
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        // Ghi log lỗi
        \Log::error('Email gửi thất bại: ' . $exception->getMessage(), [
            'email' => $this->email,
            'class' => get_class($this->mailable)
        ]);
    }
} 