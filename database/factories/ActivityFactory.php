<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\ImageGeneration;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        $user = User::factory()->create();
        $type = $this->faker->randomElement(['image_generation', 'transaction', 'notification']);
        
        $subject = match ($type) {
            'image_generation' => ImageGeneration::factory()->create(['user_id' => $user->id]),
            'transaction' => Transaction::factory()->create(['user_id' => $user->id]),
            default => null,
        };

        return [
            'user_id' => $user->id,
            'type' => $type,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'description' => $this->faker->sentence(),
            'data' => [],
            'created_at' => $this->faker->dateTimeBetween('-1 month'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at']);
            },
        ];
    }

    public function forImageGeneration(?ImageGeneration $generation = null): static
    {
        return $this->state(function (array $attributes) use ($generation) {
            if (!$generation) {
                $user = User::factory()->create();
                $generation = ImageGeneration::factory()->create(['user_id' => $user->id]);
            }
            
            return [
                'user_id' => $generation->user_id,
                'type' => 'image_generation',
                'subject_type' => ImageGeneration::class,
                'subject_id' => $generation->id,
                'data' => [
                    'prompt' => $generation->prompt,
                    'settings' => $generation->settings,
                    'status' => $generation->status,
                ],
            ];
        });
    }

    public function forTransaction(?Transaction $transaction = null): static
    {
        return $this->state(function (array $attributes) use ($transaction) {
            if (!$transaction) {
                $user = User::factory()->create();
                $transaction = Transaction::factory()->create(['user_id' => $user->id]);
            }
            
            return [
                'user_id' => $transaction->user_id,
                'type' => 'transaction',
                'subject_type' => Transaction::class,
                'subject_id' => $transaction->id,
                'data' => [
                    'amount' => $transaction->amount,
                    'type' => $transaction->type,
                    'status' => $transaction->status,
                ],
            ];
        });
    }

    public function forNotification(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'notification',
                'subject_type' => null,
                'subject_id' => null,
                'data' => [
                    'title' => $this->faker->sentence(),
                    'message' => $this->faker->paragraph(),
                ],
            ];
        });
    }
}
