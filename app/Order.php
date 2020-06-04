<?php

namespace App;

use App\Http\Services\OrderService;
use App\Scopes\UserScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'customer_id',
        'order_status'
    ];

    protected $hidden = [
        'user_id'
    ];

    protected $attributes = [
        'order_status' => 1
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
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order_products()
    {
        $OrderProduct = new OrderProduct();
        return $this->hasMany(OrderProduct::class)->select($OrderProduct->getFillable());
    }

    public function order_receipts()
    {
        $OrderReceipt = new OrderReceipt();
        return $this->hasMany(OrderReceipt::class)->select($OrderReceipt->getFillable());
    }

    public function payment()
    {
        $Payment = new Payment();
        return $this->hasOne(Payment::class)->select($Payment->getFillable());
    }

    public function shipment()
    {
        $Shipment = new Shipment();
        return $this->hasOne(Shipment::class)->select($Shipment->getFillable());
    }

    /**
     * Accesors
     */

    // order_status
    public function getOrderStatusAttribute($value)
    {
        return OrderService::order_status($value);
    }
}
