<?php

namespace App\Observers;

use App\Payment;

class PaymentObserver
{
    /**
     * Handle the payment "saving" event.
     *
     * @param  \App\Payment  $payment
     * @return void
     */
    public function saving(Payment $payment)
    {
        $payment->user_id = request()->user()->id;
    }
}
