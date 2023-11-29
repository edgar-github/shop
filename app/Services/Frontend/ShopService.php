<?php

namespace App\Services\Frontend;

use App\Models\Categories;
use App\Models\Coupon;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;

class ShopService
{
    use GeneralTrait;

    /**
     * @param Request $request
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function addToCart(Request $request): void
    {
        $prod = [
            $request->product_type . '-' . $request->product => [
                'product_type' => $request->product_type,
                'product_id' => $request->product,
                'product_count' => $request->quantity,
            ]
        ];
        $cart = session()->get('cart');
        if (!$cart) {
            session()->put('cart', $prod);
        } else {
            $productInfo = $cart[$request->product_type . '-' . $request->product] ?? false;
            if ($productInfo) {
                $cart[$request->product_type . '-' . $request->product]['product_id'] = $request->product;
                $cart[$request->product_type . '-' . $request->product]['product_count'] += $request->quantity;
                $cart[$request->product_type . '-' . $request->product]['product_type'] = $request->product_type;
            } else {
                $cart[$request->product_type . '-' . $request->product]['product_id'] = $request->product;
                $cart[$request->product_type . '-' . $request->product]['product_count'] = $request->quantity;
                $cart[$request->product_type . '-' . $request->product]['product_type'] = $request->product_type;
            }
            session()->put('cart', $cart);
        }

    }

    /**
     * @return int
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getCartProductsCount(): int
    {
        $cart = session()->get('cart');
        $total_count = 0;
        if ($cart && is_array($cart)) {
            $total_count = count($cart);
        }

        return $total_count;
    }

    /**
     * @param Request $request
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function updateCart(Request $request): void
    {
        $cart = session()->get('cart');

        if ($cart && is_array($cart)) {
            if (isset($cart[$request->productType . '-' . $request->product_id])) {
                $cart[$request->productType . '-' . $request->product_id]['product_id'] = $request->product_id;
                $cart[$request->productType . '-' . $request->product_id]['product_count'] = $request->quantity;
                $cart[$request->productType . '-' . $request->product_id]['product_type'] = $request->productType;

                session()->put('cart', $cart);
            }
        }
    }

    /**
     * @param Request $request
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function removeFromCart(Request $request): void
    {
        $cart = session()->get('cart');

        if (isset($cart[$request->product_type . '-' . $request->product_id])) {
            unset($cart[$request->product_type . '-' . $request->product_id]);
            session()->put('cart', $cart);
        }
    }

    /**
     * @return float|int
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function getCartTotalPrice($couponDiscount = false, $productsId = [], $total_price = 0, $couponType = null): float|int
    {
        $cart = session()->get('cart');
        if ($cart && is_array($cart)) {
            $products = self::separateProductsSessionIDAndGetProducts($cart);
            $sessionBookId = [];
            $sessionAccessorId = [];
            foreach ($cart as $cartValue) {
                match ($cartValue['product_type']) {
                    Categories::TYPE_BOOK => $sessionBookId[] = $cartValue['product_id'],
                    Categories::TYPE_ACCESSOR => $sessionAccessorId[] = $cartValue['product_id'],
                };
            }

            $allProducts = false;
            if ($productsId === Coupon::ALL_PRODUCTS) {
                $allProducts = true;
                $productsId = [];
                $productsId['books_id'] = $sessionBookId;
                $productsId['accessors_id'] = $sessionAccessorId;
            }

            if(isset($products['books'])) {
                foreach ($products['books'] as $product) {
                    $total_price = self::calculateTotalPrice($product, $productsId['books_id'] ?? $sessionBookId, $couponDiscount, $couponType, $allProducts, $total_price, $cart);
                }
            }

            if(isset($products['accessors'])) {
                foreach ($products['accessors'] as $product) {
                    $total_price = self::calculateTotalPrice($product, $productsId['accessors_id'] ?? $sessionAccessorId, $couponDiscount, $couponType, $allProducts, $total_price, $cart);
                }
            }

            if ($couponType === Coupon::SINGLE_BOOK && $couponDiscount && $allProducts) {
                $total_price = $total_price - ($couponDiscount * count(array_filter($products)));
            }
        }
        session()->put('total_price', $total_price);

        return $total_price;
    }

    /**
     * @param $product
     * @param $productsId
     * @param $couponDiscount
     * @param $couponType
     * @param $allProducts
     * @param $total_price
     * @param $cart
     * @return float|int
     */
    public static function calculateTotalPrice($product, $productsId, $couponDiscount, $couponType, $allProducts, $total_price, $cart): float|int
    {
        $productCount = $cart[$product->category->type . '-' . $product->id]['product_count'];
        if (!$allProducts && in_array($product->id, $productsId)) {
            if ($couponType === Coupon::EACH_BOOKS) {
                $total_price += ($product->price * $productCount) - ($couponDiscount * $productCount);
            } else {
                $total_price += ($product->price * $productCount) - $couponDiscount;
            }
        } else if ($couponDiscount && $couponType === Coupon::EACH_BOOKS && $allProducts) {
            $total_price += ($product->price - $couponDiscount) * $productCount;
        } else {
            $total_price += $product->price * $productCount;
        }
        return $total_price;
    }

}
