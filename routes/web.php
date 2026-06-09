<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('index');
});

Route::get('/register', function () {
    return view('register.index');
})->name('register');

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
    Route::get('/dashboard', function() {
        return view('user.dashboard.index');
    })->name('user.dashboard');

    Route::get('/profile', function () {
        return view('user.profile.index');
    });

    Route::get('/profile/{id}', function ($id) {
        return view('user.profile.index', compact('id'));
    });

    Route::get('/achievements', function () {
        return view('user.achievements.index');
    });

    Route::get('/friends', function () {
        return view('user.friends.index');
    });

    Route::get('/leaderboard', function () {
        return view('user.leaderboard.index');
    });

    Route::get('/store', function () {
            return view('user.store.index');
        });
});

Route::middleware('auth')->prefix('admin')->group(function () {
    
    Route::get('/dashboard', function() {
        return view('admin.dashboard.index');
    });

    Route::get('/dbms', function () {
        return view('admin.dbms.index');
    });
});