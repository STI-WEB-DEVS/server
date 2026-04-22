<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/orders', [OrderController::class, 'store']);

Route::get('/customers/{customer_uuid}/orders', [OrderController::class, 'index']);
Route::post('/products/{uuid}/restore', [ProductController::class, 'restore']);

Route::apiResources([
    'customers' => CustomerController::class,
    'products' => ProductController::class,
    'orders' => OrderController::class,
]);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);
});
