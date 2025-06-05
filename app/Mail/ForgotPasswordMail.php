<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetLink;
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
     * @param string $resetLink
     * @return void
     */
    public function __construct($email, $resetLink)
    {
        $this->email = $email;
        $this->resetLink = $resetLink;
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
        return $this->subject('Đặt lại mật khẩu - FastFood')
                    ->view('emails.forgotPassword');
    }
} 