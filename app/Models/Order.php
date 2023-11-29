<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    use HasFactory;

    const PAYMENT_METHOD_BANK = 1;
    const PAYMENT_METHOD_IDRAM = 2;
    const PAYMENT_METHOD_TELCELL = 3;

    const PAYMENT_METHODS = [
        self::PAYMENT_METHOD_BANK,
        self::PAYMENT_METHOD_IDRAM,
        self::PAYMENT_METHOD_TELCELL,
//        self::PAYMENT_METHOD_GIFT_CARD,   #TODO: uncomment when gift card payment method is ready
    ];

    const STATUS_NEW = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_FAILED = 4;
    const STATUS_RETURNED = 5;

    const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_PROCESSING,
        self::STATUS_COMPLETED,
        self::STATUS_FAILED,
        self::STATUS_RETURNED,
    ];

    protected $fillable = [
        'order_payment_id',
        'name',
        'lastname',
        'email',
        'phone',
        'street',
        'house',
        'apartment',
        'entrance',
        'company',
        'comment',
        'payment_method',
        'country_id',
        'region_id',
        'region',
        'user_id',
        'total_price',
        'total_price_with_discount',
        'payment_callback',
        'order_text',
        'postal_code',
        'status',
    ];

    /**
     * @param $products
     * @return array[]
     */
    public static function filterProductsTypeId($products): array
    {
        $sessionBookId = [];
        $sessionAccessorId = [];
        foreach ($products as $product) {
            match ($product->pivot->product_type) {
                Categories::TYPE_BOOK => $sessionBookId[$product->pivot->product_id] = $product->pivot->quantity,
                Categories::TYPE_ACCESSOR => $sessionAccessorId[$product->pivot->product_id] = $product->pivot->quantity,
            };
        }

        return ['books_id' => $sessionBookId, 'accessors_id' => $sessionAccessorId];
    }

    /**
     * @param $order
     * @return void
     */
    public static function changeInStock($order): void
    {
        $sessionBook = self::filterProductsTypeId($order->books);
        $sessionAccessor = self::filterProductsTypeId($order->accessors);

        if (count($sessionBook['books_id']) || count($sessionAccessor['accessors_id'])) {
            if (count($sessionAccessor['accessors_id'])) {
                Accessor::changeInStockAfterOrder($sessionAccessor['accessors_id']);
            }
            if (count($sessionBook['books_id'])) {
                Books::changeInStockAfterOrder($sessionBook['books_id']);
            }
        }
    }

    /**
     * @return Model|\Illuminate\Database\Eloquent\Builder
     */
    public static function getOrderWithProducts(): Model|\Illuminate\Database\Eloquent\Builder
    {
        return Order::with(['country',
            'books' => function ($query) {
                $query->where('product_type', 'book');
            },
            'accessors' => function ($query) {
                $query->where('product_type', 'accessor');
            }])
            ->orderBy('id', 'DESC')->firstOrFail();
    }

    /**
     * @param $order_payment_id
     * @return mixed
     */
    public static function getOrderWithProductsByPaymentId($order_payment_id)
    {
        return Order::where('order_payment_id', $order_payment_id)
            ->with(['country',
                'books' => function ($query) {
                    $query->where('product_type', 'book');
                },
                'accessors' => function ($query) {
                    $query->where('product_type', 'accessor');
                }])
            ->orderBy('id', 'DESC')->firstOrFail();
    }

    /**
     * @param $products
     * @param $status
     * @return void
     */
    public static function updateOrderProductsPivotStatus($products, $status): void
    {
        foreach ($products as $product) {
            $product->pivot->update([
                'status' => $status,
            ]);
        }
    }

    //on create
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_payment_id = rand(1000000, 9999999) . time();
            $order->status = self::STATUS_NEW;
        });
    }

    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function region(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function books(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Books::class, 'order_product_pivote', 'order_id', 'product_id')
            ->withPivot('id', 'quantity', 'price', 'status', 'product_type')
            ->withTimestamps();
    }

    public function accessors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Accessor::class, 'order_product_pivote', 'order_id', 'product_id')
            ->withPivot('id', 'quantity', 'price', 'status', 'product_type')
            ->withTimestamps();
    }
}
