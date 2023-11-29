<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderUserMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param Order $order
     */
    public function __construct(public Order $order)
    {

    }

    /**
     * @return OrderUserMail
     */
    public function build()
    {
        return $this->subject('Order Info')
            ->view('emails.order-user')
            ->with('order', $this->order);
    }
}
