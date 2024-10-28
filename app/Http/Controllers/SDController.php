<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SDController extends Controller
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('SD_API_BASE_URL');
    }

    public function index()
    {
        return view('sd.index');
    }

    public function getModels()
    {
        try {
            Log::info('Fetching models from: ' . $this->baseUrl . '/sd-models');
            $response = Http::get($this->baseUrl . '/sd-models');
            
            if (!$response->successful()) {
                Log::error('Failed to fetch models. Status: ' . $response->status());
                Log::error('Response: ' . $response->body());
                return response()->json(['error' => 'Failed to fetch models'], $response->status());
            }

            $models = $response->json();
            Log::info('Models fetched successfully', ['count' => count($models)]);
            return response()->json($models);
        } catch (\Exception $e) {
            Log::error('Exception while fetching models: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch models: ' . $e->getMessage()], 500);
        }
    }

    public function setOptions(Request $request)
    {
        try {
            $options = [
                'sd_model_checkpoint' => $request->input('sd_model_checkpoint')
            ];
            
            Log::info('Setting options', $options);
            $response = Http::post($this->baseUrl . '/options', $options);
            
            if (!$response->successful()) {
                Log::error('Failed to set options', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return response()->json(['error' => 'Failed to set options'], $response->status());
            }

            return response()->json(['message' => 'Options set successfully']);
        } catch (\Exception $e) {
            Log::error('Exception while setting options: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to set options: ' . $e->getMessage()], 500);
        }
    }

    public function generateImage(Request $request)
    {
        try {
            $validated = $request->validate([
                'prompt' => 'required|string',
                'negative_prompt' => 'nullable|string',
                'steps' => 'required|integer|min:1|max:150',
                'width' => 'required|integer|min:64|max:2048',
                'height' => 'required|integer|min:64|max:2048',
                'model' => 'required|string',
            ]);

            // Set the model
            $this->setOptions(new Request([
                'sd_model_checkpoint' => $validated['model']
            ]));

            // Prepare the request payload
            $payload = [
                'prompt' => $validated['prompt'],
                'negative_prompt' => $validated['negative_prompt'] ?? '',
                'steps' => intval($validated['steps']),
                'width' => intval($validated['width']),
                'height' => intval($validated['height']),
                'cfg_scale' => 7,
                'sampler_name' => 'Euler a',
                'batch_size' => 1,
                'n_iter' => 1,
                'seed' => -1,
                'restore_faces' => false,
                'tiling' => false,
                'enable_hr' => false
            ];

            Log::info('Generating image with parameters', $payload);
            $response = Http::post($this->baseUrl . '/txt2img', $payload);

            if (!$response->successful()) {
                Log::error('Failed to generate image', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return response()->json(['error' => 'Failed to generate image'], $response->status());
            }

            $result = $response->json();
            Log::info('Image generated successfully');
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Exception while generating image: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate image: ' . $e->getMessage()], 500);
        }
    }
}
