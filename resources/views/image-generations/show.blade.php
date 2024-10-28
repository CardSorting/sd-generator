@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Image Generation Details -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4">{{ $imageGeneration->prompt }}</h2>
                    <img src="{{ $imageGeneration->image_url }}" alt="{{ $imageGeneration->prompt }}" class="w-full rounded-lg shadow-lg mb-4">
                    
                    <!-- Stats -->
                    <div class="flex items-center space-x-4 mb-4">
                        <span class="text-gray-600">{{ $imageGeneration->comments_count }} {{ Str::plural('comment', $imageGeneration->comments_count) }}</span>
                        <span class="text-gray-600">{{ $imageGeneration->likes_count }} {{ Str::plural('like', $imageGeneration->likes_count) }}</span>
                    </div>

                    <!-- Like Button -->
                    @if($liked_by_user)
                        <button type="button" onclick="unlikeImage({{ $imageGeneration->id }})" class="bg-red-500 text-white px-4 py-2 rounded-md">Unlike</button>
                    @else
                        <button type="button" onclick="likeImage({{ $imageGeneration->id }})" class="bg-blue-500 text-white px-4 py-2 rounded-md">Like</button>
                    @endif
                </div>

                <!-- Comments Section -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold mb-4">Comments</h3>
                    
                    <!-- Comment Form -->
                    <form action="{{ route('comments.store', $imageGeneration) }}" method="POST" class="mb-6">
                        @csrf
                        <textarea name="content" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Add a comment..."></textarea>
                        <button type="submit" class="mt-2 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Post Comment</button>
                    </form>

                    <!-- Comments List -->
                    <div class="space-y-4">
                        @foreach($comments as $comment)
                            <div class="bg-gray-50 p-4 rounded-lg {{ $comment->parent_id ? 'ml-8' : '' }}">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="font-medium">{{ $comment->user->name }}</p>
                                        <p class="text-gray-600">{{ $comment->content }}</p>
                                        <p class="text-sm text-gray-500 mt-1">{{ $comment->created_at->diffForHumans() }}</p>
                                    </div>
                                    @if(auth()->id() === $comment->user_id || auth()->id() === $imageGeneration->user_id)
                                        <form action="{{ route('comments.destroy', $comment) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                                        </form>
                                    @endif
                                </div>

                                <!-- Nested Replies -->
                                @if($comment->replies)
                                    @foreach($comment->replies as $reply)
                                        <div class="bg-gray-100 p-4 rounded-lg mt-2 ml-8">
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <p class="font-medium">{{ $reply->user->name }}</p>
                                                    <p class="text-gray-600">{{ $reply->content }}</p>
                                                    <p class="text-sm text-gray-500 mt-1">{{ $reply->created_at->diffForHumans() }}</p>
                                                </div>
                                                @if(auth()->id() === $reply->user_id || auth()->id() === $imageGeneration->user_id)
                                                    <form action="{{ route('comments.destroy', $reply) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function likeImage(id) {
    fetch(`/likes/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    }).then(() => window.location.reload());
}

function unlikeImage(id) {
    fetch(`/likes/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    }).then(() => window.location.reload());
}
</script>
@endpush
@endsection
