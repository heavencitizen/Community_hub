<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'CommunityHub') }} - Masuk & Registrasi</title>
        <link rel="icon" type="image/jpeg" href="{{ asset('images/logos/communityhub.jpeg') }}">

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
    <body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col justify-between selection:bg-indigo-500 selection:text-white">
        
        <!-- Header Brand -->
        <div class="pt-8 pb-4 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 group">
                <img src="{{ asset('images/logos/communityhub.jpeg') }}" alt="CommunityHub" class="w-9 h-9 rounded-xl object-cover shadow-md shadow-indigo-500/10 group-hover:scale-105 transition-transform duration-150">
                <div class="text-left">
                    <span class="font-extrabold text-lg text-slate-900 tracking-tight block leading-tight">Community<span class="text-indigo-600">Hub</span></span>
                    <span class="text-[9px] block text-indigo-500 font-bold tracking-widest uppercase -mt-0.5">Sumatera Barat</span>
                </div>
            </a>
        </div>

        <!-- Main Card Content -->
        <div class="w-full max-w-md mx-auto px-4">
            <div class="p-6 sm:p-8 bg-white border border-slate-200 shadow-xl rounded-3xl space-y-5">
                {{ $slot }}
            </div>
        </div>

        <!-- Footer -->
        <footer class="py-6 text-center text-xs text-slate-400 space-y-1">
            <div class="flex justify-center gap-3 text-slate-500 font-medium text-[11px]">
                <a href="{{ route('home') }}" class="hover:text-indigo-600">Beranda</a>
                <span>•</span>
                <a href="{{ route('events.index') }}" class="hover:text-indigo-600">Event</a>
                <span>•</span>
                <a href="{{ route('communities.index') }}" class="hover:text-indigo-600">Komunitas</a>
                <span>•</span>
                <a href="{{ route('donations.index') }}" class="hover:text-indigo-600">Donasi Amal</a>
            </div>
            <p class="text-[10px]">&copy; {{ date('Y') }} CommunityHub. All rights reserved.</p>
        </footer>
    </body>
</html>
