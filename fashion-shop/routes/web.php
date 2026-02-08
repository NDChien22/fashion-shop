<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function(){ 
    Route::get('/login', function(){
        return view('pages.auth.login');
    })->name('login');
    Route::get('/register', function(){
        return view('pages.auth.register');
    })->name('register');
    Route::get('/forgot-password', function(){
        return view('pages.auth.forgot-password');
    })->name('forgot-password');
});