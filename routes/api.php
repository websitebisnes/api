<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', 'UserController@authenticate');
Route::post('/register', 'UserController@store');
Route::post('/forgot', 'UserController@recover');
Route::get('/users/email', 'UserController@check_email');
Route::get('/users/subdomain', 'UserController@check_subdomain');
Route::post('/users/subdomain', 'UserController@get_user_by_subdomain');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', 'UserController@users');
    Route::post('/users', 'UserController@update');

    Route::post('/phone/{action}', 'UserController@phone');

    // Product API
    Route::resource('products', 'ProductController');
    Route::delete('products/delete/bulk', 'ProductController@destroy_bulk');
    Route::post('/products/check', 'ProductController@check');

    // Cart API
    Route::resource('carts', 'CartController');

    // Product Promotion API
    Route::resource('promotions', 'PromotionController');
    Route::get('promotions/helper/types', 'PromotionController@types');

    // Category API
    Route::resource('categories', 'CategoryController');
    Route::delete('categories/delete/bulk', 'CategoryController@destroy_bulk');

    // Media API
    Route::resource('medias', 'MediaController');
    Route::delete('medias/delete/bulk', 'MediaController@destroy_bulk');

    // Customer API
    Route::resource('customers', 'CustomerController');
    Route::delete('customers/delete/bulk', 'CustomerController@destroy_bulk');
    Route::post('customers/check/email', 'CustomerController@check_email');

    // Address API
    Route::resource('addresses', 'AddressController');
    Route::delete('addresses/delete/bulk', 'AddressController@destroy_bulk');

    // Order API
    Route::resource('orders', 'OrderController');
    Route::delete('orders/delete/bulk', 'OrderController@destroy_bulk');

    // OrderReceipt API
    Route::resource('orderreceipts', 'OrderReceiptController');

    // Payment API
    Route::resource('payments', 'PaymentController');
    Route::delete('payments/delete/bulk', 'PaymentController@destroy_bulk');

    // Shipment API
    Route::resource('shipments', 'ShipmentController');
    Route::delete('shipments/delete/bulk', 'ShipmentController@destroy_bulk');

    // Courier API
    Route::resource('couriers', 'CourierController');
    Route::get('courierslist', 'CourierController@supported_couriers');
    Route::post('couriers/rates/{courier}/{order}', 'CourierController@courier_get_rates');
    Route::get('couriers/status/{courier}', 'CourierController@courier_get_status');

    // Resource API
    Route::resource('resources', 'ResourceController');

    // Address API
    Route::get('address/states', 'AddressController@get_states');
    Route::get('address/cities', 'AddressController@get_cities');
});
