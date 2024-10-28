<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\ImageGeneration;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CollectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the collections.
     */
    public function index(Request $request): View
    {
        $query = Collection::query()->with(['user', 'imageGenerations']);

        if (!$request->user()) {
            $query->where('is_public', true);
        } else {
            $query->where(function($q) use ($request) {
                $q->where('user_id', $request->user()->id)
                  ->orWhere('is_public', true);
            });
        }

        $collections = $query->latest()
                           ->withCount('imageGenerations')
                           ->paginate(12);

        return view('collections.index', compact('collections'));
    }

    /**
     * Store a newly created collection.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_public' => ['boolean'],
        ]);

        $collection = auth()->user()->collections()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_public' => $validated['is_public'] ?? true,
        ]);

        return redirect()
            ->route('collections.show', $collection)
            ->with('success', 'Collection created successfully');
    }

    /**
     * Display the specified collection.
     */
    public function show(Collection $collection): View
    {
        if (!$collection->is_public && (!auth()->user() || auth()->id() !== $collection->user_id)) {
            abort(403, 'This collection is private');
        }

        $collection->load(['user', 'imageGenerations.user']);

        return view('collections.show', compact('collection'));
    }

    /**
     * Update the specified collection.
     */
    public function update(Request $request, Collection $collection): RedirectResponse
    {
        $this->authorize('update', $collection);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_public' => ['boolean'],
        ]);

        $collection->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'is_public' => $validated['is_public'] ?? false,
        ]);

        return redirect()
            ->route('collections.show', $collection)
            ->with('success', 'Collection updated successfully');
    }

    /**
     * Remove the specified collection.
     */
    public function destroy(Collection $collection): RedirectResponse
    {
        $this->authorize('delete', $collection);

        $collection->delete();

        return redirect()
            ->route('collections.index')
            ->with('success', 'Collection deleted successfully');
    }

    /**
     * Add an image to a collection.
     */
    public function addImage(Request $request, Collection $collection, ImageGeneration $imageGeneration): RedirectResponse
    {
        $this->authorize('addImage', $collection);

        if ($collection->imageGenerations()->where('image_generation_id', $imageGeneration->id)->exists()) {
            return back()->with('error', 'Image is already in this collection');
        }

        $collection->imageGenerations()->attach($imageGeneration->id);

        return back()->with('success', 'Image added to collection successfully');
    }

    /**
     * Remove an image from a collection.
     */
    public function removeImage(Collection $collection, ImageGeneration $imageGeneration): RedirectResponse
    {
        $this->authorize('removeImage', $collection);

        $collection->imageGenerations()->detach($imageGeneration->id);

        return back()->with('success', 'Image removed from collection successfully');
    }
}
