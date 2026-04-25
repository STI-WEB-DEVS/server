<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProductController; 
use App\Http\Controllers\OrderController; 
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

// Protected routes — requires Bearer token from /login
Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    // Standard Resource routes for automatic CRUD operations
    Route::apiResources([
        'companies' => CompanyController::class,
        'languages' => LanguageController::class,
        'customers' => CustomerController::class,
        'products'  => ProductController::class,
        'orders'    => OrderController::class,   
    ]);

    // NEW: Custom route for Output #3 (Order list per customer)
    Route::get('/customers/{customer_uuid}/orders', [OrderController::class, 'customerOrders']);
});