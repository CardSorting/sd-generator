@props(['images'])

<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Images</h3>
            <a href="{{ route('generate.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">View All</a>
        </div>
        <div class="grid grid-cols-2 gap-4">
            @forelse($images as $image)
                <div class="relative group">
                    <img src="{{ $image->thumbnail_url }}" 
                         alt="Generated Image" 
                         class="w-full h-32 object-cover rounded"
                         loading="lazy">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity flex items-center justify-center space-x-2 opacity-0 group-hover:opacity-100">
                        <button type="button" 
                                data-action="download" 
                                data-generation-id="{{ $image->id }}" 
                                class="p-1 bg-white rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="h-4 w-4 text-gray-700" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button type="button" 
                                data-action="rerun" 
                                data-generation-id="{{ $image->id }}" 
                                class="p-1 bg-white rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="h-4 w-4 text-gray-700" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-2">
                        <p class="text-xs text-gray-500 truncate">{{ $image->prompt }}</p>
                        <p class="text-xs text-gray-400">{{ $image->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <div class="col-span-2">
                    <p class="text-sm text-gray-500 text-center">No images generated yet</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
