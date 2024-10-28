@props(['activity'])

<li class="activity-item" data-type="{{ $activity->type }}">
    <div class="relative pb-8">
        @unless($loop->last)
            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
        @endunless
        <div class="relative flex space-x-3">
            <!-- Activity Icon -->
            <div>
                @switch($activity->type)
                    @case('image_generation')
                        <span class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center ring-8 ring-white">
                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        @break
                    @case('transaction')
                        <span class="h-8 w-8 rounded-full {{ $activity->subject?->type === 'credit' ? 'bg-green-500' : 'bg-red-500' }} flex items-center justify-center ring-8 ring-white">
                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        @break
                    @default
                        <span class="h-8 w-8 rounded-full bg-gray-500 flex items-center justify-center ring-8 ring-white">
                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </span>
                @endswitch
            </div>

            <!-- Activity Content -->
            <div class="min-w-0 flex-1">
                <div class="text-sm text-gray-500">
                    <div class="font-medium text-gray-900">
                        {{ $activity->description }}
                    </div>
                    <span class="whitespace-nowrap">{{ $activity->created_at->diffForHumans() }}</span>
                </div>

                @if($activity->type === 'image_generation' && $activity->subject)
                    <div class="mt-2">
                        <div class="flex items-center space-x-2">
                            <img src="{{ $activity->subject->thumbnail_url }}" 
                                 alt="Generated Image" 
                                 class="h-20 w-20 object-cover rounded">
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">{{ Str::limit($activity->subject->prompt, 100) }}</p>
                                <div class="mt-2 flex space-x-2">
                                    <button type="button" 
                                            data-action="download" 
                                            data-generation-id="{{ $activity->subject->id }}" 
                                            class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Download
                                    </button>
                                    <button type="button" 
                                            data-action="rerun" 
                                            data-generation-id="{{ $activity->subject->id }}" 
                                            class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Re-run
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</li>
