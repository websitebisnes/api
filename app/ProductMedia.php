<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductMedia extends Model
{
    protected $fillable = [
        'product_id',
        'media_id'
    ];
}
