<?php

namespace Tests\Feature\Auth;

use Tests\Feature\FeatureTestCase;

class AuthenticationTest extends FeatureTestCase
{
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = $this->createUser();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertUserIsRedirectedToDashboard($response);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = $this->createUser();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $this->assertResponseHasValidationError($response, 'email');
    }

    public function test_users_can_logout(): void
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
