@props(['notifications'])

<div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Notifications</h3>
            <button type="button" id="mark-all-read" class="text-sm text-indigo-600 hover:text-indigo-500">
                Mark all as read
            </button>
        </div>
        <div class="flow-root">
            <ul role="list" class="-my-4 divide-y divide-gray-200">
                @forelse($notifications as $notification)
                    <li class="py-4 {{ $notification->read_at ? '' : 'bg-blue-50' }}">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                @switch($notification->type)
                                    @case('App\Notifications\NewComment')
                                        <span class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                        @break
                                    @case('App\Notifications\NewLike')
                                        <span class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                        @break
                                    @case('App\Notifications\NewFollower')
                                        <span class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                                            </svg>
                                        </span>
                                        @break
                                    @default
                                        <span class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                @endswitch
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>
                            </div>
                            <div class="flex-shrink-0 whitespace-nowrap text-sm text-gray-500">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="py-4">
                        <p class="text-sm text-gray-500 text-center">No notifications</p>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
