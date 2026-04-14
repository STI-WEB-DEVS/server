<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);





Route::middleware('auth:sanctum')->group(function () {
    // Route::delete('/logout', [AuthController::class, 'logout']);

    // Route::apiResources([
    //     'companies' => CompanyController::class,
    //     'languages' => LanguageController::class,
    // ]);

    Route::apiResource('customers', CustomerController::class);
});
