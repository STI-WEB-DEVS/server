<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LanguageController;

// Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::delete('/logout', [AuthController::class, 'logout']);

// Resource Routes
Route::apiResources([
    'companies' => CompanyController::class,
    'languages' => LanguageController::class,
    'customers' => CustomerController::class,
]);