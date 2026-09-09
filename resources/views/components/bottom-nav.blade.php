@props([])

<!-- Modern Ergonomic Bottom Navigation Bar (Mobile Phone Only: md:hidden) -->
<nav class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-lg border-t border-slate-200/90 shadow-[0_-4px_20px_rgba(0,0,0,0.05)] pb-safe transition-all duration-200">
    <div class="max-w-md mx-auto px-4 h-16 flex items-center justify-around">

        <!-- 1. Beranda / Feed -->
        @auth
            <a href="{{ route('dashboard') }}" 
               class="flex flex-col items-center justify-center w-14 h-full py-1 text-center transition group {{ request()->routeIs('dashboard') ? 'text-indigo-600 font-extrabold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
                <div class="relative">
                    <i class="fa-solid fa-house text-lg transition-transform group-active:scale-90 {{ request()->routeIs('dashboard') ? 'text-indigo-600 scale-105' : '' }}"></i>
                </div>
                <span class="text-[10px] mt-1 tracking-tight">Beranda</span>
            </a>
        @else
            <a href="{{ route('home') }}" 
               class="flex flex-col items-center justify-center w-14 h-full py-1 text-center transition group {{ request()->routeIs('home') ? 'text-indigo-600 font-extrabold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
                <div class="relative">
                    <i class="fa-solid fa-house text-lg transition-transform group-active:scale-90 {{ request()->routeIs('home') ? 'text-indigo-600 scale-105' : '' }}"></i>
                </div>
                <span class="text-[10px] mt-1 tracking-tight">Beranda</span>
            </a>
        @endauth

        <!-- 2. Jelajah Komunitas -->
        <a href="{{ route('communities.index') }}" 
           class="flex flex-col items-center justify-center w-14 h-full py-1 text-center transition group {{ request()->routeIs('communities.*') ? 'text-indigo-600 font-extrabold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
            <div class="relative">
                <i class="fa-solid fa-users text-lg transition-transform group-active:scale-90 {{ request()->routeIs('communities.*') ? 'text-indigo-600 scale-105' : '' }}"></i>
            </div>
            <span class="text-[10px] mt-1 tracking-tight">Komunitas</span>
        </a>

        <!-- 3. CENTER ELEVATED ACTION: Quick Post -->
        <div class="relative -top-2 flex flex-col items-center justify-center">
            @auth
                <button type="button" 
                        @click="$dispatch('open-mobile-composer')" 
                        class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/35 border-2 border-white active:scale-90 transition-transform focus:outline-none"
                        title="Buat Postingan Baru">
                    <i class="fa-solid fa-plus text-lg"></i>
                </button>
            @else
                <a href="{{ route('login') }}" 
                   class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/35 border-2 border-white active:scale-90 transition-transform"
                   title="Masuk untuk Posting">
                    <i class="fa-solid fa-plus text-lg"></i>
                </a>
            @endauth
            <span class="text-[9px] font-bold text-slate-500 mt-0.5 tracking-tight">Posting</span>
        </div>

        <!-- 4. Jelajah Event & Tiket -->
        <a href="{{ route('events.index') }}" 
           class="flex flex-col items-center justify-center w-14 h-full py-1 text-center transition group {{ request()->routeIs('events.*') ? 'text-indigo-600 font-extrabold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
            <div class="relative">
                <i class="fa-solid fa-calendar-days text-lg transition-transform group-active:scale-90 {{ request()->routeIs('events.*') ? 'text-indigo-600 scale-105' : '' }}"></i>
            </div>
            <span class="text-[10px] mt-1 tracking-tight">Event</span>
        </a>

        <!-- 5. Profil Akun -->
        @auth
            <a href="{{ route('profile.show') }}" 
               class="flex flex-col items-center justify-center w-14 h-full py-1 text-center transition group {{ request()->routeIs('profile.*') || request()->routeIs('users.show') ? 'text-indigo-600 font-extrabold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
                <div class="relative">
                    <img src="{{ Auth::user()->avatar_url }}" 
                         alt="{{ Auth::user()->name }}" 
                         class="w-6 h-6 rounded-full object-cover border {{ request()->routeIs('profile.*') || request()->routeIs('users.show') ? 'border-indigo-600 ring-2 ring-indigo-500/30' : 'border-slate-300' }}">
                    @if(Auth::user()->unreadNotificationsCount() > 0)
                        <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-rose-500 border-2 border-white"></span>
                    @endif
                </div>
                <span class="text-[10px] mt-1 tracking-tight truncate max-w-[50px]">{{ explode(' ', Auth::user()->name)[0] }}</span>
            </a>
        @else
            <a href="{{ route('login') }}" 
               class="flex flex-col items-center justify-center w-14 h-full py-1 text-center transition group text-slate-400 hover:text-indigo-600 font-medium">
                <div class="relative">
                    <i class="fa-regular fa-circle-user text-lg transition-transform group-active:scale-90"></i>
                </div>
                <span class="text-[10px] mt-1 tracking-tight">Masuk</span>
            </a>
        @endauth

    </div>
</nav>