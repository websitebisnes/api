<?php

namespace App\Observers;

use App\Address;

class AddressObserver
{
    /**
     * Handle the Address "saving" event.
     *
     * @param  \App\Address  $Address
     * @return void
     */
    public function saving(Address $address)
    {
        $address->user_id = request()->user()->id;
    }
}
