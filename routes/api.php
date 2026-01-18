<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\FeaturedProjectController;
use Illuminate\Support\Facades\Route;

Route::apiResource('featured-projects', FeaturedProjectController::class)->only(['index']);
Route::apiResource('clients', ClientController::class)->only(['index']);

Route::middleware('auth.basic')->group(function () {
    Route::apiResource('featured-projects', FeaturedProjectController::class)->except(['index']);
    Route::apiResource('clients', ClientController::class)->except(['index', 'show']);
});
