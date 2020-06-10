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
        'slug',
        'sku',
        'price',
        'price_discount',
        'price_wholesale',
        'stock',
        'deduct_stock',
        'category_id',
        'pre_order',
        'weight',
        'height',
        'width'
    ];

    protected $with = [
        'media'
    ];

    protected $hidden = [
        'user_id',
        'laravel_through_key',
        'deleted_at'
    ];

    protected $appends = [
        'price_effective'
    ];

    protected $casts = [
        'price_wholesale'
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

    // custom: price_effective
    public function getPriceEffectiveAttribute()
    {
        if (intval($this->attributes['price_discount'])) {
            return $this->attributes['price_discount'];
        }

        return $this->attributes['price'];
    }
}
