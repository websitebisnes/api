<?php

namespace App\Observers;

use App\Courier;

class CourierObserver
{
    /**
     * Handle the courier "saving" event.
     *
     * @param  \App\Courier  $courier
     * @return void
     */
    public function saving(Courier $courier)
    {
        $courier->user_id = request()->user()->id;
    }
}
