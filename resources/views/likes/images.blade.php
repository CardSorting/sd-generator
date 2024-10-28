@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold mb-6">Images You've Liked</h2>

                @if($images->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($images as $image)
                            <div class="bg-white rounded-lg shadow overflow-hidden">
                                @if($image->result && isset($image->result['url']))
                                    <img src="{{ $image->result['url'] }}" alt="{{ $image->prompt }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif

                                <div class="p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <span class="inline-block h-8 w-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-sm font-bold">
                                                {{ strtoupper(substr($image->user->name, 0, 1)) }}
                                            </span>
                                            <span class="ml-2 text-sm text-gray-600">{{ $image->user->name }}</span>
                                        </div>
                                        <span class="text-sm text-gray-500">{{ $image->created_at->diffForHumans() }}</span>
                                    </div>

                                    <p class="text-sm text-gray-700 line-clamp-2 mb-4">{{ $image->prompt }}</p>

                                    <div class="flex justify-between items-center">
                                        <div class="flex space-x-4">
                                            <form action="{{ route('likes.toggle', $image) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="flex items-center text-red-600 hover:text-red-800">
                                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="ml-1 text-sm">Unlike</span>
                                                </button>
                                            </form>
                                            <a href="{{ route('likes.users', $image) }}" class="flex items-center text-gray-600 hover:text-gray-800">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                <span class="ml-1 text-sm">{{ $image->likes_count ?? 0 }} likes</span>
                                            </a>
                                        </div>
                                        <a href="{{ route('generate.show', $image) }}" class="text-sm text-indigo-600 hover:text-indigo-900">View Details</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $images->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">You haven't liked any images yet</p>
                        <a href="{{ route('generate.index') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Browse Images
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
