@extends('layouts.app')

@section('content')
<div class="relative flex items-top justify-center min-h-screen bg-gray-100 sm:items-center py-4 sm:pt-0">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-center pt-8 sm:justify-start sm:pt-0">
            <h1 class="text-4xl font-bold text-gray-900">SD Generator</h1>
        </div>

        <div class="mt-8 bg-white overflow-hidden shadow sm:rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-2">
                <div class="p-6">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <div class="ml-4 text-lg leading-7 font-semibold">
                            Generate Amazing Images
                        </div>
                    </div>

                    <div class="ml-12">
                        <div class="mt-2 text-gray-600 text-sm">
                            Turn your ideas into stunning images with our state-of-the-art AI image generation. Simply describe what you want to see, and watch as your vision comes to life.
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-200 md:border-t-0 md:border-l">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        <div class="ml-4 text-lg leading-7 font-semibold">
                            Customizable Options
                        </div>
                    </div>

                    <div class="ml-12">
                        <div class="mt-2 text-gray-600 text-sm">
                            Fine-tune your generations with advanced settings. Choose from different models, adjust image dimensions, and control the generation process to get exactly what you want.
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-200">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        <div class="ml-4 text-lg leading-7 font-semibold">
                            Activity Tracking
                        </div>
                    </div>

                    <div class="ml-12">
                        <div class="mt-2 text-gray-600 text-sm">
                            Keep track of all your generations and activities. Review your history, rerun successful generations, and learn from your creative journey.
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-200 md:border-l">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="ml-4 text-lg leading-7 font-semibold">
                            Get Started Now
                        </div>
                    </div>

                    <div class="ml-12">
                        <div class="mt-2 text-gray-600 text-sm">
                            Ready to start creating? Sign up now and get 10 free credits to begin your creative journey with SD Generator.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-center mt-4 sm:items-center sm:justify-between">
            <div class="text-center text-sm text-gray-500 sm:text-left">
                <div class="flex items-center">
                    @auth
                        <a href="{{ route('dashboard') }}" class="ml-1 underline">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="ml-1 underline">
                            Log in
                        </a>

                        <a href="{{ route('register') }}" class="ml-4 underline">
                            Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
