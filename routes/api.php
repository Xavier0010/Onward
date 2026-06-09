<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Api\Admin\DBMSController;
use App\Http\Controllers\Api\Admin\StatisticsController;

use App\Http\Controllers\Api\User\UserDashboardController;
use App\Http\Controllers\Api\User\UserProfileController;
use App\Http\Controllers\Api\User\UserTodoController;
use App\Http\Controllers\Api\User\AchievementController;
use App\Http\Controllers\Api\User\FriendController;
use App\Http\Controllers\Api\User\NotificationController;
use App\Http\Controllers\Api\User\LeaderboardController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('admin')->group(function() {
    Route::get('/statistics', [StatisticsController::class, 'statistics']);
    Route::get('/dbms/tables', [DBMSController::class, 'tables']);
    Route::get('/dbms/table-info/{table}', [DBMSController::class, 'tableInfo']);

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
        Route::put('/{id}/toggle', [UserTodoController::class, 'toggle']);
        Route::delete('/{id}', [UserTodoController::class, 'destroy']);
    });
    
    Route::prefix('profile')->group(function() {
        Route::get('/', [UserProfileController::class, 'index']);
        Route::put('/', [UserProfileController::class, 'update']);
        Route::delete('/', [UserProfileController::class, 'destroy']);
        Route::get('/{id}', [UserProfileController::class, 'show']);
        Route::put('/achievements', [UserProfileController::class, 'updateShowcaseAchievements']);
    });

    // Route::post('/avatar', [UserProfileController::class, 'uploadAvatar']);
    Route::get('/activity', [UserProfileController::class, 'getActivity']);

    Route::prefix('achievement')->group(function() {
        Route::get('/', [AchievementController::class, 'index']);
    });

    Route::prefix('friends')->group(function() {
        Route::get('/', [FriendController::class, 'index']);
        Route::get('/search', [FriendController::class, 'search']);
        Route::get('/pending', [FriendController::class, 'pendingRequests']);
        Route::post('/request', [FriendController::class, 'sendRequest']);
        Route::post('/{id}/accept', [FriendController::class, 'acceptRequest']);
        Route::post('/{id}/reject', [FriendController::class, 'rejectRequest']);
        Route::delete('/{id}', [FriendController::class, 'removeFriend']);
        Route::delete('/request/{id}', [FriendController::class, 'cancelRequest']);
        Route::get('/activity', [FriendController::class, 'activity']);
        Route::get('/leaderboard', [FriendController::class, 'leaderboard']);
        Route::get('/streaks', [FriendController::class, 'streaks']);
    });

    Route::prefix('notifications')->group(function() {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{id}/read', [NotificationController::class, 'markRead']);
    });

    Route::get('/leaderboard', [LeaderboardController::class, 'index']);
});