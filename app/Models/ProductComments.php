<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductComments extends Model
{
    use HasFactory, SoftDeletes;

    const PUBLISHED = 1;
    const NOT_PUBLISHED = 0;

    protected $table = 'product_comments';

    protected $fillable = [
        'commentable_id',
        'commentable_type',
        'full_name',
        'email',
        'comment',
    ];

    /**
     * @param $status
     * @param $id
     * @return void
     */
    public static function updateStatus($status, $id): void
    {
        $getComment = ProductComments::findOrFail($id);
        $getComment->is_active = $status;
        $getComment->save();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function book(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Books::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function accessor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Accessor::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function commentable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
}
