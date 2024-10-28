<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Collection;
use App\Models\ImageGeneration;
use App\Policies\CommentPolicy;
use App\Policies\CollectionPolicy;
use App\Policies\ImageGenerationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ImageGeneration::class => ImageGenerationPolicy::class,
        Comment::class => CommentPolicy::class,
        Collection::class => CollectionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define a gate for managing notifications
        Gate::define('manage-notifications', function ($user) {
            return true; // All authenticated users can manage their notifications
        });
    }
}
