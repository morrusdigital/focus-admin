<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\FeaturedProjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProjectRequestController;
use App\Http\Controllers\CompanyProfileDownloadController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\FeaturedWorkController;
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
    Route::post('featured-projects/reorder', [FeaturedProjectController::class, 'reorder'])->name('featured-projects.reorder');
    Route::resource('featured-works', FeaturedWorkController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('news', NewsController::class);
    Route::resource('project-requests', ProjectRequestController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::resource('company-profile-downloads', CompanyProfileDownloadController::class)->only(['index', 'show', 'destroy']);
    Route::get('company-profile', [CompanyProfileController::class, 'edit'])->name('company-profile.edit');
    Route::put('company-profile', [CompanyProfileController::class, 'update'])->name('company-profile.update');
    Route::resource('users', UserController::class)->except(['show']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
