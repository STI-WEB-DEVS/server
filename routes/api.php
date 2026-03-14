<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::apiResources([
    'companies'   => CompanyController::class,
    'languages'   => LanguageController::class,
    'products'    => ProductsController::class,
    'customers'   => CustomersController::class,
    'orders'      => OrderController::class,
    'order-items' => OrderItemController::class,
]);

Route::get('/customers/{uuid}/orders', [OrderController::class, 'getByCustomer']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);
});