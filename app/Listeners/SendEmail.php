<?php

namespace App\Listeners;

use App\Events\OrderPayment;
use App\Jobs\OrderAdminJob;
use App\Jobs\OrderUserJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPayment $event): void
    {
        if ($event->isSuccess) {
            OrderAdminJob::dispatch($event->order);
            OrderUserJob::dispatch($event->order);
        }
    }
}
