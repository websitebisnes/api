<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class OrderReceipt extends Model
{
    protected $fillable = [
        'order_id',
        'image_id',
        'verified_at'
    ];

    protected $with = [
        'image'
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
     * Model resource
     */

    protected static function updateable()
    {
        return [
            'verified_at' => 'timestamp'
        ];
    }


    /**
     * Relationships
     * 
     */
    public function image()
    {
        return $this->hasOne(Media::class, 'id', 'image_id');
    }
}
