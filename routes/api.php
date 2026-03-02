<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\DBMSController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Route::middleware('auth:sanctum', 'admin')->group(function() {
//     Route::prefix('admin')->group(function() {
//         Route::get('/dbms/{table}', [DBMSController::class, 'index']);
//         Route::get('/dbms/{table}/{id}', [DBMSController::class, 'show']);
//         Route::post('/dbms/{table}', [DBMSController::class, 'store']);
//         Route::put('/dbms/{table}/{id}', [DBMSController::class, 'update']);
//         Route::delete('/dbms/{table}/{id}', [DBMSController::class, 'destroy']);
//     });
// });

Route::prefix('admin')->group(function() {
    Route::get('/dbms/{table}', [DBMSController::class, 'index']);
    Route::get('/dbms/{table}/{id}', [DBMSController::class, 'show']);
    Route::post('/dbms/{table}', [DBMSController::class, 'store']);
    Route::put('/dbms/{table}/{id}', [DBMSController::class, 'update']);
    Route::delete('/dbms/{table}/{id}', [DBMSController::class, 'destroy']);
});