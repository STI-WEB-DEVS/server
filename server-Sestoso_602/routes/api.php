<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerOrderController;
use App\Models\Customer;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);





Route::middleware('auth:sanctum')->group(function () {
    // Route::delete('/logout', [AuthController::class, 'logout']);

    // Route::apiResources([
    //     'companies' => CompanyController::class,
    //     'languages' => LanguageController::class,
    // ]);

    Route::apiResources([
        'customers' => CustomerController::class,
        'product' => ProductController::class,
        'order' => OrderController::class
    ]);

    Route::get('/customers/{id}/orders', [CustomerController::class, 'customerOrders']);
});
