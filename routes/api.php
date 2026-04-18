<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Route::delete('/logout', [AuthController::class, 'logout']);

    Route::apiResources([
        'customer' => CustomerController::class
    ]);
});
<<<<<<< HEAD

Route::apiResource('customers', CustomerController::class);
Route::apiResource('products', \App\Http\Controllers\ProductController::class);

Route::get('/student-profile', function () {
    return response()->json([
        "student_id" => "02000354357",
        "name" => "Joshua Wayman A. Arabejo",
        "location" => [
            "barangay" => "3-A",
            "city" => "Davao City"
        ],
        "favorites" => [
            "movie" => "Peninsula",
            "song" => "Martyr Nyebera",
            "artist" => "KAMIKAZE"
        ],
        "is_active" => true
    ], 200, [], JSON_PRETTY_PRINT);
});
=======
>>>>>>> 719b480669c1af01f0bbc69fd037eb8590741e5f
