<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\ImageGeneration;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'content' => $this->faker->paragraph(),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }

    /**
     * Configure the comment for an image generation.
     */
    public function forImageGeneration(ImageGeneration $imageGeneration = null): Factory
    {
        return $this->state(function (array $attributes) use ($imageGeneration) {
            return [
                'commentable_type' => ImageGeneration::class,
                'commentable_id' => $imageGeneration ? $imageGeneration->id : ImageGeneration::factory(),
            ];
        });
    }
}
