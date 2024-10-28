<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\ImageGeneration;
use Illuminate\Database\Eloquent\Factories\Factory;

class LikeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'image_generation_id' => ImageGeneration::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }

    /**
     * Configure the like for a specific user and image generation.
     */
    public function forUserAndImage(User $user, ImageGeneration $imageGeneration): Factory
    {
        return $this->state(function (array $attributes) use ($user, $imageGeneration) {
            return [
                'user_id' => $user->id,
                'image_generation_id' => $imageGeneration->id,
            ];
        });
    }
}
