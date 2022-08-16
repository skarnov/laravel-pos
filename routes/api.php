<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product;
use App\Http\Controllers\Customer;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('saveProduct', [Product::class, 'saveProduct']);
Route::get('manageProduct', [Product::class, 'manageProduct']);
Route::get('searchProduct/{key}', [Product::class, 'searchProduct']);




Route::post('saveCustomer', [Customer::class, 'saveCustomer']);
Route::get('manageCustomer', [Customer::class, 'manageCustomer']);
Route::get('selectCustomer/{customer_id}', [Customer::class, 'selectCustomer']);
Route::post('updateCustomer', [Customer::class, 'updateCustomer']);
Route::get('deleteCustomer/{customer_id}', [Customer::class, 'deleteCustomer']);
