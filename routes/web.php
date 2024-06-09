<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::redirect('/', '/posts');

Route::resource('posts', PostController::class);

Route::get('/{user}/posts', [DashboardController::class, 'userPosts'])->name('posts.user');

// Only authenticated users can access these routes
Route::middleware(('auth'))->group(function() {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('verified')->name('dashboard');

    Route::post('/logout', [AuthController::class,'logout'])->name('logout');

    // Email verification notice
    Route::get('/email/verify', [AuthController::class, 'verifyNotice'])->name('verification.notice');

    // Email verification handler
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware('signed')->name('verification.verify');

    // Resend email verification link
    Route::post('/email/verification-notification', [AuthController::class, 'verifyHandler'])->middleware('throttle:6,1')->name('verification.send');
});

// Only guests can access these routes
Route::middleware(('guest'))->group(function() {
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
