<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Radiohead Wallet') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Anti-FOUC: apply dark class BEFORE page renders --}}
        <script>
            (function() {
                if (localStorage.getItem('darkMode') === 'true') {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>
    </head>
    <body class="text-gray-900 antialiased" style="background-color: var(--app-bg);">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-8 flex flex-col items-center">
                <a href="/">
                    {{-- Radiohead Wallet Logo --}}
                    <svg class="w-16 h-16 text-[#386650]" viewBox="0 0 44 44" fill="none">
                        <!-- Radio waves -->
                        <path d="M 22 4 C 12 4 4 12 4 22 M 22 8 C 14.5 8 8.5 14.5 8.5 22" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity="0.4"/>
                        <path d="M 22 4 C 32 4 40 12 40 22 M 22 8 C 29.5 8 35.5 14.5 35.5 22" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity="0.4"/>
                        
                        <!-- Head / Robot face -->
                        <rect x="12" y="16" width="20" height="18" rx="4" stroke="currentColor" stroke-width="2.5"/>
                        
                        <!-- Eyes -->
                        <circle cx="17" cy="22" r="1.5" fill="currentColor"/>
                        <circle cx="27" cy="22" r="1.5" fill="currentColor"/>
                        
                        <!-- Mouth/Wallet slot -->
                        <line x1="16" y1="28" x2="28" y2="28" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                        
                        <!-- Antennas -->
                        <line x1="14" y1="16" x2="10" y2="10" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                        <line x1="30" y1="16" x2="34" y2="10" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                        <circle cx="10" cy="10" r="1.5" fill="currentColor"/>
                        <circle cx="34" cy="10" r="1.5" fill="currentColor"/>
                    </svg>
                </a>
                <h1 class="mt-4 text-2xl font-bold tracking-tight text-gray-900">Radiohead Wallet</h1>
                <p class="text-sm text-gray-500 mt-1">Masuk untuk mengelola keuangan Anda</p>
            </div>

            <div class="w-full sm:max-w-md px-8 py-10 bg-white shadow-xl overflow-hidden rounded-[2rem] border border-gray-50">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center text-sm text-gray-400">
                &copy; {{ date('Y') }} Radiohead Wallet. All rights reserved.
            </div>
        </div>
    </body>
</html>
