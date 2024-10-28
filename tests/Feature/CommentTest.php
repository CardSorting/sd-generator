<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Comment;
use App\Models\ImageGeneration;
use App\Notifications\NewComment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CommentTest extends TestCase
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

    public function test_user_can_comment_on_image(): void
    {
        Notification::fake();

        $response = $this->actingAs($this->user)->postJson(
            route('comments.store', $this->imageGeneration),
            ['content' => 'Great image!']
        );

        $response->assertStatus(200)
                ->assertJsonStructure(['message', 'comment']);

        $this->assertDatabaseHas('comments', [
            'user_id' => $this->user->id,
            'image_generation_id' => $this->imageGeneration->id,
            'content' => 'Great image!'
        ]);

        Notification::assertSentTo(
            $this->imageGeneration->user,
            NewComment::class
        );
    }

    public function test_user_can_update_own_comment(): void
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'image_generation_id' => $this->imageGeneration->id
        ]);

        $response = $this->actingAs($this->user)->putJson(
            route('comments.update', $comment),
            ['content' => 'Updated comment']
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Updated comment'
        ]);
    }

    public function test_user_cannot_update_others_comment(): void
    {
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $otherUser->id,
            'image_generation_id' => $this->imageGeneration->id
        ]);

        $response = $this->actingAs($this->user)->putJson(
            route('comments.update', $comment),
            ['content' => 'Updated comment']
        );

        $response->assertStatus(403);
    }

    public function test_user_can_delete_own_comment(): void
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'image_generation_id' => $this->imageGeneration->id
        ]);

        $response = $this->actingAs($this->user)->deleteJson(
            route('comments.destroy', $comment)
        );

        $response->assertStatus(200);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_image_owner_can_delete_any_comment(): void
    {
        $comment = Comment::factory()->create([
            'user_id' => User::factory()->create()->id,
            'image_generation_id' => $this->imageGeneration->id
        ]);

        $response = $this->actingAs($this->imageGeneration->user)->deleteJson(
            route('comments.destroy', $comment)
        );

        $response->assertStatus(200);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_user_cannot_delete_others_comment(): void
    {
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $otherUser->id,
            'image_generation_id' => $this->imageGeneration->id
        ]);

        $response = $this->actingAs($this->user)->deleteJson(
            route('comments.destroy', $comment)
        );

        $response->assertStatus(403);
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }

    public function test_can_list_comments_for_image(): void
    {
        Comment::factory(3)->create([
            'image_generation_id' => $this->imageGeneration->id
        ]);

        $response = $this->actingAs($this->user)->getJson(
            route('comments.index', $this->imageGeneration)
        );

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data');
    }
}
