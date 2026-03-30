<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
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
    })->name('forgot_password');
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetEmail'])->name('send_reset_password_email');

    Route::get('/password/reset/{token}', [AuthController::class, 'resetPasswordForm'])->name('password_reset');
    Route::post('/password/reset', [AuthController::class, 'resetPasswordHandler'])->name('reset_password');
});

Route::middleware('auth')->group(function(){
    //Logout
    Route::post('/logout', [AuthController::class, 'logoutHandler'])->name('logout');
});

//Admin routes
Route::prefix('admin')->name('admin.')->group(function(){
    Route::middleware('auth')->group(function(){
        Route::get('/', [AdminController::class, 'DashboardView'])->name('admin_dashboard');

        Route::get('/profile', [AdminController::class, 'ProfileView'])->name('admin-profile');
        Route::get('/account-manager', [AdminController::class, 'AccountManagerView'])->name('admin-account-manager');

        //Product Manager
        Route::get('/product-manager', [AdminController::class, 'ProductManagerView'])->name('product-manager');
        Route::get('/product-categories', [CategoryController::class, 'index'])->name('product-categories');
        Route::get('/product-manager/add', [ProductController::class, 'addProductForm'])->name('add-product');
        Route::post('/product-manager/add', [ProductController::class, 'addProductHandler'])->name('add-product-handler');
        Route::get('/product-manager/edit/{product:slug}', [ProductController::class, 'editProductForm'])->name('edit-product');
        Route::put('/product-manager/edit/{product:slug}', [ProductController::class, 'updateProductHandler'])->name('update-product-handler');
        });
        //Category Manager
        Route::post('/product-manager/category', [CategoryController::class, 'store'])->name('add-product-category-handler');
        Route::delete('/product-categories/{id}', [CategoryController::class, 'destroy'])->name('product-categories.destroy');
        //Collection Manager

        //Voucher Manager

        //Flash Sale Manager

        //Order Manager

        //Customer Manager

        //Report Manager

        //ServiceCenter Manager

        //Employee Manager
});