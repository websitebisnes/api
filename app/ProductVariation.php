<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'color',
        'size',
        'price',
        'stock'
    ];

    protected $hidden = [
        'user_id',
        'laravel_through_key',
        'deleted_at'
    ];
}
