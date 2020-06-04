<?php

namespace App;

use App\Scopes\UserScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'shipping_status',
        'shipping_method',
        'weight',
        'courier_data'
    ];

    protected $hidden = [
        'user_id'
    ];

    protected $attributes = [
        'shipping_status' => 1,
        'shipping_method' => 0,
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
}
