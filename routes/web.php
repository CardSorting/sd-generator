<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\ImageGenerationController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('register', [AuthenticatedSessionController::class, 'createRegister'])->name('register');
    Route::post('register', [AuthenticatedSessionController::class, 'storeRegister']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Dashboard
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Image Generation
    Route::prefix('generate')->name('generate.')->group(function () {
        Route::get('/', [ImageGenerationController::class, 'index'])->name('index');
        Route::post('/', [ImageGenerationController::class, 'store'])->name('store');
        Route::get('/{imageGeneration}', [ImageGenerationController::class, 'show'])->name('show');
    });

    // Activities
    Route::prefix('activities')->name('activities.')->group(function () {
        Route::get('/', [ActivityController::class, 'index'])->name('index');
        Route::get('/recent', [ActivityController::class, 'recent'])->name('recent');
    });

    // Collections
    Route::prefix('collections')->name('collections.')->group(function () {
        Route::get('/', [CollectionController::class, 'index'])->name('index');
        Route::post('/', [CollectionController::class, 'store'])->name('store');
        Route::get('/{collection}', [CollectionController::class, 'show'])->name('show');
        Route::put('/{collection}', [CollectionController::class, 'update'])->name('update');
        Route::delete('/{collection}', [CollectionController::class, 'destroy'])->name('destroy');
        Route::post('/{collection}/images/{imageGeneration}', [CollectionController::class, 'addImage'])->name('add-image');
        Route::delete('/{collection}/images/{imageGeneration}', [CollectionController::class, 'removeImage'])->name('remove-image');
    });

    // Likes
    Route::prefix('likes')->name('likes.')->group(function () {
        Route::post('/{imageGeneration}', [LikeController::class, 'toggle'])->name('toggle');
        Route::get('/images', [LikeController::class, 'images'])->name('images');
        Route::get('/{imageGeneration}/users', [LikeController::class, 'users'])->name('users');
    });

    // Comments
    Route::prefix('comments')->name('comments.')->group(function () {
        Route::post('/{imageGeneration}', [CommentController::class, 'store'])->name('store');
        Route::put('/{comment}', [CommentController::class, 'update'])->name('update');
        Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('destroy');
    });

    // Follows
    Route::prefix('follows')->name('follows.')->group(function () {
        Route::post('/{user}', [FollowController::class, 'toggle'])->name('toggle');
        Route::get('/suggestions', [FollowController::class, 'suggestions'])->name('suggestions');
        Route::get('/{user}/followers', [FollowController::class, 'followers'])->name('followers');
        Route::get('/{user}/following', [FollowController::class, 'following'])->name('following');
    });
});
