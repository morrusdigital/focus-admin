<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\FeaturedProjectController;
use Illuminate\Support\Facades\Route;

Route::apiResource('featured-projects', FeaturedProjectController::class);
Route::apiResource('clients', ClientController::class)
    ->only(['index', 'store', 'update', 'destroy']);
