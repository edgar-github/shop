<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Services\Frontend\ShopService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ShopController extends Controller
{

    /**
     * @param ShopService $shopService
     */
    public function __construct(protected ShopService $shopService)
    {
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function addToCart(Request $request)
    {
        $request->only(['quantity', 'product',]);

        if(in_array($request->product_type, Categories::PRODUCTS_TYPE)) {
            $this->shopService->addToCart($request);
        } else {
            abort(404);
        }

        return response()->json([
            'success' => true,
            'cartProductsCount' => $this->shopService->getCartProductsCount(),
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function updateCart(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->shopService->updateCart($request);

        if ($request->coupon && CouponController::checkCouponIsValid($request->coupon)) {
            $getTotalPrice = CouponController::checkCoupon($request, true);
        } else {
            $getTotalPrice = ShopService::getCartTotalPrice() ;
        }

       return $this->returnTotalPriceResponse($getTotalPrice);
    }

    /**
     * @param $getTotalPrice
     * @param $coupon
     * @param bool $success
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function returnTotalPriceResponse($getTotalPrice, $coupon = null, bool $success = true, string $message = ''): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'total_price' => $getTotalPrice,
            'coupon' => $coupon,
            'success' => $success,
            'message' => $message,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function removeFromCart(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->only(['product_id', 'product_type']);
        $this->shopService->removeFromCart($request);

        return response()->json([
            'success' => true,
            'cartProductsCount' => $this->shopService->getCartProductsCount(),
        ]);
    }

}
