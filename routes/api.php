<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);



Route::middleware('auth:sanctum')->group(function () {
   

     Route::apiResources([
        'orders' => OrderController::class,
        'products' => ProductController::class,
        'customers' => CustomerController::class,
         'companies' => CompanyController::class,
        'languages' => LanguageController::class,
    ]);


});
