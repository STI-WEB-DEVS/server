<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);
    Route::apiResource('companies', CompanyController::class);
});



Route::prefix('companies')->group(function () {
    Route::get('/', [CompanyController::class, 'index']);       // List all companies
    Route::post('/', [CompanyController::class, 'store']);      // Create new company
    Route::get('/{id}', [CompanyController::class, 'show']);    // Get single company
    Route::put('/{id}', [CompanyController::class, 'update']);  // Update company
    Route::delete('/{id}', [CompanyController::class, 'destroy']); // Delete company
});


