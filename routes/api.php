<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::post('/login', [AuthController::class, 'login']);

// ORDERS
Route::post('/orders', [OrderController::class, 'store']);
Route::get('orders/customers/{customerUuid}', [OrderController::class, 'getByCustomer']);
Route::get('/admin/orders', [OrderController::class, 'index']);
//CHECKOUT
Route::post('/checkout', [OrderController::class, 'store']);
// CUSTOMERS
Route::post('/customers/{customer_uuid}/orders', [OrderController::class, 'store']);
Route::get('/customers/{customer_uuid}/orders', [OrderController::class, 'getByCustomer']);
Route::post('/customers', [CustomerController::class, 'store']);
Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);
// PRODUCTS
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);

Route::get('/products', function () {
    return \App\Models\Product::all();
});

// REGISTER
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    Route::apiResources([
        'companies' => CompanyController::class,
        'languages' => LanguageController::class,
        'customers' => CustomerController::class,
        'Products' => ProductController::class,
    ]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user(); // Returns name, email, and ID metadata attributes from the database table
});
