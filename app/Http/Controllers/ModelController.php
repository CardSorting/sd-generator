<?php

namespace App\Http\Controllers;

use App\Models\SDModel;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ModelController extends Controller
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('SD_API_BASE_URL');
    }

    public function index(Request $request)
    {
        $query = SDModel::with('tags');

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('style_type')) {
            $query->where('style_type', $request->style_type);
        }

        if ($request->has('tags')) {
            $tags = explode(',', $request->tags);
            $query->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('slug', $tags);
            });
        }

        $models = $query->get();

        // Get all tags grouped by type for filtering
        $tags = Tag::get()->groupBy('type');

        return response()->json([
            'models' => $models,
            'tags' => $tags,
            'categories' => SDModel::distinct('category')->pluck('category'),
            'styleTypes' => SDModel::distinct('style_type')->pluck('style_type'),
        ]);
    }

    public function sync()
    {
        try {
            Log::info('Fetching models from: ' . $this->baseUrl . '/sd-models');
            $response = Http::get($this->baseUrl . '/sd-models');
            
            if (!$response->successful()) {
                Log::error('Failed to fetch models. Status: ' . $response->status());
                return response()->json(['error' => 'Failed to fetch models'], $response->status());
            }

            $models = $response->json();

            foreach ($models as $modelData) {
                $model = SDModel::updateOrCreate(
                    ['title' => $modelData['title']],
                    [
                        'description' => $modelData['title'], // You might want to add proper descriptions
                        'category' => $this->guessCategory($modelData['title']),
                        'style_type' => $this->guessStyleType($modelData['title']),
                        'recommended_steps' => 20,
                        'recommended_cfg' => 7.0,
                    ]
                );

                // Assign tags based on the model name
                $this->assignTags($model, $modelData['title']);
            }

            return response()->json(['message' => 'Models synced successfully']);
        } catch (\Exception $e) {
            Log::error('Exception while syncing models: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to sync models: ' . $e->getMessage()], 500);
        }
    }

    private function guessCategory($title)
    {
        $title = Str::lower($title);
        
        if (Str::contains($title, ['anime', 'manga'])) {
            return 'anime';
        }
        
        if (Str::contains($title, ['realistic', 'photo', 'real'])) {
            return 'realistic';
        }
        
        if (Str::contains($title, ['paint', 'art'])) {
            return 'artistic';
        }
        
        return 'base';
    }

    private function guessStyleType($title)
    {
        $title = Str::lower($title);
        
        if (Str::contains($title, ['character', 'portrait'])) {
            return 'character';
        }
        
        if (Str::contains($title, ['landscape', 'scene'])) {
            return 'landscape';
        }
        
        if (Str::contains($title, ['concept', 'abstract'])) {
            return 'concept';
        }
        
        return 'general';
    }

    private function assignTags($model, $title)
    {
        $title = Str::lower($title);
        $tagIds = [];

        // Style tags
        if (Str::contains($title, ['photo', 'realistic'])) {
            $tagIds[] = Tag::where('name', 'Photorealistic')->first()->id;
        }
        if (Str::contains($title, ['anime', 'manga'])) {
            $tagIds[] = Tag::where('name', 'Anime')->first()->id;
        }
        if (Str::contains($title, ['digital'])) {
            $tagIds[] = Tag::where('name', 'Digital Art')->first()->id;
        }

        // Subject tags
        if (Str::contains($title, ['portrait', 'character'])) {
            $tagIds[] = Tag::where('name', 'Portrait')->first()->id;
        }
        if (Str::contains($title, ['landscape', 'scene'])) {
            $tagIds[] = Tag::where('name', 'Landscape')->first()->id;
        }
        if (Str::contains($title, ['abstract'])) {
            $tagIds[] = Tag::where('name', 'Abstract')->first()->id;
        }

        $model->tags()->sync($tagIds);
    }
}
