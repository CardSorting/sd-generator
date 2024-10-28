<nav class="bg-white border-b border-gray-100" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-md">
                        <span class="text-xl font-bold text-gray-900">SD Generator</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                            Dashboard
                        </a>
                        <a href="{{ route('generate.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('generate.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                            Generate
                        </a>
                        
                        <!-- Community Dropdown -->
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out"
                                        @keydown.arrow.up.prevent="$refs.communityMenu?.focusLast()"
                                        @keydown.arrow.down.prevent="$refs.communityMenu?.focusFirst()">
                                    Community
                                    <svg class="ml-2 -mr-0.5 h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <div x-ref="communityMenu" class="focus:outline-none" tabindex="-1"
                                 @keydown.arrow.up.prevent="focusPrevious"
                                 @keydown.arrow.down.prevent="focusNext">
                                <x-dropdown-link href="{{ route('activities.index') }}" :active="request()->routeIs('activities.*')">
                                    Activity Feed
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('collections.index') }}" :active="request()->routeIs('collections.*')">
                                    Collections
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('likes.images') }}" :active="request()->routeIs('likes.*')">
                                    Liked Images
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('follows.suggestions') }}" :active="request()->routeIs('follows.suggestions')">
                                    Find Users
                                </x-dropdown-link>
                            </div>
                        </x-dropdown>
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
                @auth
                    <!-- Notifications -->
                    <x-dropdown align="right" width="80">
                        <x-slot name="trigger">
                            <button class="relative p-1 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-full transition duration-150 ease-in-out"
                                    @keydown.arrow.up.prevent="$refs.notificationMenu?.focusLast()"
                                    @keydown.arrow.down.prevent="$refs.notificationMenu?.focusFirst()">
                                <span class="sr-only">View notifications</span>
                                <!-- Bell Icon -->
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <!-- Notification Badge -->
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="notification-badge absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
                                        {{ auth()->user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                            </button>
                        </x-slot>

                        <div x-ref="notificationMenu" class="focus:outline-none" tabindex="-1">
                            <x-notification-list :notifications="auth()->user()->notifications()->latest()->take(5)->get()" />
                        </div>
                    </x-dropdown>

                    <!-- User Menu -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-md transition duration-150 ease-in-out"
                                    @keydown.arrow.up.prevent="$refs.userMenu?.focusLast()"
                                    @keydown.arrow.down.prevent="$refs.userMenu?.focusFirst()">
                                <span class="mr-4">Credits: {{ auth()->user()->credits_balance }}</span>
                                <span>{{ auth()->user()->name }}</span>
                                <svg class="ml-2 -mr-0.5 h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <div x-ref="userMenu" class="focus:outline-none" tabindex="-1"
                             @keydown.arrow.up.prevent="focusPrevious"
                             @keydown.arrow.down.prevent="focusNext">
                            <x-dropdown-link href="{{ route('follows.following', auth()->id()) }}" :active="request()->routeIs('follows.following')">
                                Following
                            </x-dropdown-link>
                            <x-dropdown-link href="{{ route('follows.followers', auth()->id()) }}" :active="request()->routeIs('follows.followers')">
                                Followers
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}"
                                               onclick="event.preventDefault(); this.closest('form').submit();">
                                    Logout
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-md transition duration-150 ease-in-out">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="text-sm text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-md transition duration-150 ease-in-out">
                        Register
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dropdownNav', () => ({
        focusNext(e) {
            const items = [...e.target.querySelectorAll('[role="menuitem"]')]
            const index = items.indexOf(document.activeElement)
            const next = items[index + 1] || items[0]
            next?.focus()
        },
        focusPrevious(e) {
            const items = [...e.target.querySelectorAll('[role="menuitem"]')]
            const index = items.indexOf(document.activeElement)
            const previous = items[index - 1] || items[items.length - 1]
            previous?.focus()
        },
        focusFirst() {
            this.$el.querySelector('[role="menuitem"]')?.focus()
        },
        focusLast() {
            const items = this.$el.querySelectorAll('[role="menuitem"]')
            items[items.length - 1]?.focus()
        }
    }))
})
</script>
@endpush
