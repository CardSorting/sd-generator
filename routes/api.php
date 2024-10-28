<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ImageGenerationController;
use App\Http\Controllers\ModelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Activity Routes
    Route::get('/activities', [ActivityController::class, 'index']);
    Route::get('/activities/recent', [ActivityController::class, 'recent']);

    // Image Generation Routes
    Route::post('/generate', [ImageGenerationController::class, 'store']);
    Route::get('/generations', [ImageGenerationController::class, 'index']);
    Route::post('/generations/{imageGeneration}/download', [ImageGenerationController::class, 'download']);
    Route::post('/generations/{imageGeneration}/rerun', [ImageGenerationController::class, 'rerun']);

    // Model Routes
    Route::get('/models', [ModelController::class, 'index']);
    Route::post('/models/sync', [ModelController::class, 'sync']);
});
