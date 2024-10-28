<?php

namespace App\Http\Controllers;

use App\Models\ImageGeneration;
use App\Models\Like;
use App\Models\User;
use App\Notifications\NewLike;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Request $request, ImageGeneration $imageGeneration)
    {
        $user = $request->user();
        $liked = $user->hasLiked($imageGeneration);

        if ($liked) {
            Like::where('user_id', $user->id)
                ->where('image_generation_id', $imageGeneration->id)
                ->delete();
        } else {
            Like::create([
                'user_id' => $user->id,
                'image_generation_id' => $imageGeneration->id,
            ]);

            if ($user->id !== $imageGeneration->user_id) {
                $imageGeneration->user->notify(new NewLike($user, $imageGeneration));
            }
        }

        return back()->with('success', $liked ? 'Image unliked' : 'Image liked');
    }

    public function users(Request $request, ImageGeneration $imageGeneration)
    {
        $users = $imageGeneration->likedBy()->paginate(20);

        return view('likes.users', [
            'users' => $users,
            'imageGeneration' => $imageGeneration,
        ]);
    }

    public function images(Request $request)
    {
        $images = $request->user()
            ->likedImages()
            ->with('user')
            ->latest()
            ->paginate(12);

        return view('likes.images', [
            'images' => $images,
        ]);
    }
}
