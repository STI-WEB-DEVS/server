<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::get('/customers/{uuid}/orders', [OrderController::class, 'customerOrders']);


Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    Route::apiResources([
        'customers' => CustomerController::class,
        'products' => ProductController::class,
        'orders' => OrderController::class,
    ]);
});
