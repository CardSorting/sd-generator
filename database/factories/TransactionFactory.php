<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['credit', 'debit']),
            'amount' => $this->faker->numberBetween(1, 100),
            'description' => $this->faker->sentence(),
            'status' => 'completed',
            'reference' => Str::uuid(),
            'created_at' => $this->faker->dateTimeBetween('-1 month'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at']);
            },
        ];
    }

    public function credit(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'credit',
        ]);
    }

    public function debit(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'debit',
        ]);
    }
}
