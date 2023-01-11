<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Login;
use App\Http\Controllers\Product;
use App\Http\Controllers\Stock;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Sale;
use App\Http\Controllers\Report;

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

Route::post('registration', [Login::class, 'registration']);
Route::post('login', [Login::class, 'login']);

Route::controller(AuthController::class)->middleware("auth:api")->group(function () {
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::post('saveProduct', [Product::class, 'saveProduct']);
    Route::post('manageProduct', [Product::class, 'manageProduct']);
    Route::post('selectProduct/{id}', [Product::class, 'selectProduct']);
    Route::post('updateProduct', [Product::class, 'updateProduct']);
    Route::post('deleteProduct/{id}', [Product::class, 'deleteProduct']);

    Route::post('saveStock', [Stock::class, 'saveStock']);
    Route::post('manageStock', [Stock::class, 'manageStock']);

    Route::post('saveCustomer', [Customer::class, 'saveCustomer']);
    Route::post('manageCustomer', [Customer::class, 'manageCustomer']);
    Route::post('selectCustomer/{id}', [Customer::class, 'selectCustomer']);
    Route::post('updateCustomer', [Customer::class, 'updateCustomer']);
    Route::post('deleteCustomer/{id}', [Customer::class, 'deleteCustomer']);
  
    Route::post('manageSale', [Sale::class, 'manageSale']);
});