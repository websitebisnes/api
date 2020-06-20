<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'start_at',
        'end_at',
        'promotion_type',
        'promotion_limit'
    ];

    /**
     * Relationships
     */

    public function products()
    {
        $this->hasMany(PromotionProduct::class);
    }
}
