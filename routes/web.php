<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\FeaturedProjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('featured-projects.index');
    });

    Route::resource('featured-projects', FeaturedProjectController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('users', UserController::class)->except(['show']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
