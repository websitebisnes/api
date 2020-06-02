<?php

namespace App\Observers;

use App\Media;

class MediaObserver
{
    /**
     * Handle the media "saving" event.
     *
     * @param  \App\Media  $media
     * @return void
     */
    public function saving(Media $media)
    {
        $media->user_id = request()->user()->id;
    }
}
