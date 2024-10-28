<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ImageGeneration;
use App\Notifications\NewLike;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private ImageGeneration $imageGeneration;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $imageOwner = User::factory()->create();
        $this->imageGeneration = ImageGeneration::factory()->create([
            'user_id' => $imageOwner->id
        ]);
    }

    public function test_user_can_like_image(): void
    {
        Notification::fake();

        $response = $this->actingAs($this->user)->postJson(
            route('likes.toggle', $this->imageGeneration)
        );

        $response->assertStatus(200)
                ->assertJson([
                    'liked' => true,
                    'likeCount' => 1
                ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $this->user->id,
            'image_generation_id' => $this->imageGeneration->id
        ]);

        Notification::assertSentTo(
            $this->imageGeneration->user,
            NewLike::class
        );
    }

    public function test_user_can_unlike_image(): void
    {
        // First like the image
        $this->user->likedImages()->attach($this->imageGeneration->id);

        $response = $this->actingAs($this->user)->postJson(
            route('likes.toggle', $this->imageGeneration)
        );

        $response->assertStatus(200)
                ->assertJson([
                    'liked' => false,
                    'likeCount' => 0
                ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->user->id,
            'image_generation_id' => $this->imageGeneration->id
        ]);
    }

    public function test_can_get_users_who_liked_image(): void
    {
        $likers = User::factory(3)->create();
        
        foreach ($likers as $liker) {
            $liker->likedImages()->attach($this->imageGeneration->id);
        }

        $response = $this->actingAs($this->user)->getJson(
            route('likes.users', $this->imageGeneration)
        );

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data')
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['id', 'name']
                    ]
                ]);
    }

    public function test_can_get_images_liked_by_user(): void
    {
        $images = ImageGeneration::factory(3)->create();
        
        foreach ($images as $image) {
            $this->user->likedImages()->attach($image->id);
        }

        $response = $this->actingAs($this->user)->getJson(route('likes.images'));

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data')
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['id', 'user']
                    ]
                ]);
    }

    public function test_like_notification_not_sent_to_self(): void
    {
        Notification::fake();

        $ownImage = ImageGeneration::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->actingAs($this->user)->postJson(
            route('likes.toggle', $ownImage)
        );

        Notification::assertNothingSent();
    }

    public function test_like_is_unique_per_user_and_image(): void
    {
        // Try to like the same image twice
        $this->actingAs($this->user)->postJson(
            route('likes.toggle', $this->imageGeneration)
        );
        
        $response = $this->actingAs($this->user)->postJson(
            route('likes.toggle', $this->imageGeneration)
        );

        $response->assertStatus(200)
                ->assertJson([
                    'liked' => false, // Should unlike instead of creating duplicate
                    'likeCount' => 0
                ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->user->id,
            'image_generation_id' => $this->imageGeneration->id
        ]);
    }
}
