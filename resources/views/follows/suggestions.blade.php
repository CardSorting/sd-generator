@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold mb-6">Suggested Users to Follow</h2>

                @if($suggestions->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($suggestions as $user)
                            <div class="bg-white rounded-lg shadow p-6">
                                <div class="flex items-center mb-4">
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

                                <div class="flex justify-between text-sm text-gray-500 mb-4">
                                    <span>{{ $user->followers_count }} followers</span>
                                    <span>{{ $user->following_count }} following</span>
                                    <span>{{ $user->image_generations_count }} images</span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <form action="{{ route('follows.toggle', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Follow
                                        </button>
                                    </form>
                                    <a href="{{ route('follows.following', $user) }}" class="text-sm text-indigo-600 hover:text-indigo-900">View Profile</a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $suggestions->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">No suggestions available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
