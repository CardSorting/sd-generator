<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Comment;
use App\Models\Like;
use App\Models\ImageGeneration;
use App\Models\Activity;
use App\Services\ActivityFeedService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;

class CommunityViewTest extends FeatureTestCase
{
    use RefreshDatabase;

    private User $user;
    private ImageGeneration $imageGeneration;
    private ActivityFeedService $mockActivityFeedService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->imageGeneration = ImageGeneration::factory()->create([
            'user_id' => User::factory()->create()->id
        ]);

        // Mock ActivityFeedService
        $this->mockActivityFeedService = $this->mock(ActivityFeedService::class);
    }

    public function test_activity_feed_shows_recent_activities(): void
    {
        $this->actingAs($this->user);
        
        // Create test activities
        $activities = Activity::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'type' => 'image_generation',
            'description' => 'Generated an image'
        ]);

        // Mock service responses
        $this->mockActivityFeedService->shouldReceive('getActivities')
            ->once()
            ->andReturn(new LengthAwarePaginator($activities, 3, 10));

        $this->mockActivityFeedService->shouldReceive('getFilters')
            ->once()
            ->andReturn([
                'types' => ['image_generation' => 'Image Generations'],
                'date_ranges' => ['today' => 'Today']
            ]);

        $this->mockActivityFeedService->shouldReceive('getStatistics')
            ->once()
            ->andReturn([
                'total' => 3,
                'by_type' => ['image_generation' => 3],
                'recent_trend' => []
            ]);

        $response = $this->get(route('activities.index'));

        $response->assertStatus(200)
                ->assertViewIs('activities.index')
                ->assertSee('Generated an image');
    }

    public function test_activity_item_shows_correct_components(): void
    {
        $this->actingAs($this->user);
        
        // Create a comment using polymorphic relationship
        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_type' => ImageGeneration::class,
            'commentable_id' => $this->imageGeneration->id,
            'content' => 'Test comment'
        ]);

        // Create activity for the comment
        $activity = Activity::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'comment',
            'description' => 'Added a comment'
        ]);

        // Mock service responses
        $this->mockActivityFeedService->shouldReceive('getActivities')
            ->once()
            ->andReturn(new LengthAwarePaginator(collect([$activity]), 1, 10));

        $this->mockActivityFeedService->shouldReceive('getFilters')
            ->once()
            ->andReturn(['types' => [], 'date_ranges' => []]);

        $this->mockActivityFeedService->shouldReceive('getStatistics')
            ->once()
            ->andReturn(['total' => 1, 'by_type' => [], 'recent_trend' => []]);

        $response = $this->get(route('activities.index'));

        $response->assertStatus(200)
                ->assertSee('Added a comment');
    }

    public function test_like_button_shows_correct_state(): void
    {
        $this->actingAs($this->user);
        
        // Create a like
        $like = Like::factory()->create([
            'user_id' => $this->user->id,
            'image_generation_id' => $this->imageGeneration->id
        ]);

        $response = $this->get(route('generate.show', $this->imageGeneration));

        $response->assertStatus(200)
                ->assertSee('Unlike')
                ->assertSee('1 like');
    }

    public function test_follow_suggestions_shows_relevant_users(): void
    {
        $this->actingAs($this->user);
        
        // Create some users to follow
        $suggestedUsers = User::factory()->count(3)->create();

        // Create some follows to test the suggestions logic
        \DB::table('follows')->insert([
            'follower_id' => $this->user->id,
            'following_id' => $suggestedUsers[0]->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $response = $this->get(route('follows.suggestions'));

        $response->assertStatus(200)
                ->assertViewIs('follows.suggestions')
                ->assertDontSee($suggestedUsers[0]->name)
                ->assertSee($suggestedUsers[1]->name)
                ->assertSee($suggestedUsers[2]->name);
    }

    public function test_comment_section_shows_nested_replies(): void
    {
        $this->actingAs($this->user);
        
        // Create a parent comment
        $parentComment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_type' => ImageGeneration::class,
            'commentable_id' => $this->imageGeneration->id,
            'content' => 'Parent comment'
        ]);

        // Create replies to the parent comment
        $replies = Comment::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'commentable_type' => ImageGeneration::class,
            'commentable_id' => $this->imageGeneration->id,
            'parent_id' => $parentComment->id,
            'content' => 'Reply comment'
        ]);

        $response = $this->get(route('generate.show', $this->imageGeneration));

        $response->assertStatus(200)
                ->assertSee($parentComment->content);

        foreach ($replies as $reply) {
            $response->assertSee($reply->content);
        }
    }

    public function test_activity_stats_shows_correct_counts(): void
    {
        $this->actingAs($this->user);
        
        // Create some test data
        Comment::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'commentable_type' => ImageGeneration::class,
            'commentable_id' => $this->imageGeneration->id
        ]);

        Like::factory()->count(3)->create([
            'image_generation_id' => $this->imageGeneration->id
        ]);

        $response = $this->get(route('generate.show', $this->imageGeneration));

        $response->assertStatus(200)
                ->assertSee('2 comments')
                ->assertSee('3 likes');
    }

    public function test_notification_list_shows_recent_notifications(): void
    {
        $this->actingAs($this->user);
        
        // Create a notification (like on user's image)
        $userImage = ImageGeneration::factory()->create([
            'user_id' => $this->user->id
        ]);

        $liker = User::factory()->create();
        Like::factory()->create([
            'image_generation_id' => $userImage->id,
            'user_id' => $liker->id
        ]);

        // Trigger notification creation
        $userImage->user->notify(new \App\Notifications\NewLike($liker, $userImage));

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200)
                ->assertSee('liked your image');
    }

    public function test_recent_images_component_displays_correctly(): void
    {
        $this->actingAs($this->user);
        
        // Create some recent images
        $recentImages = ImageGeneration::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);

        foreach ($recentImages as $image) {
            $response->assertSee($image->prompt);
        }
    }

    public function test_liked_images_shows_correct_images(): void
    {
        $this->actingAs($this->user);
        
        // Create some images
        $images = ImageGeneration::factory()->count(3)->create();

        // Like two of the images
        Like::factory()->create([
            'user_id' => $this->user->id,
            'image_generation_id' => $images[0]->id
        ]);

        Like::factory()->create([
            'user_id' => $this->user->id,
            'image_generation_id' => $images[1]->id
        ]);

        $response = $this->get(route('likes.images'));

        $response->assertStatus(200)
                ->assertViewIs('likes.images')
                ->assertSee($images[0]->prompt)
                ->assertSee($images[1]->prompt)
                ->assertDontSee($images[2]->prompt);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
