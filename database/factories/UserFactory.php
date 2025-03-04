<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'credits_balance' => $this->faker->numberBetween(0, 1000),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function noCredits(): static
    {
        return $this->state(fn (array $attributes) => [
            'credits_balance' => 0,
        ]);
    }

    public function credits(int $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'credits_balance' => $amount,
        ]);
    }
}
