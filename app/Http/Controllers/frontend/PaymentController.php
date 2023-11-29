<?php

namespace App\Http\Controllers\frontend;

use App\Events\CouponQuantity;
use App\Http\Controllers\Controller;

use App\Services\Frontend\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function __construct(protected PaymentService $paymentService)
    {

    }

    public function success()
    {
        if (session()->get('coupon')) {
            event(new CouponQuantity(session()->get('coupon')));
        }
        session()->forget('cart');

        return view('payments.order_success');
    }

    public function fail()
    {
        return view('payments.fail');
    }

    public function idramCallback(Request $request)
    {
        return $this->paymentService->idramCallback($request);
    }

    public function telcellCallback(Request $request)
    {
        return $this->paymentService->telcellCallback($request);
    }

    public function telcellRedirect(Request $request)
    {
        return $this->paymentService->telcellRedirect($request);
    }

    public function arcaCallback(Request $request)
    {
        return $this->paymentService->arcaCallback($request);
    }
}
