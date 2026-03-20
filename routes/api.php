<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Api\Admin\DBMSController;
use App\Http\Controllers\Api\Admin\StatisticsController;

use App\Http\Controllers\Api\User\UserDashboardController;
use App\Http\Controllers\Api\User\UserProfileController;
use App\Http\Controllers\Api\User\UserTodoController;
use App\Http\Controllers\Api\User\AchievementController;

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Route::middleware('auth:sanctum', 'admin')->prefix('admin')->group(function() {
//      Route::get('/dbms/{table}', [DBMSController::class, 'index']);
//      Route::get('/dbms/{table}/{id}', [DBMSController::class, 'show']);
//      Route::post('/dbms/{table}', [DBMSController::class, 'store']);
//      Route::put('/dbms/{table}/{id}', [DBMSController::class, 'update']);
//      Route::delete('/dbms/{table}/{id}', [DBMSController::class, 'destroy']);
// });

Route::prefix('admin')->group(function() {
    Route::get('/statistics', [StatisticsController::class, 'statistics']);

    Route::prefix('/dbms')->group(function() {
        Route::get('/{table}', [DBMSController::class, 'index']);
        Route::get('/{table}/{id}', [DBMSController::class, 'show']);
        Route::post('/{table}', [DBMSController::class, 'store']);
        Route::put('/{table}/{id}', [DBMSController::class, 'update']);
        Route::delete('/{table}/{id}', [DBMSController::class, 'destroy']);
    });
});


Route::middleware('auth:sanctum')->prefix('user')->group(function() {
    Route::get('/dashboard', [UserDashboardController::class, 'index']);

    Route::prefix('todo')->group(function() {
        Route::get('/', [UserTodoController::class, 'index']);
        Route::post('/', [UserTodoController::class, 'store']);
        Route::put('/{id}', [UserTodoController::class, 'update']);
        Route::delete('/{id}', [UserTodoController::class, 'destroy']);
    });
    
    Route::prefix('profile')->group(function() {
        Route::get('/', [UserProfileController::class, 'index']);
        Route::put('/', [UserProfileController::class, 'update']);
        Route::delete('/', [UserProfileController::class, 'destroy']);
    });

    Route::prefix('achievement')->group(function() {
        Route::get('/', [AchievementController::class, 'index']);
    });
});