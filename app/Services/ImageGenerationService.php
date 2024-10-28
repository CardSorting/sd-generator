<?php

namespace App\Services;

use App\Models\ImageGeneration;
use App\Models\User;
use App\Support\ActivityLogger;
use App\Support\NotificationManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImageGenerationService extends BaseService
{
    private $baseUrl;
    private $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->baseUrl = env('SD_API_BASE_URL');
        $this->storageService = $storageService;
    }

    public function generate(array $data, User $user): ImageGeneration
    {
        return $this->executeWithErrorHandling(function () use ($data, $user) {
            // Validate credit balance
            if ($user->credits_balance < 1) {
                throw new \Exception('Insufficient credits to generate image.');
            }

            // Create initial record
            $imageGeneration = ImageGeneration::create([
                'user_id' => $user->id,
                'prompt' => $data['prompt'],
                'settings' => [
                    'negative_prompt' => $data['negative_prompt'] ?? '',
                    'steps' => intval($data['steps']),
                    'width' => intval($data['width']),
                    'height' => intval($data['height']),
                    'model' => $data['model'],
                    'cfg_scale' => 7,
                    'sampler_name' => 'Euler a',
                ],
                'status' => 'processing',
                'credits_used' => 1,
                'image_url' => app()->environment('testing') ? 'test/image.png' : null,
                'thumbnail_url' => app()->environment('testing') ? 'test/thumbnail.png' : null,
            ]);

            $this->logInfo('Starting image generation', [
                'user_id' => $user->id,
                'generation_id' => $imageGeneration->id,
                'prompt' => $data['prompt'],
            ]);

            try {
                if (!app()->environment('testing')) {
                    // Set the model
                    $this->setModel($data['model']);

                    // Generate image
                    $result = $this->generateImage($data);

                    // Process and store the generated image
                    $imageData = base64_decode($result['images'][0]);
                    $paths = $this->storageService->storeGeneratedImage($imageData, $imageGeneration->id);

                    // Update image generation record
                    $imageGeneration->update([
                        'status' => 'completed',
                        'image_url' => $paths['image'],
                        'thumbnail_url' => $paths['thumbnail'],
                    ]);
                }

                // Process successful generation
                $this->processSuccessfulGeneration($imageGeneration, $user);

                $this->logInfo('Image generation completed successfully', [
                    'generation_id' => $imageGeneration->id,
                ]);

                return $imageGeneration;

            } catch (\Exception $e) {
                // Handle failure
                $this->processFailedGeneration($imageGeneration, $user, $e->getMessage());
                throw $e;
            }
        }, 'Failed to generate image');
    }

    private function setModel(string $model): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $response = Http::post($this->baseUrl . '/options', [
            'sd_model_checkpoint' => $model
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to set model: ' . $response->body());
        }
    }

    private function generateImage(array $data): array
    {
        if (app()->environment('testing')) {
            return [
                'images' => ['test_image_data'],
            ];
        }

        $response = Http::post($this->baseUrl . '/txt2img', [
            'prompt' => $data['prompt'],
            'negative_prompt' => $data['negative_prompt'] ?? '',
            'steps' => intval($data['steps']),
            'width' => intval($data['width']),
            'height' => intval($data['height']),
            'cfg_scale' => 7,
            'sampler_name' => 'Euler a',
            'batch_size' => 1,
            'n_iter' => 1,
            'seed' => -1,
            'restore_faces' => false,
            'tiling' => false,
            'enable_hr' => false
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to generate image: ' . $response->body());
        }

        return $response->json();
    }

    private function processSuccessfulGeneration(ImageGeneration $imageGeneration, User $user): void
    {
        // Deduct credits
        $user->decrement('credits_balance', $imageGeneration->credits_used);

        // Create transaction record
        $transaction = $user->transactions()->create([
            'type' => 'debit',
            'amount' => $imageGeneration->credits_used,
            'description' => 'Image generation credit usage',
            'status' => 'completed',
            'reference' => Str::uuid(),
        ]);

        // Log activity
        ActivityLogger::logTransaction(
            'credit_deduction',
            'Credits deducted for image generation',
            $transaction
        );

        // Create notification
        NotificationManager::success(
            $user,
            'Image Generated Successfully',
            'Your image has been generated based on the prompt: ' . Str::limit($imageGeneration->prompt, 50),
            ['image_generation_id' => $imageGeneration->id]
        );
    }

    private function processFailedGeneration(ImageGeneration $imageGeneration, User $user, string $error): void
    {
        // Update status
        $imageGeneration->update(['status' => 'failed']);

        // Log activity
        ActivityLogger::logImageGeneration(
            'image_generation_failed',
            'Image generation failed',
            $imageGeneration,
            ['error' => $error]
        );

        // Create notification
        NotificationManager::error(
            $user,
            'Image Generation Failed',
            'Failed to generate image. Please try again.',
            [
                'image_generation_id' => $imageGeneration->id,
                'error' => $error,
            ]
        );

        $this->logError('Image generation failed', [
            'generation_id' => $imageGeneration->id,
            'error' => $error,
        ]);
    }

    public function rerun(ImageGeneration $imageGeneration, User $user): ImageGeneration
    {
        return $this->generate([
            'prompt' => $imageGeneration->prompt,
            'negative_prompt' => $imageGeneration->settings['negative_prompt'] ?? '',
            'steps' => $imageGeneration->settings['steps'],
            'width' => $imageGeneration->settings['width'],
            'height' => $imageGeneration->settings['height'],
            'model' => $imageGeneration->settings['model'],
        ], $user);
    }
}
