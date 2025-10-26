<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-emerald-50 to-gray-100 min-h-screen">
    <div class="flex items-center justify-center min-h-screen px-4 py-12">
        <div class="w-full max-w-md">
            <!-- Logo / App Name -->
            <div class="text-center mb-8">
                <img class="h-8 w-auto mx-auto mb-4" src="{{ asset('estatia.png') }}" alt="{{ config('theme.brand.name') }}" onerror="this.style.display='none'">
                <p class="text-gray-600">Sign in to your account</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white shadow-md rounded-xl p-6">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 text-sm font-medium text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-md p-3">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email Address -->
                    <x-form-field 
                        name="email" 
                        label="Email Address" 
                        type="email" 
                        :value="old('email')" 
                        required 
                        autofocus
                        autocomplete="username"
                    />

                    <!-- Password -->
                    <x-form-field 
                        name="password" 
                        label="Password" 
                        type="password" 
                        required
                        autocomplete="current-password"
                    />

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input 
                            id="remember_me" 
                            type="checkbox" 
                            name="remember"
                            class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                        >
                        <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-2">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-emerald-600 hover:text-emerald-700 underline focus:outline-none focus:ring-2 focus:ring-emerald-300 rounded">
                                Forgot password?
                            </a>
                        @endif

                        <x-button-primary class="ml-auto">
                            Log in
                        </x-button-primary>
                    </div>
                </form>

                <!-- Register Link -->
                @if (Route::has('register'))
                    <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                        <p class="text-sm text-gray-600">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="text-emerald-600 hover:text-emerald-700 font-medium underline focus:outline-none focus:ring-2 focus:ring-emerald-300 rounded">
                                Sign up
                            </a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
