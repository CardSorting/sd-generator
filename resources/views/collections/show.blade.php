@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Collection Header -->
                <div class="mb-8">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold mb-2">{{ $collection->name }}</h1>
                            @if($collection->description)
                                <p class="text-gray-600 mb-4">{{ $collection->description }}</p>
                            @endif
                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                <span>Created by {{ $collection->user->name }}</span>
                                <span>•</span>
                                <span>{{ $collection->created_at->diffForHumans() }}</span>
                                <span>•</span>
                                <span class="px-2 py-1 rounded-full {{ $collection->is_public ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $collection->is_public ? 'Public' : 'Private' }}
                                </span>
                            </div>
                        </div>
                        @if(auth()->id() === $collection->user_id)
                            <div class="flex space-x-4">
                                <button onclick="document.getElementById('editCollectionModal').classList.remove('hidden')" class="text-sm text-blue-600 hover:text-blue-800">
                                    Edit Collection
                                </button>
                                <form action="{{ route('collections.destroy', $collection) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this collection?')">
                                        Delete Collection
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Images Grid -->
                <div>
                    <h2 class="text-lg font-semibold mb-4">Images in Collection</h2>
                    @if($collection->imageGenerations->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($collection->imageGenerations as $image)
                                <div class="border rounded-lg overflow-hidden">
                                    @if($image->thumbnail_url)
                                        <img src="{{ asset('storage/' . $image->thumbnail_url) }}" 
                                             alt="Generated image" 
                                             class="w-full h-48 object-cover">
                                    @endif
                                    <div class="p-4">
                                        <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ $image->prompt }}</p>
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-500">{{ $image->created_at->diffForHumans() }}</span>
                                            <span class="text-xs text-gray-500">By {{ $image->user->name }}</span>
                                        </div>
                                        @if(auth()->id() === $collection->user_id)
                                            <div class="mt-4">
                                                <form action="{{ route('collections.remove-image', [$collection, $image]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                                        Remove from Collection
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No images in this collection yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Collection Modal -->
@if(auth()->id() === $collection->user_id)
    <div id="editCollectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit Collection</h3>
                <form action="{{ route('collections.update', $collection) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <x-label for="edit_name" value="Collection Name" />
                        <x-input id="edit_name" name="name" type="text" class="mt-1 block w-full" 
                                value="{{ $collection->name }}" required />
                    </div>

                    <div class="mb-4">
                        <x-label for="edit_description" value="Description (Optional)" />
                        <textarea id="edit_description" name="description" rows="2" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ $collection->description }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_public" value="1" 
                                {{ $collection->is_public ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Make this collection public</span>
                        </label>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" 
                                onclick="document.getElementById('editCollectionModal').classList.add('hidden')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <x-button>
                            Save Changes
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
