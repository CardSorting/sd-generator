@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold">Users Who Liked This Image</h2>
                    <p class="text-gray-600 mt-1">Image prompt: {{ $imageGeneration->prompt }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($users as $user)
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <span class="inline-block h-12 w-12 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xl font-bold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <a href="{{ route('follows.following', $user->id) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                    View Profile
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-8">
                            <p class="text-gray-500">No users have liked this image yet</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
