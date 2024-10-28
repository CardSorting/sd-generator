<?php

namespace App\Policies;

use App\Models\ImageGeneration;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImageGenerationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the image generation.
     * Any authenticated user can view images.
     */
    public function view(User $user, ImageGeneration $imageGeneration): bool
    {
        return true; // Allow all authenticated users to view
    }

    /**
     * Determine whether the user can download the image generation.
     * Only the owner can download.
     */
    public function download(User $user, ImageGeneration $imageGeneration): bool
    {
        return $user->id === $imageGeneration->user_id;
    }

    /**
     * Determine whether the user can rerun the image generation.
     * Only the owner can rerun.
     */
    public function rerun(User $user, ImageGeneration $imageGeneration): bool
    {
        return $user->id === $imageGeneration->user_id;
    }
}
