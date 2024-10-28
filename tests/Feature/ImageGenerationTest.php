<?php

namespace Tests\Feature;

use App\Models\ImageGeneration;

class ImageGenerationTest extends FeatureTestCase
{
    public function test_image_generation_screen_requires_authentication(): void
    {
        $response = $this->get('/generate');
        $this->assertUserIsRedirectedToLogin($response);
    }

    public function test_authenticated_users_can_access_image_generation(): void
    {
        $user = $this->createAuthenticatedUser();
        $response = $this->get('/generate');
        $response->assertStatus(200);
    }

    public function test_users_can_generate_images(): void
    {
        $user = $this->createAuthenticatedUser([
            'credits_balance' => 10,
        ]);

        $response = $this->post('/generate', [
            'prompt' => 'A beautiful sunset',
            'negative_prompt' => 'ugly, blurry',
            'steps' => 20,
            'width' => 512,
            'height' => 512,
            'model' => 'v1-5-pruned-emaonly.safetensors',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('image_generations', [
            'user_id' => $user->id,
            'prompt' => 'A beautiful sunset',
            'status' => 'processing',
        ]);
    }

    public function test_users_cannot_generate_without_credits(): void
    {
        $user = $this->createAuthenticatedUser([
            'credits_balance' => 0,
        ]);

        $response = $this->post('/generate', [
            'prompt' => 'A beautiful sunset',
            'steps' => 20,
            'width' => 512,
            'height' => 512,
            'model' => 'v1-5-pruned-emaonly.safetensors',
        ]);

        $this->assertResponseHasValidationError($response, 'credits');
        $this->assertDatabaseCount('image_generations', 0);
    }

    public function test_users_can_view_their_generations(): void
    {
        $user = $this->createAuthenticatedUser();
        ImageGeneration::factory()->count(5)->create(['user_id' => $user->id]);

        $response = $this->get('/generate');

        $response->assertStatus(200);
        $response->assertViewHas('generations');
        $this->assertEquals(5, $response->viewData('generations')->count());
    }

    public function test_users_cannot_view_others_generations(): void
    {
        $user1 = $this->createUser();
        ImageGeneration::factory()->count(5)->create(['user_id' => $user1->id]);

        $user2 = $this->createAuthenticatedUser();

        $response = $this->get('/generate');

        $response->assertStatus(200);
        $response->assertViewHas('generations');
        $this->assertEquals(0, $response->viewData('generations')->count());
    }

    public function test_users_can_rerun_their_generations(): void
    {
        $user = $this->createAuthenticatedUser([
            'credits_balance' => 10,
        ]);

        $generation = ImageGeneration::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
        ]);

        $response = $this->post("/generate/{$generation->id}/rerun");

        $response->assertRedirect();
        $this->assertDatabaseHas('image_generations', [
            'user_id' => $user->id,
            'prompt' => $generation->prompt,
            'status' => 'processing',
        ]);
    }

    public function test_users_cannot_rerun_others_generations(): void
    {
        $user1 = $this->createUser();
        $generation = ImageGeneration::factory()->create([
            'user_id' => $user1->id,
            'status' => 'completed',
        ]);

        $user2 = $this->createAuthenticatedUser();

        $response = $this->post("/generate/{$generation->id}/rerun");

        $response->assertStatus(403);
    }
}
