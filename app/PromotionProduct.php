<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromotionProduct extends Model
{
    protected $fillable = [
        'promotion_id',
        'product_id',
    ];

    /**
     * Relationships
     */

    public function promotion()
    {
        $this->belongsTo(Promotion::class);
    }
}
