<?php

namespace App\Observers;

use App\Media;

class GlobalObserver
{
    /**
     * Handle the media "saving" event.
     *
     * @param  \App\Media  $media
     * @return void
     */
    public function retrieved(Media $media)
    {
        $media->user_id = request()->user()->id;
    }
}
