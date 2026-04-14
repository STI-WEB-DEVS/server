<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('customers', CustomerController::class);

Route::apiResources([

    'customers' => CustomerController::class,
  
    'companies' => CompanyController::class,
  
    'policies' => PasswordPolicyController::class,
  
  ]);