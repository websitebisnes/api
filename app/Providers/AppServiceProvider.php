<?php

namespace App\Providers;

use App\Address;
use App\Category;
use App\Customer;
use App\Product;
use App\Media;
use App\Observers\AddressObserver;
use App\Observers\CategoryObserver;
use App\Observers\CustomerObserver;
use App\Observers\MediaObserver;
use App\Observers\ProductObserver;
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
        Media::observe(MediaObserver::class);
    }
}
