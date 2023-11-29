<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use App\Events\OrderPayment;

class SendTestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-test-mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $order = Order::latest()->first();

        event(new OrderPayment(true, $order, Order::STATUS_COMPLETED));
    }
}
