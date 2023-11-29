<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Services\Frontend\OrderService;
use App\Services\Frontend\PaymentService;
use App\Services\Frontend\ShopService;
use Psr\Container\NotFoundExceptionInterface;

class OrderController extends Controller
{
    /**
     * @param OrderService $orderService
     * @param ShopService $shopService
     */
    public function __construct(protected OrderService $orderService, protected ShopService $shopService)
    {
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function index(): \Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $cardProducts = [];
        if (session()->get('cart')) {
            $regions = $this->orderService->getRegions();
            $countries = $this->orderService->getCountries();
            $cardProducts = $this->orderService->getCartProducts();
            $cardProductsTotalPrice = $this->shopService->getCartTotalPrice();

            $data = compact('cardProducts', 'regions', 'countries', 'cardProductsTotalPrice');
        } else {
            $data = compact('cardProducts');
        }

        return view('checkout.checkout', $data);
    }

    /**
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function checkIsProductsAvailable(): bool
    {
        $cardProducts = $this->orderService->getCartProducts();
        $checkProductsInStockCount = count($cardProducts['books']) + count($cardProducts['accessors']);
        $sessionCartProductsId = count(session()->get('cart'));

        return ($sessionCartProductsId === $checkProductsInStockCount);
    }

    /**
     * @param OrderStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(OrderStoreRequest $request)
    {
        try {
            if (!$this->checkIsProductsAvailable()) {
                return redirect()->route('order')->with('product_is_not_in_stock', __('checkout.product_is_not_in_stock'));
            }
        } catch (NotFoundExceptionInterface $e) {
            return redirect()->route('order')->with('product_is_not_in_stock', __('checkout.product_is_not_in_stock'));
        }

        $order = $this->orderService->create($request);
        $payment_service = new PaymentService();

        return $payment_service->makePayment($order);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function success(): \Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('payments.order_success');
    }

    /**
     * @return \Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application\
     */
    public function fail(): \Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('payments.fail');
    }
}
