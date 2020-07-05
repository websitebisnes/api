<?php

namespace App;

use App\Http\Services\CourierService;
use App\Http\Services\PaymentService;
use App\Http\Services\ShipmentService;
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

    /**
     * Relationships
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'order_id', 'order_id', Order::class);
    }

    public function payment_paid()
    {
        return $this->belongsTo(Payment::class, 'order_id', 'order_id', Order::class)->where('payment_status', '=', PaymentService::PAYMENT_STATUS_PAID);
    }

    /**
     * Accessors
     */

    public function getShippingStatusAttribute($value)
    {
        return ShipmentService::shipment_status($value);
    }

    public function getShippingMethodAttribute($value)
    {
        return ShipmentService::shipment_method($value);
    }
}
