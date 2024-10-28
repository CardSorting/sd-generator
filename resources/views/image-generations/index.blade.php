@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Generation Form -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold mb-4">Create New Generation</h2>
                    <form action="{{ route('generate.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <x-label for="prompt" value="Prompt" />
                            <textarea id="prompt" name="prompt" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>{{ old('prompt') }}</textarea>
                            @error('prompt')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="negative_prompt" value="Negative Prompt (Optional)" />
                            <textarea id="negative_prompt" name="negative_prompt" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('negative_prompt') }}</textarea>
                            @error('negative_prompt')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-label for="model" value="Model" />
                                <select id="model" name="model" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @foreach($modelCategories as $category)
                                        <optgroup label="{{ ucfirst($category) }}">
                                            @foreach($models->where('category', $category) as $model)
                                                <option value="{{ $model->name }}" {{ old('model') == $model->name ? 'selected' : '' }}>
                                                    {{ $model->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('model')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <x-label for="steps" value="Steps" />
                                <input type="number" id="steps" name="steps" value="{{ old('steps', 20) }}" min="1" max="150" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('steps')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <x-label for="width" value="Width" />
                                    <input type="number" id="width" name="width" value="{{ old('width', 512) }}" min="64" max="2048" step="64" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('width')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <x-label for="height" value="Height" />
                                    <input type="number" id="height" name="height" value="{{ old('height', 512) }}" min="64" max="2048" step="64" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('height')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @if(session('error'))
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        @endif

                        <div class="flex justify-end">
                            <x-button>
                                Generate Image (1 Credit)
                            </x-button>
                        </div>
                    </form>
                </div>

                <!-- Previous Generations -->
                <div>
                    <h2 class="text-lg font-semibold mb-4">Your Generations</h2>
                    @if($generations->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($generations as $generation)
                                <div class="border rounded-lg overflow-hidden">
                                    @if($generation->thumbnail_url)
                                        <img src="{{ asset('storage/' . $generation->thumbnail_url) }}" 
                                             alt="Generated image" 
                                             class="w-full h-48 object-cover">
                                    @endif
                                    <div class="p-4">
                                        <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ $generation->prompt }}</p>
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-500">{{ $generation->created_at->diffForHumans() }}</span>
                                            <span class="px-2 py-1 text-xs rounded-full {{ $generation->status === 'completed' ? 'bg-green-100 text-green-800' : ($generation->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($generation->status) }}
                                            </span>
                                        </div>
                                        @if($generation->status === 'completed')
                                            <div class="mt-4 flex justify-between">
                                                <form action="{{ route('generate.download', $generation) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                                                        Download
                                                    </button>
                                                </form>
                                                <form action="{{ route('generate.rerun', $generation) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                                                        Rerun (1 Credit)
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $generations->links() }}
                        </div>
                    @else
                        <p class="text-gray-500">No generations yet. Start creating!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
