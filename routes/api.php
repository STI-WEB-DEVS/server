
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    Route::apiResources([
        'companies' => CompanyController::class,
        'languages' => LanguageController::class,
        'customer' => CustomerController::class,
        'products'  => ProductController::class,
        'orders'    => OrderController::class,
    ]);

    // If you want a custom route for listing orders per customer:
    Route::get('/customer/{uuid}/orders', [CustomerController::class, 'show']);
});
