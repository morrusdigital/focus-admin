<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\FeaturedProjectController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\NewsController;
use Illuminate\Support\Facades\Route;

Route::apiResource('featured-projects', FeaturedProjectController::class)->only(['index']);
Route::apiResource('clients', ClientController::class)->only(['index']);
Route::apiResource('projects', ProjectController::class)->only(['index', 'show']);
Route::apiResource('news', NewsController::class)->only(['index']);
Route::get('news/{slug}', [NewsController::class, 'show'])->name('news.show');

Route::middleware('auth.basic')->group(function () {
    Route::apiResource('featured-projects', FeaturedProjectController::class)->except(['index']);
    Route::apiResource('clients', ClientController::class)->except(['index', 'show']);
    Route::apiResource('projects', ProjectController::class)->except(['index', 'show']);
    Route::apiResource('news', NewsController::class)->except(['index', 'show']);
});
