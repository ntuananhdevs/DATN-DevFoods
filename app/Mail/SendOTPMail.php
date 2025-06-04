<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOTPMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $email;

    /**
     * Create a new message instance.
     *
     * @param string $email
     * @param string $otp
     * @return void
     */
    public function __construct($email, $otp)
    {
        $this->email = $email;
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Xác thực tài khoản - FastFood')
                    ->view('emails.sendOTP')
                    ->with(['otp' => $this->otp]);
    }
} 