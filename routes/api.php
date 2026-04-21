<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Route::delete('/logout', [AuthController::class, 'logout']);

    // Route::apiResources([
    //     'companies' => CompanyController::class,
    //     'languages' => LanguageController::class,
    // ]);

    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('products', ProductController::class);
    Route::post('orders/{uuid}/restore', [OrderController::class, 'restore']);
    Route::get('customers/{customer_uuid}/orders', [OrderController::class, 'getByCustomer']);
});
