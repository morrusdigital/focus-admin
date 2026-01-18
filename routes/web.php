<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard', [
        'title' => 'Dashboard',
        'subtitle' => 'Dashboard',
    ]);
})->name('dashboard');
