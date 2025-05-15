<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectLine;
    public $content;
    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct($subjectLine, $content, $data = [])
    {
        $this->subjectLine = $subjectLine;
        $this->content = $content;
        $this->data = $data;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->view('emails.generic')  // bạn tạo view này để chứa html mail
                    ->with([
                        'content' => $this->content,
                        'data' => $this->data,
                    ]);
    }
}
