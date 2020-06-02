<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug'
    ];

    protected $hidden = [
        'user_id',
        'deleted_at'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
