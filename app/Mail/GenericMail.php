<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GenericMail extends Mailable {
    use Queueable, SerializesModels;

    public $subjectLine;
    public $content;
    public $data;
    /**
     * Create a new message instance.
     */
    public function __construct($subjectLine, $content, $data = []) {
        $this->subjectLine = $subjectLine;
        $this->content = $content;
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
     public function build() {
        return $this->subject($this->subjectLine)
                    ->view('emails.generic') 
                    ->with([
                        'content' => $this->content,
                        'data' => $this->data,
                    ]);
    }
}
