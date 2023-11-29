<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderAdminMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param Order $order
     */
    public function __construct(public Order $order)
    {

    }

    /**
     * @return OrderAdminMail
     */
    public function build()
    {
        return $this->subject('Order Administration')
            ->view('emails.order-admin')
            ->with('order', $this->order);
    }
}
