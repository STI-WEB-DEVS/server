<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\OrderItemsController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    Route::apiResources([
        'companies' => CompanyController::class,
        'languages' => LanguageController::class,
        'products' => ProductsController::class,
        'customers' => CustomersController::class,
        'orders' => OrdersController::class,
        'order-items', OrderItemsController::class


    ]);
});



Route::prefix('companies')->group(function () {
    Route::get('/', [CompanyController::class, 'index']);       // List all companies
    Route::post('/', [CompanyController::class, 'store']);      // Create new company
    Route::get('/{id}', [CompanyController::class, 'show']);    // Get single company
    Route::put('/{id}', [CompanyController::class, 'update']);  // Update company
    Route::delete('/{id}', [CompanyController::class, 'destroy']); // Delete company
});


