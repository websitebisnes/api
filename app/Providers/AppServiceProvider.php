<?php

namespace App\Providers;

use App\Address;
use App\Cart;
use App\Category;
use App\Courier;
use App\Customer;
use App\Product;
use App\Media;
use App\Order;
use App\Payment;
use App\Shipment;

use App\Observers\AddressObserver;
use App\Observers\CartObserver;
use App\Observers\CategoryObserver;
use App\Observers\CourierObserver;
use App\Observers\CustomerObserver;
use App\Observers\MediaObserver;
use App\Observers\OrderObserver;
use App\Observers\PaymentObserver;
use App\Observers\ProductObserver;
use App\Observers\ShipmentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Category::observe(CategoryObserver::class);
        Product::observe(ProductObserver::class);
        Customer::observe(CustomerObserver::class);
        Address::observe(AddressObserver::class);
        //Media::observe(MediaObserver::class);
        Order::observe(OrderObserver::class);
        Payment::observe(PaymentObserver::class);
        Shipment::observe(ShipmentObserver::class);
        Courier::observe(CourierObserver::class);
        Cart::observe(CartObserver::class);
    }
}
