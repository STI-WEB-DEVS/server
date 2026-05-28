<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\OrdersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn(Request $request) => $request->user()->load('customer'));

    // Products — accessible by both admin and customer
    Route::middleware('role:admin|customer')->group(function () {
        Route::get('/products',        [ProductsController::class, 'index']);
        Route::get('/products/{uuid}', [ProductsController::class, 'show']);
    });

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::apiResources([
            'companies' => CompanyController::class,
            'languages' => LanguageController::class,
            'customers' => CustomersController::class,
        ]);
        Route::get('/customers/{id}/orders', [CustomersController::class, 'orders']);

        // Products write operations — admin only
        Route::post('/products',           [ProductsController::class, 'store']);
        Route::put('/products/{uuid}',     [ProductsController::class, 'update']);
        Route::delete('/products/{uuid}',  [ProductsController::class, 'destroy']);

        // Orders — admin sees all
        Route::apiResource('orders', OrdersController::class);
    });

    // Customer-only routes
    Route::middleware('role:customer')->group(function () {
        Route::post('/orders',   [OrdersController::class, 'store']);
        Route::get('/my-orders', [OrdersController::class, 'myOrders']);
    });
});
