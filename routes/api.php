<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CustomersController; 
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\OrdersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. PUBLIC ROUTES (Accessible immediately in Hoppscotch)
Route::post('/login', [AuthController::class, 'login']);

// These are moved outside the middleware so you can test POST/GET freely
Route::apiResource('customers', CustomersController::class);
Route::apiResource('products', ProductsController::class);


// 2. PROTECTED ROUTES (Requires a valid Sanctum Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    Route::apiResources([
        'companies' => CompanyController::class,
        'languages' => LanguageController::class,
        'orders'    => OrdersController::class,
        // Add other protected resources here
    ]);

    // Custom route for retrieving a customer's orders
    Route::get('/customers/{id}/orders', [CustomersController::class, 'orders']);
});