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
    public $year;
    public $contactLink;
    public $termsLink;
    public $privacyLink;
    public $facebookLink;
    public $instagramLink;
    public $twitterLink;
    public $googlePlayLink;
    public $appStoreLink;

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
        $this->year = date('Y');
        $this->contactLink = url('/contact');
        $this->termsLink = url('/terms');
        $this->privacyLink = url('/privacy');
        $this->facebookLink = '#';
        $this->instagramLink = '#';
        $this->twitterLink = '#';
        $this->googlePlayLink = '#';
        $this->appStoreLink = '#';
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