<?php

namespace App\Mail;

use App\Models\ProductComments;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookCommentMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param ProductComments $comment
     */
    public function __construct(public ProductComments $comment)
    {

    }

    /**
     * @return BookCommentMail
     */
    public function build()
    {
        return $this->subject('Book comment')
            ->view('emails.comment-message')
            ->with('comment', $this->comment);
    }
}
