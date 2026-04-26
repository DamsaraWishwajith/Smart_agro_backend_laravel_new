<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PlantController;
use App\Http\Controllers\Api\FarmConditionController;
use App\Http\Controllers\Api\MotorController;
use App\Http\Controllers\Api\ModeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Add a named 'login' route to prevent redirection errors
Route::get('/login', function () {
    return response()->json(['message' => 'Unauthenticated.'], 401);
})->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
});

// Temporarily public for easy testing
Route::post('/plants', [PlantController::class, 'store']);
Route::post('/farm-conditions', [FarmConditionController::class, 'store']);
Route::post('/get-farm-data', [FarmConditionController::class, 'getUserFarmData']);
Route::post('/save-irrigation-schedules', [PlantController::class, 'saveSchedules']);
Route::post('/get-irrigation-schedules', [PlantController::class, 'getSchedules']);
Route::post('/update-motors', [MotorController::class, 'updateStatus']);
Route::post('/get-motors', [MotorController::class, 'getStatus']);
Route::post('/update-mode', [ModeController::class, 'updateMode']);
Route::post('/get-mode', [ModeController::class, 'getMode']);



