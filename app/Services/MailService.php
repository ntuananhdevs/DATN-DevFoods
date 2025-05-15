<?php

namespace App\Services;

use App\Mail\GenericMail;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public static function send($toEmail, $subject, $content, $data = [])
    {
        Mail::to($toEmail)->send(new GenericMail($subject, $content, $data));
    }
}
