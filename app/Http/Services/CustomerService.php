<?php

namespace App\Http\Services;

use App\Customer;

class CustomerService
{
    public static function total_customer()
    {
        return Customer::count();
    }
}
