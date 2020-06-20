<?php

namespace App\Observers;

use App\Cart;

class CartObserver
{
    /**
     * Handle the cart "saving" event.
     *
     * @param  \App\Cart  $cart
     * @return void
     */
    public function saving(Cart $cart)
    {
        $cart->user_id = request()->user()->id;
    }
}
