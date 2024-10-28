<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImageGenerationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'prompt' => $this->faker->sentence(),
            'image_url' => 'images/' . $this->faker->uuid() . '.png',
            'thumbnail_url' => 'thumbnails/' . $this->faker->uuid() . '.png',
            'status' => 'completed',
            'settings' => json_encode([
                'model' => 'stable-diffusion-v1-5',
                'width' => $this->faker->randomElement([512, 768, 1024]),
                'height' => $this->faker->randomElement([512, 768, 1024]),
                'steps' => $this->faker->numberBetween(20, 50),
                'guidance_scale' => $this->faker->randomFloat(1, 5.0, 15.0),
                'seed' => $this->faker->numberBetween(1, 999999999),
            ]),
            'credits_used' => $this->faker->numberBetween(1, 5),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }

    /**
     * Indicate that the generation is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'image_url' => null,
            'thumbnail_url' => null,
        ]);
    }

    /**
     * Indicate that the generation failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'image_url' => null,
            'thumbnail_url' => null,
        ]);
    }
}
