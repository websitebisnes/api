<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
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
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'file_properties' => 'array'
    ];

    /**
     * Accessor
     */

    protected function getFullurlAttribute()
    {
        if ($this->is_cloud) {
            return "https://teamsales-my.s3.amazonaws.com/" . $this->filename;
        } else {
            return asset('storage/' . $this->filename);
        }
    }

    protected function getThumbnailAttribute()
    {
        if ($this->is_cloud) {
            return "https://teamsales-my.s3.amazonaws.com/thumbnail/" . $this->filename;
        } else {
            return asset('storage/thumbnail/' . $this->filename);
        }
    }
}
