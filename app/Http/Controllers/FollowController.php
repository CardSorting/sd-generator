<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\NewFollower;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function toggle(Request $request, User $user)
    {
        $currentUser = $request->user();

        if ($currentUser->id === $user->id) {
            return back()->with('error', 'You cannot follow yourself');
        }

        if ($currentUser->isFollowing($user)) {
            $currentUser->unfollow($user);
            $message = 'User unfollowed successfully';
        } else {
            $currentUser->follow($user);
            $user->notify(new NewFollower($currentUser));
            $message = 'User followed successfully';
        }

        return back()->with('success', $message);
    }

    public function suggestions(Request $request)
    {
        $user = $request->user();
        $following = $user->following()->pluck('users.id');
        
        $suggestions = User::where('id', '!=', $user->id)
            ->whereNotIn('id', $following)
            ->withCount(['followers', 'following', 'imageGenerations'])
            ->orderByDesc('image_generations_count')
            ->paginate(12);

        return view('follows.suggestions', [
            'suggestions' => $suggestions,
        ]);
    }

    public function followers(Request $request, User $user)
    {
        $followers = $user->followers()
            ->withCount(['followers', 'following', 'imageGenerations'])
            ->latest()
            ->paginate(12);

        return view('follows.followers', [
            'user' => $user,
            'followers' => $followers,
        ]);
    }

    public function following(Request $request, User $user)
    {
        $following = $user->following()
            ->withCount(['followers', 'following', 'imageGenerations'])
            ->latest()
            ->paginate(12);

        return view('follows.following', [
            'user' => $user,
            'following' => $following,
        ]);
    }
}
