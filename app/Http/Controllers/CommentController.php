<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\ImageGeneration;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    /**
     * Store a newly created comment.
     */
    public function store(Request $request, ImageGeneration $imageGeneration): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $comment = $imageGeneration->comments()->create([
            'content' => $validated['content'],
            'user_id' => auth()->id(),
        ]);

        // Load the user relationship for the response
        $comment->load('user');

        // If the comment is not from the image owner, create a notification
        if ($imageGeneration->user_id !== auth()->id()) {
            $imageGeneration->user->notify(new \App\Notifications\NewComment($comment));
        }

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment,
        ]);
    }

    /**
     * Update the specified comment.
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $comment->update($validated);

        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => $comment,
        ]);
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
        ]);
    }

    /**
     * Get comments for an image generation.
     */
    public function index(ImageGeneration $imageGeneration): JsonResponse
    {
        $comments = $imageGeneration->comments()
            ->with('user')
            ->latest()
            ->paginate(10);

        return response()->json($comments);
    }
}
