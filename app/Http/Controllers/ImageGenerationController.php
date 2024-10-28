<?php

namespace App\Http\Controllers;

use App\Models\ImageGeneration;
use App\Services\ImageGenerationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ImageGenerationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private ImageGenerationService $imageGenerationService
    ) {}

    public function index()
    {
        $generations = Auth::user()->imageGenerations()
            ->latest()
            ->paginate(12);

        return view('image-generations.index', [
            'generations' => $generations,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prompt' => ['required', 'string', 'min:3', 'max:1000'],
            'negative_prompt' => ['nullable', 'string', 'max:1000'],
            'steps' => ['required', 'integer', 'min:1', 'max:150'],
            'width' => ['required', 'integer', 'min:64', 'max:2048'],
            'height' => ['required', 'integer', 'min:64', 'max:2048'],
            'model' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();

        if ($user->credits_balance < 1) {
            return back()->withErrors(['credits' => 'Insufficient credits to generate image.'])->withInput();
        }

        try {
            $this->imageGenerationService->generate($request->all(), $user);
            return redirect()->route('generate.index')->with('success', 'Image generation started.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(ImageGeneration $imageGeneration)
    {
        $this->authorize('view', $imageGeneration);

        $imageGeneration->loadCount(['comments', 'likes']);
        
        // Load parent comments with their replies and users
        $comments = $imageGeneration->comments()
            ->with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->latest()
            ->get();

        return view('image-generations.show', [
            'imageGeneration' => $imageGeneration,
            'comments' => $comments,
            'liked_by_user' => $imageGeneration->likes()->where('user_id', Auth::id())->exists()
        ]);
    }

    public function download(ImageGeneration $imageGeneration)
    {
        $this->authorize('download', $imageGeneration);

        if (!$imageGeneration->image_url) {
            return back()->withErrors(['error' => 'Image not available for download.']);
        }

        return response()->download(storage_path('app/' . $imageGeneration->image_url));
    }

    public function rerun(ImageGeneration $imageGeneration)
    {
        $this->authorize('rerun', $imageGeneration);

        $user = Auth::user();

        if ($user->credits_balance < 1) {
            return back()->withErrors(['credits' => 'Insufficient credits to rerun generation.']);
        }

        try {
            $this->imageGenerationService->rerun($imageGeneration, $user);
            return redirect()->route('generate.index')->with('success', 'Image generation restarted.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
