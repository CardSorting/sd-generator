@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Recent Generations -->
                    <div>
                        <h2 class="text-lg font-semibold mb-4">Recent Generations</h2>
                        @if($generations->count() > 0)
                            <div class="space-y-4">
                                @foreach($generations as $generation)
                                    <div class="border rounded-lg p-4">
                                        @if($generation->thumbnail_url)
                                            <img src="{{ asset('storage/' . $generation->thumbnail_url) }}" 
                                                 alt="Generated image" 
                                                 class="w-full h-32 object-cover rounded-lg mb-2">
                                        @endif
                                        <p class="text-sm text-gray-600 truncate">{{ $generation->prompt }}</p>
                                        <div class="mt-2 flex justify-between items-center">
                                            <span class="text-xs text-gray-500">{{ $generation->created_at->diffForHumans() }}</span>
                                            <span class="px-2 py-1 text-xs rounded-full {{ $generation->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($generation->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">No generations yet. <a href="{{ route('generate.index') }}" class="text-blue-500 hover:text-blue-700">Create your first one!</a></p>
                        @endif
                    </div>

                    <!-- Activity Feed -->
                    <div>
                        <h2 class="text-lg font-semibold mb-4">Recent Activity</h2>
                        @if($activities->count() > 0)
                            <div class="space-y-4">
                                @foreach($activities as $activity)
                                    <div class="border rounded-lg p-4">
                                        <div class="flex items-start">
                                            <div class="flex-1">
                                                <p class="text-sm">{{ $activity->description }}</p>
                                                <span class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</span>
                                            </div>
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                                {{ ucfirst($activity->type) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">No activity yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
