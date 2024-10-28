@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold mb-6">Recent Activity</h2>

                <div class="space-y-8">
                    @forelse($activities as $date => $dayActivities)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">{{ $date }}</h3>
                            <div class="space-y-4">
                                @foreach($dayActivities as $activity)
                                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                        <div class="px-4 py-5 sm:p-6">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    @switch($activity['type'])
                                                        @case('image_generation')
                                                            <span class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                            </span>
                                                            @break
                                                        @case('transaction')
                                                            <span class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            </span>
                                                            @break
                                                        @default
                                                            <span class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center">
                                                                <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            </span>
                                                    @endswitch
                                                </div>
                                                <div class="ml-4">
                                                    <h4 class="text-lg font-medium text-gray-900">
                                                        {{ $activity['description'] }}
                                                    </h4>
                                                    <p class="mt-1 text-sm text-gray-500">
                                                        {{ $activity['created_at']->format('g:i A') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-gray-500">No recent activities</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-8 flex justify-center">
                    <a href="{{ route('activities.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        View All Activities
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
