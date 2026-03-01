<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::get('/signup', function () {
    return view('signup.index');
});

Route::get('/login', function () {
    return view('login.index');
});