<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/test', function (Request $request) {
    return response()->json([
        'name' => "Mariz Esparago",
        'section' => "BSCS 601",
        'fav_song' => "Heather" 
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    Route::apiResources([
        'companies' => CompanyController::class,
        'languages' => LanguageController::class,
        'customers' => CustomerController::class,
        'products' => ProductController::class,
        'orders' => OrderController::class,
    ]);
});