<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'CommunityHub') }} - Komunitas Sumatera Barat</title>
        <link rel="icon" type="image/jpeg" href="{{ asset('images/logos/communityhub.jpeg') }}">

        <!-- PWA Meta & App Manifest -->
        <meta name="theme-color" content="#4f46e5">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="CommunityHub">
        <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col selection:bg-indigo-500 selection:text-white">
        @include('layouts.navigation')

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-3 w-full">
                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-between gap-3 text-xs shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="fa fa-circle-check text-emerald-500 text-sm"></i>
                        <span class="font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-3 w-full">
                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 flex items-center justify-between gap-3 text-xs shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="fa fa-circle-exclamation text-rose-500 text-sm"></i>
                        <span class="font-semibold">{{ session('error') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-3 w-full">
                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-between gap-3 text-xs shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="fa fa-triangle-exclamation text-amber-500 text-sm"></i>
                        <span class="font-semibold">{{ session('warning') }}</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main class="flex-grow pb-20 md:pb-0">
            {{ $slot }}
        </main>

        <!-- Unified Modern Footer -->
        @include('layouts.footer')

        <!-- Mobile App Components -->
        <x-bottom-nav />
        <x-mobile-post-modal />

        <!-- Global Interactive Toast Notification -->
        <x-toast-notification />

        <!-- Progressive Web App Service Worker Registration -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then(reg => {
                            // Service worker registered successfully
                        })
                        .catch(err => {
                            console.warn('[PWA] Service Worker registration skipped:', err);
                        });
                });
            }
        </script>
    </body>
</html>
