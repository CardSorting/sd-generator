@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Create Collection Form -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold mb-4">Create New Collection</h2>
                    <form action="{{ route('collections.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <x-label for="name" value="Collection Name" />
                            <x-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="description" value="Description (Optional)" />
                            <textarea id="description" name="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="is_public" name="is_public" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <label for="is_public" class="ml-2 text-sm text-gray-600">Make this collection public</label>
                        </div>

                        @if(session('error'))
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        @endif

                        <div class="flex justify-end">
                            <x-button>
                                Create Collection
                            </x-button>
                        </div>
                    </form>
                </div>

                <!-- Collections List -->
                <div>
                    <h2 class="text-lg font-semibold mb-4">Your Collections</h2>
                    @if($collections->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($collections as $collection)
                                <div class="border rounded-lg overflow-hidden">
                                    <div class="p-4">
                                        <h3 class="font-semibold text-lg mb-2">{{ $collection->name }}</h3>
                                        @if($collection->description)
                                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $collection->description }}</p>
                                        @endif
                                        <div class="flex justify-between items-center mb-3">
                                            <span class="text-xs text-gray-500">{{ $collection->created_at->diffForHumans() }}</span>
                                            <span class="px-2 py-1 text-xs rounded-full {{ $collection->is_public ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $collection->is_public ? 'Public' : 'Private' }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-600 mb-3">
                                            {{ $collection->imageGenerations->count() }} {{ Str::plural('image', $collection->imageGenerations->count()) }}
                                        </div>
                                        <div class="flex justify-between">
                                            <a href="{{ route('collections.show', $collection) }}" class="text-sm text-blue-600 hover:text-blue-800">
                                                View Collection
                                            </a>
                                            <form action="{{ route('collections.destroy', $collection) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this collection?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $collections->links() }}
                        </div>
                    @else
                        <p class="text-gray-500">No collections yet. Start organizing your generations!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
