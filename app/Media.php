<?php

namespace App;

use App\Scopes\UserScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'filename',
        'file_properties',
        'is_cloud',
        'is_deleted_cleaned'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_id',
        'deleted_at',
        'laravel_through_key',
        'deleted_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'file_properties' => 'array'
    ];

    protected $appends = [
        'thumbnail'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        //static::addGlobalScope(new UserScope);
        static::retrieved(function ($model) {
            $model->created_at = Carbon::createFromTimestamp(strtotime($model->created_at))
                ->timezone('Asia/Kuala_Lumpur')
                ->toDateTimeString();
        });
    }

    /**
     * Accessor
     */

    protected function getFilenameAttribute($value)
    {
        if ($this->is_cloud) {
            return "https://teamsales-my.s3.amazonaws.com/" . $value;
        } else {
            return asset('storage/' . $value);
        }
    }

    protected function getThumbnailAttribute()
    {
        if ($this->is_cloud) {
            return "https://teamsales-my.s3.amazonaws.com/thumbnail/" . $this->attributes['filename'];
        } else {
            return asset('storage/thumbnail/' . $this->filename);
        }
    }
}
