<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Verify Email</title>

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
                <h1 class="text-4xl font-bold text-emerald-600 mb-2">{{ config('app.name', 'Estatia') }}</h1>
                <p class="text-gray-600">Verify your email address</p>
            </div>

            <!-- Verify Email Card -->
            <div class="bg-white shadow-md rounded-xl p-6">
                <div class="mb-4 text-sm text-gray-600">
                    Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 text-sm font-medium text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-md p-3">
                        A new verification link has been sent to the email address you provided during registration.
                    </div>
                @endif

                <div class="flex items-center justify-between gap-4">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf

                        <x-button-primary>
                            Resend Verification Email
                        </x-button-primary>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 underline focus:outline-none focus:ring-2 focus:ring-emerald-300 rounded">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
