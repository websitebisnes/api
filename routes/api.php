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

    Route::resource('products', 'ProductController');
    Route::delete('products/delete/bulk', 'ProductController@destroy_bulk');

    Route::resource('categories', 'CategoryController');
    Route::delete('categories/delete/bulk', 'CategoryController@destroy_bulk');

    Route::resource('medias', 'MediaController');
    Route::delete('medias/delete/bulk', 'MediaController@destroy_bulk');

    Route::resource('customers', 'CustomerController');
    Route::delete('customers/delete/bulk', 'CustomerController@destroy_bulk');

    Route::resource('addresses', 'AddressController');
    Route::delete('addresses/delete/bulk', 'AddressController@destroy_bulk');
});
