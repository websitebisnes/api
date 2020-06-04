<?php

namespace App;

use App\Scopes\UserScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Askedio\SoftCascade\Traits\SoftCascadeTrait;

class Category extends Model
{
    use SoftDeletes, SoftCascadeTrait;

    protected $softCascade = ['products'];

    protected $fillable = [
        'user_id',
        'name',
        'slug'
    ];

    protected $hidden = [
        'user_id',
        'deleted_at'
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

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
