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
// Use these for your demonstration video to avoid 401 Unauthorized errors.
Route::post('/login', [AuthController::class, 'login']);

// API Resources for your main assignment features
Route::apiResource('customers', CustomersController::class);
Route::apiResource('products', ProductsController::class);
Route::apiResource('orders', OrdersController::class);

// Custom route for retrieving a customer's specific orders
Route::get('/customers/{id}/orders', [CustomersController::class, 'orders']);


// 2. PROTECTED ROUTES (Requires a valid Sanctum Token)
// Keep internal system routes here.
Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    Route::apiResources([
        'companies' => CompanyController::class,
        'languages' => LanguageController::class,
    ]);
});