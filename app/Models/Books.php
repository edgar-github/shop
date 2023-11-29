<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Books extends Model
{
    use HasFactory, SoftDeletes;

    const INACTIVE = 0;

    const ACTIVE = 1;

    const API_LAST_BOOKS_LIMIT = 6;

    const HOME_PAGE_BOOKS_COUNT = 4;

    const BOOK_IMAGE_PATH = 'images/books';

    protected $fillable = [
        'title_hy',
        'title_en',
        'text_hy',
        'text_en',
        'description_hy',
        'description_en',
        'book_size_hy',
        'book_size_en',
        'video_url',
        'slug',
        'price',
        'word_count',
        'page_count',
        'font_size',
        'isbn',
        'in_stock',
        'main_image',
        'category_id',
        'published_date',
        'status',
    ];

    /**
     * @param $sessionBookId
     * @return void
     */
    public static function changeInStockAfterOrder($booksId): void
    {
        $getBooksId = array_keys($booksId);
        $books = Books::whereIn('id', $getBooksId)->get();
        foreach ($books as $book) {
            $oldInStock = $book->in_stock;
            $newInStock = (int)$booksId[$book->id];
            $quantityToSubtract = $oldInStock - $newInStock;
            $book->in_stock = $quantityToSubtract;
            $book->save();
        }
    }

    /**
     * @param $bookId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function constructOtherBooksQuery($bookId): \Illuminate\Database\Eloquent\Builder
    {
        return Books::with(['authors' => function ($query) {
            $query->select('authors.id', 'authors.name_hy', 'authors.name_en');
        }])->where('id', '!=', $bookId)
            ->where('status', Books::ACTIVE)
            ->inRandomOrder()
            ->limit(4);
    }

    /**
     * @return string
     */
    public static function getBookIdByUrl(): string
    {
        try {
            $getPostSlug = last(explode('/', url()->previous()));
            return Books::where('slug', $getPostSlug)->firstOrFail()->id;
        }
        catch (\Exception $e) {
             return $e->getMessage();
        }
    }

    /**
     * @param $url
     * @return string
     */
    public static function filterYoutubeUrl($url): string
    {
       return str_replace('watch?v=', 'embed/', $url);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Categories::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function authors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Authors::class, 'book_authors_pivot', 'book_id', 'author_id')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function translators(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Translators::class, 'book_translators_pivot', 'book_id', 'translator_id')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function images()
    {
        return $this->morphMany(Images::class, 'imageable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_product_pivote', 'book_id', 'order_id')
            ->withPivot('id', 'quantity', 'price', 'status');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(ProductComments::class, 'commentable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function accessors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Accessor::class, 'accessor_books', 'book_id', 'accessor_id');
    }

}
