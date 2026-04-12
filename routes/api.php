<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    Route::apiResources([
        'companies' => CompanyController::class,
        'languages' => LanguageController::class,
    ]);
});

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
