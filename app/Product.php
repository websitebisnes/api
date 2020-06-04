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
        'stock',
        'category_id',
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
