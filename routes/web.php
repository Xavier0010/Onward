<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('login.index');
});

Route::get('/register', function () {
    return view('register.index');
});

Route::get('/login', function () {
    return view('login.index');
})->name('login');

Route::get('/logout', function (Request $request) {

    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');

});

Route::middleware('auth')->prefix('user')->group(function () {
    Route::get('/profile', function () {
        return view('user.profile.index');
    });

    Route::get('/dashboard', function() {
        return view('user.dashboard.index');
    });

    Route::get('/achievements', function () {
        return view('user.achievements.index');
    });
});

Route::prefix('admin')->group(function () {
    Route::get('/statistics', function () {
        return view('admin.statistics');
    });
    
    Route::get('/{table}', function ($table) {
        return view('admin.table', compact('table'));
    });
});