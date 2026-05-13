<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

// ORDERS
Route::post('/orders', [OrderController::class, 'store']);
Route::get('orders/customers/{customerUuid}', [OrderController::class, 'getByCustomer']);
// CUSTOMERS
Route::post('/customers/{customer_uuid}/orders', [OrderController::class, 'store']);
Route::get('/customers/{customer_uuid}/orders', [OrderController::class, 'getByCustomer']);
Route::post('/customers', [CustomerController::class, 'store']);
Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);
// PRODUCTS
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    Route::apiResources([
        'companies' => CompanyController::class,
        'languages' => LanguageController::class,
        'customers' => CustomerController::class,
        'Products' => ProductController::class,
    ]);
});
