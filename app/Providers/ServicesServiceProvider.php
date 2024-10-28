<?php

namespace App\Providers;

use App\Services\ActivityService;
use App\Services\ImageGenerationService;
use App\Services\NotificationService;
use App\Services\StorageService;
use App\Support\ActivityLogger;
use App\Support\NotificationManager;
use Illuminate\Support\ServiceProvider;

class ServicesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register core services
        $this->app->singleton(StorageService::class);
        $this->app->singleton(ActivityService::class);
        $this->app->singleton(NotificationService::class);

        // Register the image generation service with its dependencies
        $this->app->singleton(ImageGenerationService::class, function ($app) {
            return new ImageGenerationService(
                $app->make(StorageService::class)
            );
        });

        // Register support classes
        $this->app->bind(ActivityLogger::class);
        $this->app->bind(NotificationManager::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
