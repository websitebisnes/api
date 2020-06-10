<?php

namespace App;

use App\Http\Services\AddressService;
use App\Scopes\UserScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'address',
        'address2',
        'city',
        'state',
        'postcode',
        'country'
    ];

    protected $hidden = [
        'user_id'
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
     * Accessors
     */

    // Country
    public function getCountryAttribute($value)
    {
        return AddressService::countries()[$value];
    }

    // State
    public function getStateAttribute($value)
    {
        return AddressService::states()[$value];
    }

    // City
    public function getCityAttribute($value)
    {
        return AddressService::cities($this->attributes['state'])[$value];
    }
}
