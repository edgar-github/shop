<?php

namespace App\Jobs;

use App\Mail\OrderUserMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class OrderUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Order $order)
    {

    }

    /**
     * @return void
     */
    public function handle(): void
    {
        try {
            Mail::to($this->order->email)->send(new OrderUserMail($this->order));
        } catch (\Exception $e) {
            info('OrderUserJob: error-2: ' . $e->getMessage());
        }
    }
}
