<?php

namespace App\Listeners;

use App\Events\OrderPayment;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateOrderStatus
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param OrderPayment $event
     * @return void
     */
    public function handle(OrderPayment $event)
    {
        $orderBooks = $event->order->books;
        $orderAccessors = $event->order->accessors;

        if($orderBooks !== null && count($orderBooks) > 0) {
            Order::updateOrderProductsPivotStatus($orderBooks, $event->status);
        }
        if(count($orderAccessors) > 0) {
            Order::updateOrderProductsPivotStatus($orderAccessors, $event->status);
        }

        $event->order->update([
            'status' => $event->status,
        ]);
    }
}
