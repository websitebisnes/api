<?php

namespace App\Observers;

use App\Order;

class OrderObserver
{
    /**
     * Handle the order "saving" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function saving(Order $order)
    {
        $order->user_id = request()->user()->id;
    }
}
