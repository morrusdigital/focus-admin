<?php

use App\Http\Controllers\Api\FeaturedProjectController;
use Illuminate\Support\Facades\Route;

Route::apiResource('featured-projects', FeaturedProjectController::class);
