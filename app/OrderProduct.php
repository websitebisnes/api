<?php

namespace App;

use App\Scopes\UserScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id',
        'order_id',
        'product_id',
        'quantity',
        'price',
        'price_discount'
    ];

    protected $hidden = [
        'user_id'
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

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')->withTrashed();
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
