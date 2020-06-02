<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'sku',
        'price',
        'price_discount',
        'stock',
        'category_id',
        'weight',
        'height',
        'width'
    ];

    protected $hidden = [
        'user_id',
        'laravel_through_key'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function media()
    {
        return $this->hasManyThrough(Media::class, ProductMedia::class, 'product_id', 'id', 'id', 'media_id');
    }
}
