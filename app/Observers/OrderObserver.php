<?php

namespace App\Observers;

use App\Models\Books;
use App\Models\Order;

class OrderObserver
{
    /**
     * @param Order $order
     * @return void
     */
    public function updated(Order $order): void
    {
        if ($order->status === Order::STATUS_COMPLETED) {
            try {
                Order::changeInStock($order);
            } catch (\Exception  $e) {
                echo $e->getMessage();
            }
        }
    }

}
