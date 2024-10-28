<?php

namespace App\Providers;

use App\Services\ActivityService;
use App\Services\ImageGenerationService;
use App\Services\NotificationService;
use App\Services\StorageService;
use App\View\Components\Button;
use App\View\Components\Input;
use App\View\Components\Label;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register services
        $this->app->singleton(StorageService::class);
        $this->app->singleton(ActivityService::class);
        $this->app->singleton(NotificationService::class);
        
        $this->app->singleton(ImageGenerationService::class, function ($app) {
            return new ImageGenerationService(
                $app->make(StorageService::class),
                $app->make(ActivityService::class),
                $app->make(NotificationService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Blade components
        Blade::component('input', Input::class);
        Blade::component('label', Label::class);
        Blade::component('button', Button::class);
    }
}
