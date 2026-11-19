<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GameController::class, 'play'])->name('home');
Route::get('/leaderboard', [GameController::class, 'leaderboard'])->name('leaderboard');
Route::view('/pricing', 'pricing')->name('pricing');
Route::view('/privacy', 'legal.privacy')->name('privacy');
Route::view('/terms', 'legal.terms')->name('terms');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');

    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->whereIn('provider', SocialAuthController::PROVIDERS)->name('oauth.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->whereIn('provider', SocialAuthController::PROVIDERS)->middleware('throttle:20,1')->name('oauth.callback');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/games', [GameController::class, 'store'])->middleware('throttle:30,1')->name('games.store');
    Route::get('/scores', [GameController::class, 'history'])->name('scores');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
});
