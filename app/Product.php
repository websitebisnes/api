<?php

namespace App;

use App\Scopes\UserScope;
use Carbon\Carbon;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, Filterable;

    protected $fillable = [
        'user_id',
        'name',
        'category_id',
        'detail_short',
        'slug',
        'sku',
        'price',
        'price_discount',
        'stock',
        'stock_status',
        'deduct_stock',
        'price_data',
        'attributes',
        'stock_data',
        'variations'
    ];

    protected $with = [
        'media',
        'product_detail'
    ];

    protected $hidden = [
        'user_id',
        'laravel_through_key',
        'deleted_at'
    ];

    protected $casts = [
        'price_data' => 'array',
        'attributes' => 'array',
        'stock_data' => 'array',
        'variations' => 'array'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new UserScope);
        static::retrieved(function ($model) {
            $model->created_at = Carbon::createFromTimestamp(strtotime($model->created_at))
                ->timezone('Asia/Kuala_Lumpur')
                ->toDateTimeString();
        });
    }

    /**
     * Relationships
     * 
     */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function media()
    {
        return $this->hasManyThrough(Media::class, ProductMedia::class, 'product_id', 'id', 'id', 'media_id');
    }

    public function product_variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function product_detail()
    {
        return $this->hasOne(ProductDetail::class);
    }

    /**
     * Accessors
     */
}
