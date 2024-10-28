<?php

namespace App\Providers;

use App\Models\ImageGeneration;
use App\Models\SDModel;
use App\Models\Tag;
use App\Services\ImageGenerationService;
use App\Support\ActivityLogger;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\View\Factory;
use Illuminate\Database\QueryException;

class ImageGenerationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register any additional bindings
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register view composers after the application has booted
        $this->app->booted(function () {
            /** @var Factory $view */
            $view = $this->app['view'];

            $view->composer(['dashboard', 'image-generations.*'], function ($view) {
                try {
                    $view->with([
                        'modelCategories' => SDModel::query()->distinct('category')->pluck('category') ?? collect(),
                        'styleTypes' => SDModel::query()->distinct('style_type')->pluck('style_type') ?? collect(),
                        'tags' => Tag::query()->get()->groupBy('type') ?? collect(),
                    ]);
                } catch (QueryException $e) {
                    // If tables don't exist yet (e.g. during testing), provide empty collections
                    $view->with([
                        'modelCategories' => collect(),
                        'styleTypes' => collect(),
                        'tags' => collect(),
                    ]);
                }
            });
        });

        // Register model observers
        ImageGeneration::created(function (ImageGeneration $imageGeneration) {
            ActivityLogger::logImageGeneration(
                'image_generation',
                'Started image generation',
                $imageGeneration
            );
        });

        ImageGeneration::updated(function (ImageGeneration $imageGeneration) {
            if ($imageGeneration->isDirty('status')) {
                ActivityLogger::logImageGeneration(
                    'image_generation_status',
                    'Image generation status updated to: ' . $imageGeneration->status,
                    $imageGeneration,
                    ['old_status' => $imageGeneration->getOriginal('status')]
                );
            }
        });
    }
}
