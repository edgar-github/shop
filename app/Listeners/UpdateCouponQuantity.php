<?php

namespace App\Listeners;

use App\Models\Coupon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateCouponQuantity
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
    public function handle($coupon): void
    {
        Coupon::updateCouponQuantity($coupon->coupon);
        session()->forget('coupon');
    }
}
