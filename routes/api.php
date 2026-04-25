<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::post('/customers', [CustomerController::class, 'store']);
Route::post('/products', [ProductController::class, 'store']);

Route::post('/orders', [OrderController::class, 'store']);
Route::get('/customers/{customer_uuid}/orders', [OrderController::class, 'customerOrders']);

Route::middleware('auth:sanctum')->group(function () {
    // Route::delete('/logout', [AuthController::class, 'logout']);

    // Route::apiResources([
    //     'companies' => CompanyController::class,
    //     'languages' => LanguageController::class,
    // ]);
    Route::apiResources([
        'customers' => CustomerController::class,
        'product' => ProductController::class,
        'order' => OrderController::class,
    ]);
});
