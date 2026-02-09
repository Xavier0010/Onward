<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/signup', function () {
    return view('signup.index');
});

Route::get('/login', function () {
    return view('login.index');
});