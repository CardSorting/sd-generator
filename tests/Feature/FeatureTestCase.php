<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;

abstract class FeatureTestCase extends TestCase
{
    use RefreshDatabase;

    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    protected function createAuthenticatedUser(array $attributes = []): User
    {
        $user = $this->createUser($attributes);
        $this->actingAs($user);
        return $user;
    }

    protected function assertResponseHasValidationError(TestResponse $response, string $field): void
    {
        $response->assertSessionHasErrors($field);
    }

    protected function assertResponseHasNoValidationError(TestResponse $response, string $field): void
    {
        $response->assertSessionDoesntHaveErrors($field);
    }

    protected function assertUserIsRedirectedToLogin(TestResponse $response): void
    {
        $response->assertRedirect(route('login'));
    }

    protected function assertUserIsRedirectedToDashboard(TestResponse $response): void
    {
        $response->assertRedirect(route('dashboard'));
    }
}
