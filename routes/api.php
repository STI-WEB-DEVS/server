<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderSummaryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::apiResources([
    'companies' => CompanyController::class,
    'languages' => LanguageController::class,
    'products'  => ProductController::class,
    'customers' => CustomerController::class,
    'orders'    => OrderController::class,
]);


Route::get('/customers/orders/{customerUuid}', [OrderController::class, 'listByCustomer']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    Route::middleware('role:admin')->group(function () {
        Route::get('/order/summary', [OrderSummaryController::class, 'index']);
    });

    Route::apiResources([
        'companies' => CompanyController::class,
        'languages' => LanguageController::class,
    ]);
});
