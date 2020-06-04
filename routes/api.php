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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Product API
    Route::resource('products', 'ProductController');
    Route::delete('products/delete/bulk', 'ProductController@destroy_bulk');

    // Category API
    Route::resource('categories', 'CategoryController');
    Route::delete('categories/delete/bulk', 'CategoryController@destroy_bulk');

    // Media API
    Route::resource('medias', 'MediaController');
    Route::delete('medias/delete/bulk', 'MediaController@destroy_bulk');

    // Customer API
    Route::resource('customers', 'CustomerController');
    Route::delete('customers/delete/bulk', 'CustomerController@destroy_bulk');

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

    Route::resource('resources', 'ResourceController');
});
