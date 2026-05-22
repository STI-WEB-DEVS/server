
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\OrdersController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    
    Route::get('/orders/summary', [OrdersController::class, 'getSummary']);

    Route::apiResources([
        'companies' => CompanyController::class,
        'languages' => LanguageController::class,
        'customers' => CustomersController::class,
        'products'  => ProductsController::class,
        'orders'    => OrdersController::class,
    ]);

    // If you want a custom route for listing orders per customer:
    Route::get('/customers/{id}/orders', [CustomersController::class, 'orders']);
});
