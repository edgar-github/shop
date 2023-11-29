<?php

namespace App\Services\Frontend;

use App\Models\Country;
use App\Models\Order;
use App\Models\Region;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;

class OrderService
{
    use GeneralTrait;

    public function getRegions()
    {
        return Region::where('status', true)->orderBy('order')->get();
    }

    public function getCountries()
    {
        return Country::where('status', true)->orderBy('order')->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getCartProducts(): \Illuminate\Database\Eloquent\Collection|array
    {
        return self::separateProductsSessionIDAndGetProducts(session()->get('cart'));
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function create(Request $request): mixed
    {
        $country = Country::find($request->country_id);
        $shipping_price = $country?->shipping_price ?: 0;

        $request->request->add([
            'total_price' => (session()->get('total_price') + $shipping_price),
            'total_price_with_discount' => (session()->get('total_price') + $shipping_price),
        ]);

        $order = Order::create($request->except(['_token', 'terms']));

        $this->createOrderProducts($order);

        return $order;
    }

    /**
     * @param Order $order
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function createOrderProducts(Order $order)
    {
        $order->load(['region', 'country']);
        $cart = session()->get('cart');
        $total_price = 0;

        if ($cart && is_array($cart)) {

            $products = self::separateProductsSessionIDAndGetProducts($cart);

            foreach ($products['accessors'] as $product) {
                $total_price = self::orderProductsPivotAttach($order, $product, $cart, $total_price);
            }
            foreach ($products['books'] as $product) {
                $total_price = self::orderProductsPivotAttach($order, $product, $cart, $total_price);
            }
        }

        $order->update([
            'total_price' => $total_price + ($order->country->shipping_price ?: 0),
        ]);
    }

    /**
     * @param $order
     * @param $product
     * @param $cart
     * @param $total_price
     * @return float|int
     */
    public static function orderProductsPivotAttach($order, $product, $cart, $total_price): float|int
    {
        $total_price += $product->price * $cart[$product->category->type . '-' . $product->id]['product_count'];
        $product->save();

        $order->books()->attach($product->id,
            [
                'quantity' => $cart[$product->category->type . '-' . $product->id]['product_count'],
                'price' => $product->price,
                'status' => Order::STATUS_NEW,
                'product_type' => $product->category->type
            ]);

        return $total_price;
    }

}
