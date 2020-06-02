<?php

namespace App\Observers;

use App\Customer;

class CustomerObserver
{
    /**
     * Handle the customer "saving" event.
     *
     * @param  \App\Customer  $customer
     * @return void
     */
    public function saving(Customer $customer)
    {
        $customer->user_id = request()->user()->id;
    }
}
