<?php

namespace App;

use App\Scopes\UserScope;
use Carbon\Carbon;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Courier extends Model
{
    use SoftDeletes, Filterable;

    protected $fillable = [
        'user_id',
        'courier_id',
        'name',
        'config',
        'data',
        'is_enabled'
    ];

    protected $hidden = [
        'user_id'
    ];

    protected $casts = [
        'config' => 'array',
        'data' => 'array'
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
