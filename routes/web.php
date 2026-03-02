<?php

use Illuminate\Support\Facades\Route;

Route::get('/signup', function () {
    return view('signup.index');
});

Route::get('/login', function () {
    return view('login.index');
});