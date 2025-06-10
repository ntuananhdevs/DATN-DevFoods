<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\SendOTPMail;

class SendOTPJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $otp;

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
     * Create a new job instance.
     *
     * @param string $email Địa chỉ email nhận
     * @param string $otp Mã OTP
     * @return void
     */
    public function __construct($email, $otp)
    {
        $this->email = $email;
        $this->otp = $otp;
        $this->onQueue('default'); // Sử dụng queue default cho nhất quán
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Gửi email sử dụng Mailable class
            Mail::to($this->email)->send(new SendOTPMail($this->email, $this->otp));

            // Lưu OTP vào cache
            Cache::put('otp_' . $this->email, $this->otp, now()->addMinutes(10));
            
            // Log thành công
            Log::info('OTP email sent successfully via queue to: ' . $this->email);
        } catch (\Exception $e) {
            Log::error('Lỗi gửi OTP qua queue: ' . $e->getMessage(), [
                'email' => $this->email,
                'exception' => $e
            ]);
            
            // Trong môi trường phát triển, lưu OTP mặc định
            if (app()->environment('local', 'development')) {
                $defaultOtp = '123456';
                Log::warning('Using default OTP in development environment: ' . $defaultOtp);
                Cache::put('otp_' . $this->email, $defaultOtp, now()->addMinutes(10));
            } else {
                // Trong môi trường production, đảm bảo OTP được lưu
                Cache::put('otp_' . $this->email, $this->otp, now()->addMinutes(10));
                throw $e; // Ném lại lỗi để job có thể retry
            }
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
            'otp' => $this->otp,
            'attempts' => $this->tries,
            'error' => $exception->getMessage(),
            'exception' => $exception
        ]);
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array
     */
    public function backoff()
    {
        // Exponential backoff: 30s, 60s, 120s
        return [30, 60, 120];
    }
}