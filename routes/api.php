<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// PUBLIC
Route::post('/login', [AuthController::class, 'login']);

// PROTECTED
Route::middleware('auth:sanctum')->group(function () {

    Route::apiResources([
        'companies' => CompanyController::class,
        'languages' => LanguageController::class,
        'products' => ProductController::class,
        'customers' => CustomerController::class,
        'orders' => OrderController::class,
    ]);

    Route::get('/orders/{customeruuid}/customers', [OrderController::class, 'listByCustomer']);

    Route::delete('/logout', [AuthController::class, 'logout']);
});