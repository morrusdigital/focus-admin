<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeaturedProjectController;

Route::get('/', function () {
    return redirect()->route('featured-projects.index');
});

Route::resource('featured-projects', FeaturedProjectController::class);
