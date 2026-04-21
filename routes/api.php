<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

<<<<<<< HEAD
Route::get('/test', function (Request $request) {
    return response()->json([
        'name' => 'Jaspher Lloyd Tadlan',
        'section' => 'BSCS 601',
        'fav_song' => 'Here Without You'
    ]);
});
=======
Route::apiResources([
    'companies' => CompanyController::class,
    'languages' => LanguageController::class,
    'products' => ProductController::class,
    'customers' => CustomerController::class,
    'orders' => OrderController::class,
]);

// Order list per customer
Route::get('/orders/{customeruuid}/customers', [OrderController::class, 'listByCustomer']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);
});
>>>>>>> Malubay,-Tristan-Tryke-
