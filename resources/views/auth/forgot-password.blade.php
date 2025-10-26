<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Forgot Password</title>

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
                <p class="text-gray-600">Reset your password</p>
            </div>

            <!-- Forgot Password Card -->
            <div class="bg-white shadow-md rounded-xl p-6">
                <div class="mb-4 text-sm text-gray-600">
                    Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 text-sm font-medium text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-md p-3">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
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

                    <!-- Actions -->
                    <div class="flex items-center justify-end pt-2">
                        <x-button-primary class="w-full justify-center">
                            Email Password Reset Link
                        </x-button-primary>
                    </div>
                </form>

                <!-- Back to Login Link -->
                <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                    <p class="text-sm text-gray-600">
                        Remember your password?
                        <a href="{{ route('login') }}" class="text-emerald-600 hover:text-emerald-700 font-medium underline focus:outline-none focus:ring-2 focus:ring-emerald-300 rounded">
                            Sign in
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
