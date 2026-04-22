<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::apiResources([
    'companies' => CompanyController::class,
    'languages' => LanguageController::class,
    'products' => ProductController::class,
    'customers' => CustomerController::class,
    'orders' => OrderController::class,
]);

Route::get('/orders/{customeruuid}/customers', [OrderController::class, 'listByCustomer']);