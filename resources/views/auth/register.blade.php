@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-900">Register</h2>

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <div>
                <x-label for="name" value="Name" class="block text-sm font-medium text-gray-700" />
                <div class="mt-1">
                    <x-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                </div>
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-label for="email" value="Email" class="block text-sm font-medium text-gray-700" />
                <div class="mt-1">
                    <x-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-label for="password" value="Password" class="block text-sm font-medium text-gray-700" />
                <div class="mt-1">
                    <x-input id="password" type="password" name="password" required autocomplete="new-password" />
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-label for="password_confirmation" value="Confirm Password" class="block text-sm font-medium text-gray-700" />
                <div class="mt-1">
                    <x-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
                </div>
            </div>

            <div class="flex items-center justify-between">
                <a class="text-sm font-medium text-indigo-600 hover:text-indigo-500" href="{{ route('login') }}">
                    Already registered?
                </a>

                <x-button>
                    Register
                </x-button>
            </div>
        </form>
    </div>
</div>
@endsection
