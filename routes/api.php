<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/orders', [OrderController::class, 'store']);
Route::post('/customers/{customer_uuid}/orders', [OrderController::class, 'store']);
Route::get('/customers/{customer_uuid}/orders', [OrderController::class, 'getByCustomer']);
Route::get('orders/customers/{customerUuid}', [OrderController::class, 'getByCustomer']);
Route::post('/products', [ProductController::class, 'store']);
Route::post('/customers', [CustomerController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    Route::apiResources([
        'companies' => CompanyController::class,
        'languages' => LanguageController::class,
        'customers' => CustomerController::class,
        'Products' => ProductController::class,
    ]);
});
