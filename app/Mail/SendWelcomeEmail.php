<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendWelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
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
     * @param User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
        return $this->subject('Chào mừng bạn đến với FastFood!')
                    ->view('emails.welcome')
                    ->with(['notifiable' => $this->user]);
    }
} 