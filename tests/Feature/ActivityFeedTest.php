<?php

namespace Tests\Feature;

use App\Models\Activity;

class ActivityFeedTest extends FeatureTestCase
{
    public function test_activity_feed_requires_authentication(): void
    {
        $response = $this->getJson('/api/activities');

        $response->assertStatus(401);
    }

    public function test_users_can_view_their_activity_feed(): void
    {
        $user = $this->createAuthenticatedUser();
        Activity::factory()->count(5)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/activities');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'activities',
                'filters',
                'statistics',
            ]);

        $this->assertEquals(5, count($response->json('activities')));
    }

    public function test_users_cannot_view_others_activities(): void
    {
        $user1 = $this->createUser();
        Activity::factory()->count(5)->create(['user_id' => $user1->id]);

        $user2 = $this->createAuthenticatedUser();

        $response = $this->getJson('/api/activities');

        $response->assertStatus(200);
        $this->assertEquals(0, count($response->json('activities')));
    }

    public function test_activity_feed_can_be_filtered(): void
    {
        $user = $this->createAuthenticatedUser();

        Activity::factory()->count(3)->create([
            'user_id' => $user->id,
            'type' => 'image_generation',
        ]);

        Activity::factory()->count(2)->create([
            'user_id' => $user->id,
            'type' => 'transaction',
        ]);

        $response = $this->getJson('/api/activities?type=image_generation');

        $response->assertStatus(200);
        $this->assertEquals(3, count($response->json('activities')));
    }

    public function test_activity_feed_can_be_searched(): void
    {
        $user = $this->createAuthenticatedUser();

        Activity::factory()->create([
            'user_id' => $user->id,
            'description' => 'Test activity one',
        ]);

        Activity::factory()->create([
            'user_id' => $user->id,
            'description' => 'Another activity',
        ]);

        $response = $this->getJson('/api/activities?search=test');

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('activities')));
    }

    public function test_activity_feed_statistics_are_accurate(): void
    {
        $user = $this->createAuthenticatedUser();

        Activity::factory()->count(3)->create([
            'user_id' => $user->id,
            'type' => 'image_generation',
        ]);

        Activity::factory()->count(2)->create([
            'user_id' => $user->id,
            'type' => 'transaction',
        ]);

        $response = $this->getJson('/api/activities');

        $response->assertStatus(200);
        $statistics = $response->json('statistics');
        
        $this->assertEquals(5, $statistics['total']);
        $this->assertEquals(3, $statistics['by_type']['image_generation']);
        $this->assertEquals(2, $statistics['by_type']['transaction']);
    }
}
