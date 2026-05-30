<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderSummaryController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    Route::apiResources([
        'companies' => CompanyController::class,
        'languages' => LanguageController::class,
        'products' => ProductController::class,
        'customers' => CustomerController::class,
        'orders' => OrderController::class,
    ]);

    Route::post('/products/{uuid}/restock', [ProductController::class, 'restock']);
    Route::get('/orders/{customeruuid}/customers', [OrderController::class, 'listByCustomer']);
    Route::get('/order/summary', [OrderSummaryController::class, 'index']);
});