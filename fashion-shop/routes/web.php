<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function(){
    return view('pages.dashboard');
})->name('dashboard');

Route::middleware('guest')->group(function(){ 
    //Login
    Route::get('/login', function(){
        return view('pages.auth.login');
    })->name('login');
    Route::post('/login', [AuthController::class, 'loginHandler'])->name('login_handler');

    //Register
    Route::get('/register', function(){
        return view('pages.auth.register');
    })->name('register');
    Route::post('/register', [AuthController::class, 'registerHandler'])->name('register_handler');

    //Login with Google
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google_login');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google_callback');

    //Forgot Password
    Route::get('/forgot-password', function(){
        return view('pages.auth.forgot-password');
    })->name('forgot-password');
});

Route::middleware('auth')->group(function(){
    //Logout
    Route::post('/logout', [AuthController::class, 'logoutHandler'])->name('logout');
});