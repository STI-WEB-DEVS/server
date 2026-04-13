<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

// ── Products ──────────────────────────────────────────
Route::apiResource('products', ProductController::class);
Route::post('products/{uuid}/restore', [ProductController::class, 'restore']);

// ── Customers ─────────────────────────────────────────
Route::apiResource('customers', CustomerController::class);
Route::post('customers/{uuid}/restore', [CustomerController::class, 'restore']);

// Output #3 — Order list per customer
Route::get('customers/{uuid}/orders', [CustomerController::class, 'orders'])
     ->name('customers.orders');

// ── Orders ────────────────────────────────────────────
Route::apiResource('orders', OrderController::class);
Route::post('orders/{uuid}/restore', [OrderController::class, 'restore']);

Route::middleware('auth:sanctum')->get('/customers', function () {
    return DB::table('customers')->get();
});