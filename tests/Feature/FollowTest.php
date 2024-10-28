<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\NewFollower;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $targetUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->targetUser = User::factory()->create();
    }

    public function test_user_can_follow_another_user(): void
    {
        Notification::fake();

        $response = $this->actingAs($this->user)->postJson(
            route('follows.toggle', $this->targetUser)
        );

        $response->assertStatus(200)
                ->assertJson([
                    'following' => true,
                    'followerCount' => 1
                ]);

        $this->assertDatabaseHas('follows', [
            'follower_id' => $this->user->id,
            'following_id' => $this->targetUser->id
        ]);

        Notification::assertSentTo(
            $this->targetUser,
            NewFollower::class
        );
    }

    public function test_user_can_unfollow_another_user(): void
    {
        // First follow the user
        $this->user->following()->attach($this->targetUser->id);

        $response = $this->actingAs($this->user)->postJson(
            route('follows.toggle', $this->targetUser)
        );

        $response->assertStatus(200)
                ->assertJson([
                    'following' => false,
                    'followerCount' => 0
                ]);

        $this->assertDatabaseMissing('follows', [
            'follower_id' => $this->user->id,
            'following_id' => $this->targetUser->id
        ]);
    }

    public function test_user_cannot_follow_self(): void
    {
        $response = $this->actingAs($this->user)->postJson(
            route('follows.toggle', $this->user)
        );

        $response->assertStatus(422)
                ->assertJson([
                    'message' => 'You cannot follow yourself'
                ]);

        $this->assertDatabaseMissing('follows', [
            'follower_id' => $this->user->id,
            'following_id' => $this->user->id
        ]);
    }

    public function test_can_get_user_followers(): void
    {
        $followers = User::factory(3)->create();
        
        foreach ($followers as $follower) {
            $follower->following()->attach($this->user->id);
        }

        $response = $this->actingAs($this->user)->getJson(
            route('follows.followers', $this->user)
        );

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data')
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'follower_count'
                        ]
                    ]
                ]);
    }

    public function test_can_get_user_following(): void
    {
        $following = User::factory(3)->create();
        
        foreach ($following as $followedUser) {
            $this->user->following()->attach($followedUser->id);
        }

        $response = $this->actingAs($this->user)->getJson(
            route('follows.following', $this->user)
        );

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data')
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'follower_count'
                        ]
                    ]
                ]);
    }

    public function test_can_get_follow_suggestions(): void
    {
        // Create some users
        $otherUsers = User::factory(5)->create();
        
        // Make some users follow the current user
        $followers = $otherUsers->take(2);
        foreach ($followers as $follower) {
            $follower->following()->attach($this->user->id);
        }

        $response = $this->actingAs($this->user)->getJson(
            route('follows.suggestions')
        );

        $response->assertStatus(200)
                ->assertJsonStructure([
                    '*' => [
                        'id',
                        'name'
                    ]
                ]);

        // Should not include users already being followed
        $suggestions = $response->json();
        $suggestionIds = collect($suggestions)->pluck('id');
        $this->assertFalse($suggestionIds->contains($this->user->id));
    }

    public function test_follow_is_unique_per_user_pair(): void
    {
        // Try to follow the same user twice
        $this->actingAs($this->user)->postJson(
            route('follows.toggle', $this->targetUser)
        );
        
        Notification::fake();
        
        $response = $this->actingAs($this->user)->postJson(
            route('follows.toggle', $this->targetUser)
        );

        $response->assertStatus(200)
                ->assertJson([
                    'following' => false, // Should unfollow instead of creating duplicate
                    'followerCount' => 0
                ]);

        Notification::assertNothingSent();

        $this->assertDatabaseMissing('follows', [
            'follower_id' => $this->user->id,
            'following_id' => $this->targetUser->id
        ]);
    }
}
