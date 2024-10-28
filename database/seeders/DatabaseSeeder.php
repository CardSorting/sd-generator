<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Collection;
use App\Models\Comment;
use App\Models\ImageGeneration;
use App\Models\Like;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\NewComment;
use App\Notifications\NewFollower;
use App\Notifications\NewLike;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default tags
        $this->call([
            TagSeeder::class,
        ]);

        // Create a test user with some activity
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'credits_balance' => 100,
        ]);

        // Create some image generations for the test user
        $generations = ImageGeneration::factory()
            ->count(10)
            ->sequence(
                ['status' => 'completed'],
                ['status' => 'completed'],
                ['status' => 'completed'],
                ['status' => 'failed'],
                ['status' => 'completed'],
            )
            ->for($user)
            ->create();

        // Create some transactions for the test user
        Transaction::factory()
            ->count(20)
            ->sequence(
                ['type' => 'credit', 'amount' => 50],
                ['type' => 'debit', 'amount' => 1],
                ['type' => 'debit', 'amount' => 1],
                ['type' => 'credit', 'amount' => 25],
            )
            ->for($user)
            ->create()
            ->each(function ($transaction) {
                Activity::factory()
                    ->forTransaction()
                    ->create([
                        'user_id' => $transaction->user_id,
                        'subject_id' => $transaction->id,
                        'description' => $transaction->type === 'credit'
                            ? 'Added ' . $transaction->amount . ' credits'
                            : 'Used ' . $transaction->amount . ' credits',
                        'data' => [
                            'amount' => $transaction->amount,
                            'type' => $transaction->type,
                            'status' => $transaction->status,
                        ],
                    ]);
            });

        // Create activities for image generations
        foreach ($generations as $generation) {
            Activity::factory()
                ->forImageGeneration()
                ->create([
                    'user_id' => $user->id,
                    'subject_id' => $generation->id,
                    'description' => 'Generated image with prompt: ' . $generation->prompt,
                    'data' => [
                        'prompt' => $generation->prompt,
                        'settings' => $generation->settings,
                        'status' => $generation->status,
                    ],
                ]);
        }

        // Create collections for the test user
        Collection::factory()
            ->count(3)
            ->for($user)
            ->create()
            ->each(function ($collection) use ($generations) {
                $collection->imageGenerations()->attach(
                    $generations->random(rand(2, 4))->pluck('id')->toArray()
                );
            });

        // Create additional test users
        $additionalUsers = User::factory()
            ->count(5)
            ->create()
            ->each(function ($user) {
                // Create image generations for each user
                ImageGeneration::factory()
                    ->count(5)
                    ->for($user)
                    ->create()
                    ->each(function ($generation) {
                        Activity::factory()
                            ->forImageGeneration()
                            ->create([
                                'user_id' => $generation->user_id,
                                'subject_id' => $generation->id,
                                'description' => 'Generated image with prompt: ' . $generation->prompt,
                                'data' => [
                                    'prompt' => $generation->prompt,
                                    'settings' => $generation->settings,
                                    'status' => $generation->status,
                                ],
                            ]);
                    });

                // Create collections for each user
                Collection::factory()
                    ->count(2)
                    ->for($user)
                    ->create()
                    ->each(function ($collection) use ($user) {
                        $collection->imageGenerations()->attach(
                            $user->imageGenerations->random(rand(1, 3))->pluck('id')->toArray()
                        );
                    });
            });

        // Create comments, likes, and follows
        $allUsers = User::all();
        $allGenerations = ImageGeneration::all();

        foreach ($allGenerations as $generation) {
            // Add 1-3 comments to each generation
            $commenters = $allUsers->where('id', '!=', $generation->user_id)->random(rand(1, 3));
            foreach ($commenters as $commenter) {
                $comment = Comment::factory()
                    ->forImageGeneration($generation)
                    ->create([
                        'user_id' => $commenter->id,
                    ]);

                // Notify the generation owner about the comment
                $generation->user->notify(new NewComment($comment, $generation));
            }
        }

        // Create likes
        foreach ($allUsers as $user) {
            // Like 2-5 random generations from other users
            $otherGenerations = $allGenerations->where('user_id', '!=', $user->id);
            $likedGenerations = $otherGenerations->random(min($otherGenerations->count(), rand(2, 5)));
            
            foreach ($likedGenerations as $generation) {
                Like::factory()
                    ->forUserAndImage($user, $generation)
                    ->create();

                // Notify the generation owner about the like
                $generation->user->notify(new NewLike($user, $generation));
            }
        }

        // Create follows
        foreach ($allUsers as $user) {
            // Follow 1-3 random users
            $otherUsers = $allUsers->where('id', '!=', $user->id);
            $followedUsers = $otherUsers->random(min($otherUsers->count(), rand(1, 3)));
            
            foreach ($followedUsers as $followedUser) {
                $user->follow($followedUser);
                $followedUser->notify(new NewFollower($user));
            }
        }
    }
}
