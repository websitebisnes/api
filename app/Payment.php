<?php

namespace App;

use App\Http\Services\PaymentService;
use App\Scopes\UserScope;
use Carbon\Carbon;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes, Filterable;

    protected $fillable = [
        'user_id',
        'order_id',
        'payment_status',
        'payment_method',
        'amount',
        'paid_at'
    ];

    protected $hidden = [
        'user_id'
    ];

    protected $attributes = [
        'payment_status' => 1
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
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Accesors
     */

    // Column: payment_status
    public function getPaymentStatusAttribute($value)
    {
        return PaymentService::payment_status($value);
    }

    // Column: payment_method
    public function getPaymentMethodAttribute($value)
    {
        return PaymentService::payment_method($value);
    }
}
