<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenericMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $subjectLine;
    public $content;
    public $data;
    public $title;
    protected $template;

    /**
     * Create a new message instance.
     * 
     * @param string $subjectLine Tiêu đề email
     * @param string|null $content Nội dung email (có thể null nếu sử dụng template)
     * @param array $data Dữ liệu truyền vào template
     * @param string|null $title Tiêu đề trang (mặc định là subjectLine)
     * @param string $template Đường dẫn đến template email (mặc định là 'emails.generic')
     */
    public function __construct($subjectLine, $content = null, $data = [], $title = null, $template = 'emails.generic')
    {
        $this->subjectLine = $subjectLine;
        $this->content = $content;
        $this->data = $data;
        $this->title = $title ?? $subjectLine;
        $this->template = $template;
    }


    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->view($this->template) 
                    ->with([
                        'content' => $this->content,
                        'data' => $this->data,
                        'title' => $this->title,
                    ]);
    }
}
