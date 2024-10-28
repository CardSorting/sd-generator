<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Collection;
use App\Models\ImageGeneration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private ImageGeneration $imageGeneration;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->imageGeneration = ImageGeneration::factory()->create();
    }

    public function test_user_can_create_collection(): void
    {
        $response = $this->actingAs($this->user)->postJson(
            route('collections.store'),
            [
                'name' => 'My Favorites',
                'description' => 'A collection of my favorite images',
                'is_public' => true,
                'image_ids' => [$this->imageGeneration->id]
            ]
        );

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'collection' => [
                        'id',
                        'name',
                        'description',
                        'is_public',
                        'imageGenerations'
                    ]
                ]);

        $this->assertDatabaseHas('collections', [
            'user_id' => $this->user->id,
            'name' => 'My Favorites',
            'description' => 'A collection of my favorite images',
            'is_public' => true
        ]);

        $this->assertDatabaseHas('collection_image_generation', [
            'image_generation_id' => $this->imageGeneration->id
        ]);
    }

    public function test_user_can_update_own_collection(): void
    {
        $collection = Collection::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)->putJson(
            route('collections.update', $collection),
            [
                'name' => 'Updated Collection',
                'description' => 'Updated description',
                'is_public' => false
            ]
        );

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('collections', [
            'id' => $collection->id,
            'name' => 'Updated Collection',
            'description' => 'Updated description',
            'is_public' => false
        ]);
    }

    public function test_user_cannot_update_others_collection(): void
    {
        $otherUser = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($this->user)->putJson(
            route('collections.update', $collection),
            [
                'name' => 'Updated Collection',
                'description' => 'Updated description',
                'is_public' => false
            ]
        );

        $response->assertStatus(403);
    }

    public function test_user_can_delete_own_collection(): void
    {
        $collection = Collection::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)->deleteJson(
            route('collections.destroy', $collection)
        );

        $response->assertStatus(200);
        $this->assertDatabaseMissing('collections', ['id' => $collection->id]);
    }

    public function test_user_can_add_image_to_collection(): void
    {
        $collection = Collection::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)->postJson(
            route('collections.add-image', [
                'collection' => $collection,
                'imageGeneration' => $this->imageGeneration
            ])
        );

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('collection_image_generation', [
            'collection_id' => $collection->id,
            'image_generation_id' => $this->imageGeneration->id
        ]);
    }

    public function test_user_can_remove_image_from_collection(): void
    {
        $collection = Collection::factory()->create([
            'user_id' => $this->user->id
        ]);
        
        $collection->imageGenerations()->attach($this->imageGeneration->id);

        $response = $this->actingAs($this->user)->deleteJson(
            route('collections.remove-image', [
                'collection' => $collection,
                'imageGeneration' => $this->imageGeneration
            ])
        );

        $response->assertStatus(200);
        
        $this->assertDatabaseMissing('collection_image_generation', [
            'collection_id' => $collection->id,
            'image_generation_id' => $this->imageGeneration->id
        ]);
    }

    public function test_can_list_public_collections(): void
    {
        Collection::factory(3)->create(['is_public' => true]);
        Collection::factory(2)->create(['is_public' => false]);

        $response = $this->getJson(route('collections.index'));

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data');
    }

    public function test_user_can_see_own_private_collections(): void
    {
        Collection::factory(2)->create([
            'user_id' => $this->user->id,
            'is_public' => false
        ]);
        Collection::factory(3)->create(['is_public' => true]);

        $response = $this->actingAs($this->user)->getJson(route('collections.index'));

        $response->assertStatus(200)
                ->assertJsonCount(5, 'data');
    }

    public function test_cannot_view_private_collection_of_other_user(): void
    {
        $otherUser = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $otherUser->id,
            'is_public' => false
        ]);

        $response = $this->actingAs($this->user)->getJson(
            route('collections.show', $collection)
        );

        $response->assertStatus(403);
    }

    public function test_image_can_only_be_in_collection_once(): void
    {
        $collection = Collection::factory()->create([
            'user_id' => $this->user->id
        ]);

        // Add image first time
        $this->actingAs($this->user)->postJson(
            route('collections.add-image', [
                'collection' => $collection,
                'imageGeneration' => $this->imageGeneration
            ])
        );

        // Try to add same image again
        $response = $this->actingAs($this->user)->postJson(
            route('collections.add-image', [
                'collection' => $collection,
                'imageGeneration' => $this->imageGeneration
            ])
        );

        $this->assertDatabaseCount('collection_image_generation', 1);
    }
}
