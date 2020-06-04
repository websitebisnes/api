<?php

namespace App\Observers;

use App\Shipment;

class ShipmentObserver
{
    /**
     * Handle the shipment "saving" event.
     *
     * @param  \App\Shipment  $shipment
     * @return void
     */
    public function saving(Shipment $shipment)
    {
        $shipment->user_id = request()->user()->id;
    }
}
