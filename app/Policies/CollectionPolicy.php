<?php

namespace App\Policies;

use App\Models\Collection;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CollectionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the collection.
     */
    public function view(User $user, Collection $collection): bool
    {
        // Users can view public collections or their own collections
        return $collection->is_public || $user->id === $collection->user_id;
    }

    /**
     * Determine whether the user can update the collection.
     */
    public function update(User $user, Collection $collection): bool
    {
        return $user->id === $collection->user_id;
    }

    /**
     * Determine whether the user can delete the collection.
     */
    public function delete(User $user, Collection $collection): bool
    {
        return $user->id === $collection->user_id;
    }

    /**
     * Determine whether the user can add images to the collection.
     */
    public function addImage(User $user, Collection $collection): bool
    {
        return $user->id === $collection->user_id;
    }

    /**
     * Determine whether the user can remove images from the collection.
     */
    public function removeImage(User $user, Collection $collection): bool
    {
        return $user->id === $collection->user_id;
    }
}
