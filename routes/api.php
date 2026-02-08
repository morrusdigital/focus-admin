<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\FeaturedProjectController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\ProjectRequestController;
use App\Http\Controllers\Api\CompanyProfileDownloadController;
use Illuminate\Support\Facades\Route;

Route::apiResource('featured-projects', FeaturedProjectController::class)->only(['index']);
Route::apiResource('clients', ClientController::class)->only(['index']);
Route::apiResource('projects', ProjectController::class)->only(['index', 'show']);
Route::apiResource('news', NewsController::class)->only(['index']);
Route::get('news/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::post('project-requests', [ProjectRequestController::class, 'store']);
Route::post('company-profile-downloads', [CompanyProfileDownloadController::class, 'store']);

Route::middleware('auth.basic')->group(function () {
    Route::apiResource('featured-projects', FeaturedProjectController::class)->except(['index']);
    Route::apiResource('clients', ClientController::class)->except(['index', 'show']);
    Route::apiResource('projects', ProjectController::class)->except(['index', 'show']);
    Route::apiResource('news', NewsController::class)->except(['index', 'show']);
    Route::get('project-requests', [ProjectRequestController::class, 'index']);
    Route::get('project-requests/{projectRequest}', [ProjectRequestController::class, 'show']);
    Route::put('project-requests/{projectRequest}', [ProjectRequestController::class, 'update']);
    Route::delete('project-requests/{projectRequest}', [ProjectRequestController::class, 'destroy']);
    Route::get('company-profile-downloads', [CompanyProfileDownloadController::class, 'index']);
    Route::get('company-profile-downloads/{companyProfileDownload}', [CompanyProfileDownloadController::class, 'show']);
    Route::delete('company-profile-downloads/{companyProfileDownload}', [CompanyProfileDownloadController::class, 'destroy']);
});
