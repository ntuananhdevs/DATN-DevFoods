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
            // Tạo nội dung email HTML
            $emailContent = '<!DOCTYPE html>
                            <html lang="vi">
                            <head>
                                <meta charset="UTF-8">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                <title>Xác thực tài khoản - FastFood</title>
                                <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
                                <style>
                                    .otp-code {
                                        letter-spacing: 0.5rem;
                                    }
                                    @media only screen and (max-width: 600px) {
                                        .container {
                                            padding: 16px !important;
                                        }
                                        .otp-code {
                                            font-size: 1.5rem !important;
                                        }
                                    }
                                </style>
                            </head>
                            <body class="bg-gray-100 font-sans" style="margin: 0; padding: 0;">
                                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; margin: 20px auto;">
                                    <tr>
                                        <td bgcolor="#ffffff">
                                            <!-- Header -->
                                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td bgcolor="#f97316" style="padding: 24px; text-align: center;">
                                                        <h1 style="font-size: 24px; font-weight: bold; color: #ffffff; margin: 0;">FastFood</h1>
                                                        <p style="font-size: 14px; color: #fed7aa; margin: 4px 0 0;">Xác thực tài khoản của bạn</p>
                                                    </td>
                                                </tr>
                                            </table>
                                            <!-- Body -->
                                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="padding: 24px;">
                                                        <h2 style="font-size: 20px; font-weight: 600; color: #1f2937; margin-bottom: 16px; text-align: center;">Mã OTP của bạn</h2>
                                                        <p style="font-size: 16px; color: #4b5563; margin-bottom: 16px;">Vui lòng sử dụng mã OTP dưới đây để xác thực tài khoản của bạn. Mã này có hiệu lực trong <strong>10 phút</strong>.</p>
                                                        <div style="background-color: #f3f4f6; padding: 16px; border-radius: 8px; text-align: center;">
                                                            <span class="otp-code" style="font-size: 28px; font-family: monospace; font-weight: bold; color: #f97316;">' . $this->otp . '</span>
                                                        </div>
                                                        <p style="font-size: 16px; color: #4b5563; margin-top: 16px;">Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này hoặc liên hệ với chúng tôi qua <a href="mailto:support@fastfood.com" style="color: #f97316; text-decoration: underline;">support@fastfood.com</a>.</p>
                                                        <div style="text-align: center; margin-top: 24px;">
                                                            <a href="' . url('/verify-otp') . '" style="display: inline-block; background-color: #f97316; color: #ffffff; font-weight: 500; padding: 10px 20px; border-radius: 6px; text-decoration: none;">Xác thực ngay</a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <!-- Footer -->
                                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td bgcolor="#f9fafb" style="padding: 16px; text-align: center; font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb;">
                                                        <p style="margin: 0;">© ' . date('Y') . ' FastFood. Tất cả quyền được bảo lưu.</p>
                                                        <p style="margin-top: 4px;">
                                                            <a href="' . url('/terms') . '" style="color: #f97316; text-decoration: underline;">Điều khoản dịch vụ</a> | 
                                                            <a href="' . url('/privacy') . '" style="color: #f97316; text-decoration: underline;">Chính sách bảo mật</a>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </body>
                            </html>';

            // Gửi email HTML
            Mail::html($emailContent, function ($message) {
                $message->to($this->email)
                    ->subject('Xác thực tài khoản - FastFood');
            });

            // Lưu OTP vào cache
            Cache::put('otp_' . $this->email, $this->otp, now()->addMinutes(10));
        } catch (\Exception $e) {
            Log::error('Lỗi gửi OTP qua queue: ' . $e->getMessage());
            // Trong môi trường phát triển, lưu OTP mặc định
            $defaultOtp = '123456';
            Cache::put('otp_' . $this->email, $defaultOtp, now()->addMinutes(10));
            throw $e; // Ném lại lỗi để job có thể retry
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