<?php

namespace App\Observers;

use App\Product;

class ProductObserver
{
    /**
     * Handle the customer "saving" event.
     *
     * @param  \App\Customer  $customer
     * @return void
     */
    public function saving(Product $product)
    {
        $product->user_id = request()->user()->id;
    }
}
