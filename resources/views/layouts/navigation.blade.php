<nav x-data="{ open: false }" class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex justify-between h-14 sm:h-16">
            <div class="flex items-center space-x-2 lg:space-x-3 xl:space-x-4 shrink-0">
                <!-- Logo & Brand -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <img src="{{ asset('images/logos/communityhub.jpeg') }}" alt="CommunityHub" class="w-8 h-8 rounded-lg object-cover shadow-sm group-hover:scale-105 transition-transform duration-150">
                        <span class="font-extrabold text-base text-slate-900 tracking-tight whitespace-nowrap">Community<span class="text-indigo-600">Hub</span></span>
                    </a>
                </div>

                <!-- Main Navigation Links in Header / Navigation Bar -->
                <div class="hidden md:-my-px md:flex md:items-center md:space-x-0.5 lg:space-x-1" x-data="{ moreNav: false }">
                    <!-- 1. Beranda / Dashboard -->
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap {{ request()->routeIs('dashboard') ? 'text-indigo-600 bg-indigo-50/90 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                            <i class="fa fa-home me-1.5 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                            <span>Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('home') }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap {{ request()->routeIs('home') ? 'text-indigo-600 bg-indigo-50/90 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                            <i class="fa fa-home me-1.5 {{ request()->routeIs('home') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                            <span>Beranda</span>
                        </a>
                    @endauth

                    <!-- 2. Event -->
                    <a href="{{ route('events.index') }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap {{ request()->routeIs('events.*') ? 'text-indigo-600 bg-indigo-50/90 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                        <i class="fa fa-calendar-alt me-1.5 {{ request()->routeIs('events.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Event</span>
                    </a>

                    <!-- 3. Komunitas -->
                    <a href="{{ route('communities.index') }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap {{ request()->routeIs('communities.*') ? 'text-indigo-600 bg-indigo-50/90 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                        <i class="fa fa-users me-1.5 {{ request()->routeIs('communities.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Komunitas</span>
                    </a>

                    <!-- 4. Jual Beli -->
                    <a href="{{ route('marketplace.index') }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap {{ request()->routeIs('marketplace.*') ? 'text-indigo-600 bg-indigo-50/90 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                        <i class="fa-solid fa-bag-shopping me-1.5 {{ request()->routeIs('marketplace.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Jual Beli</span>
                    </a>

                    <!-- 5. Lelang -->
                    <a href="{{ route('auctions.index') }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap {{ request()->routeIs('auctions.*') ? 'text-indigo-600 bg-indigo-50/90 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                        <i class="fa-solid fa-gavel me-1.5 {{ request()->routeIs('auctions.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Lelang</span>
                    </a>

                    <!-- 6. Artikel (Tampil langsung di xl+, masuk dropdown di md & lg) -->
                    <a href="{{ route('articles.index') }}" class="hidden xl:inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap {{ request()->routeIs('articles.*') ? 'text-indigo-600 bg-indigo-50/90 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                        <i class="fa fa-newspaper me-1.5 {{ request()->routeIs('articles.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Artikel</span>
                    </a>

                    <!-- 7. Donasi (Tampil langsung di xl+, masuk dropdown di md & lg) -->
                    <a href="{{ route('donations.index') }}" class="hidden xl:inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap {{ request()->routeIs('donations.*') ? 'text-indigo-600 bg-indigo-50/90 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                        <i class="fa fa-hand-holding-heart me-1.5 {{ request()->routeIs('donations.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span>Donasi</span>
                    </a>

                    <!-- Dropdown Pintasan "Lainnya" untuk Layar Menengah -->
                    <div class="relative xl:hidden" @click.away="moreNav = false">
                        <button @click="moreNav = !moreNav" 
                                class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap {{ (request()->routeIs('articles.*') || request()->routeIs('donations.*')) ? 'text-indigo-600 bg-indigo-50/90 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                            <span>Lainnya</span>
                            <i class="fa fa-chevron-down ms-1 text-[9px] opacity-70"></i>
                        </button>
                        <div x-show="moreNav" x-cloak class="absolute left-0 mt-1.5 w-44 rounded-xl shadow-xl bg-white border border-slate-200 py-1 z-50">
                            <a href="{{ route('articles.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium hover:bg-slate-50 {{ request()->routeIs('articles.*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-700' }}">
                                <i class="fa fa-newspaper {{ request()->routeIs('articles.*') ? 'text-indigo-600' : 'text-slate-400' }} w-4"></i>
                                <span>Artikel</span>
                            </a>
                            <a href="{{ route('donations.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium hover:bg-slate-50 {{ request()->routeIs('donations.*') ? 'text-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-700' }}">
                                <i class="fa fa-hand-holding-heart {{ request()->routeIs('donations.*') ? 'text-indigo-600' : 'text-slate-400' }} w-4"></i>
                                <span>Donasi</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Controls: LIVE SEARCH, Notification, Profile (Tablet & Desktop: md:flex) -->
            <div class="hidden md:flex md:items-center md:ms-2 lg:ms-4 space-x-1.5 lg:space-x-2.5 shrink-0">
                
                <!-- LIVE SEARCH GLOBAL (Desktop) -->
                <div class="relative mr-1 lg:mr-2" x-data="{
                    query: '{{ request('q', '') }}',
                    results: { users: [], communities: [] },
                    isLoading: false,
                    showDropdown: false,
                    async fetchSearch() {
                        if(this.query.length < 2) {
                            this.results = { users: [], communities: [] };
                            this.showDropdown = false;
                            return;
                        }
                        this.isLoading = true;
                        try {
                            const res = await fetch(`/search/live?q=${encodeURIComponent(this.query)}`);
                            const data = await res.json();
                            this.results = data || { users: [], communities: [] };
                            this.showDropdown = true;
                        } catch(e) {
                            console.error('Error:', e);
                        }
                        this.isLoading = false;
                    }
                }" @click.away="showDropdown = false">
                    <form action="{{ route('search.index') }}" method="GET" class="relative flex items-center">
                        <input type="text" name="q" x-model="query" @input.debounce.500ms="fetchSearch()" @focus="if(query.length >= 2) showDropdown = true" placeholder="Cari..." 
                               class="w-48 lg:w-56 pl-9 pr-8 py-1.5 bg-slate-100 border border-transparent rounded-full text-xs outline-none focus:bg-white focus:border-indigo-300 focus:ring-4 focus:ring-indigo-600/10 transition-all duration-300 text-slate-700 font-medium placeholder-slate-400" autocomplete="off">
                        <i class="fa fa-search absolute left-3.5 text-slate-400 text-[10px]"></i>
                        <i x-show="isLoading" style="display: none;" class="fa fa-spinner fa-spin absolute right-3.5 text-indigo-500 text-[10px]" x-cloak></i>
                    </form>

                    <div x-show="showDropdown && (results.users?.length > 0 || results.communities?.length > 0)" style="display: none;" x-transition x-cloak class="absolute top-full mt-2 right-0 w-72 bg-white rounded-2xl shadow-xl border border-slate-200 py-2 z-50 overflow-hidden">
                        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                            <template x-if="results.users && results.users.length > 0">
                                <div>
                                    <div class="px-3 py-1.5 bg-slate-50 text-[9px] font-black text-slate-400 uppercase tracking-wider">Pengguna</div>
                                    <template x-for="user in results.users" :key="'ud'+user.id">
                                        <a :href="`/users/${user.username}`" class="flex items-center gap-2.5 p-2.5 hover:bg-slate-50 transition">
                                            <img :src="user.avatar_url" class="w-8 h-8 rounded-full object-cover border border-slate-200 shrink-0">
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-bold text-slate-800 truncate" x-text="user.name"></p>
                                                <p class="text-[10px] text-slate-400 font-mono truncate" x-text="`@${user.username}`"></p>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </template>

                            <template x-if="results.communities && results.communities.length > 0">
                                <div>
                                    <div class="px-3 py-1.5 bg-slate-50 text-[9px] font-black text-slate-400 uppercase tracking-wider">Komunitas</div>
                                    <template x-for="com in results.communities" :key="'cd'+com.id">
                                        <a :href="`/communities/${com.slug}`" class="flex items-center gap-2.5 p-2.5 hover:bg-slate-50 transition">
                                            <img :src="com.logo_url" class="w-8 h-8 rounded-xl object-cover border border-slate-200 shrink-0">
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-bold text-slate-800 truncate" x-text="com.name"></p>
                                                <p class="text-[9px] font-bold text-indigo-500 uppercase tracking-wider mt-0.5" x-text="com.category"></p>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </template>
                        </div>
                        <div class="p-2 border-t border-slate-100 bg-slate-50 text-center">
                            <button type="button" @click="$el.closest('form').submit()" class="text-[10px] font-bold text-indigo-600 hover:underline block w-full">
                                Lihat Semua Hasil &rarr;
                            </button>
                        </div>
                    </div>
                </div>

                @auth
                    <!-- Notification Bell Dropdown -->
                    <div class="relative" x-data="{ notifOpen: false }">
                        <button @click="notifOpen = !notifOpen" class="relative p-2 rounded-xl text-slate-600 hover:text-indigo-600 hover:bg-slate-100 transition focus:outline-none" title="Notifikasi">
                            <i class="fa-regular fa-bell text-base"></i>
                            @if(Auth::user()->unreadNotificationsCount() > 0)
                                <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white ring-2 ring-white animate-pulse">
                                    {{ Auth::user()->unreadNotificationsCount() > 9 ? '9+' : Auth::user()->unreadNotificationsCount() }}
                                </span>
                            @endif
                        </button>

                        <div x-show="notifOpen" @click.away="notifOpen = false" x-cloak class="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl shadow-2xl bg-white border border-slate-200 py-2 z-50 overflow-hidden">
                            <div class="px-4 py-2 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-extrabold text-xs text-slate-900">Notifikasi</span>
                                    @if(Auth::user()->unreadNotificationsCount() > 0)
                                        <span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-indigo-100 text-indigo-700">
                                            {{ Auth::user()->unreadNotificationsCount() }} baru
                                        </span>
                                    @endif
                                </div>
                                <a href="{{ route('notifications.index') }}" class="text-[10px] font-bold text-indigo-600 hover:underline">
                                    Lihat Semua
                                </a>
                            </div>

                            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                                @forelse(Auth::user()->appNotifications()->take(5)->get() as $n)
                                    <a href="{{ route('notifications.read', $n->id) }}" class="flex items-start gap-2.5 p-3 hover:bg-slate-50 transition {{ $n->is_read ? '' : 'bg-indigo-50/40' }}">
                                        <div class="relative flex-shrink-0 mt-0.5">
                                            @if($n->sender)
                                                <img src="{{ $n->sender->avatar_url }}" alt="{{ $n->sender->name }}" class="w-8 h-8 rounded-lg object-cover border border-slate-200">
                                            @else
                                                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">
                                                    <i class="fa {{ $n->icon }}"></i>
                                                </div>
                                            @endif
                                            <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-white shadow flex items-center justify-center text-[7px] {{ $n->icon_color }}">
                                                <i class="fa {{ $n->icon }}"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow min-w-0">
                                            <p class="text-xs text-slate-800 font-bold truncate">{{ $n->title }}</p>
                                            <p class="text-[11px] text-slate-500 line-clamp-2 leading-tight">{{ $n->message }}</p>
                                            <span class="text-[9px] text-slate-400 mt-1 block">{{ $n->created_at->diffForHumans() }}</span>
                                        </div>
                                    </a>
                                @empty
                                    <div class="py-6 text-center text-slate-400 text-xs">
                                        <i class="fa fa-bell-slash text-2xl text-slate-300 mb-1"></i>
                                        <p>Belum ada notifikasi.</p>
                                     </div>
                                @endforelse
                            </div>

                            <div class="p-2 border-t border-slate-100 bg-slate-50 text-center">
                                <a href="{{ route('notifications.index') }}" class="text-xs font-bold text-indigo-600 hover:underline block">
                                    Buka Pusat Notifikasi &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Action: Create Community / Donation -->
                    @if(auth()->user()->isCommunityAdmin() || auth()->user()->isSuperAdmin())
                        <div class="relative" x-data="{ dropdownOpen: false }">
                            <button @click="dropdownOpen = !dropdownOpen" class="inline-flex items-center gap-1.5 px-2.5 lg:px-3 py-1.5 text-xs font-bold rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white shadow-sm transition whitespace-nowrap">
                                <i class="fa-solid fa-gauge-high text-[11px]"></i>
                                <span>Kelola</span>
                                <svg class="ms-0.5 -me-0.5 h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                            </button>

                            <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak class="absolute right-0 mt-2 w-52 rounded-xl shadow-xl bg-white border border-slate-200 py-1.5 z-50">
                                <div class="px-3.5 py-1.5 border-b border-slate-100">
                                    <p class="text-[9px] text-slate-400 font-extrabold uppercase tracking-wider">Peran Anda</p>
                                    <span class="inline-block mt-0.5 px-2 py-0.2 text-[10px] rounded-full font-bold {{ auth()->user()->isSuperAdmin() ? 'bg-amber-100 text-amber-700' : 'bg-indigo-100 text-indigo-700' }}">
                                        {{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Admin Komunitas / EO' }}
                                    </span>
                                </div>
                                <a href="{{ route('admin.tickets.index') }}" class="block px-3.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50"><i class="fa fa-qrcode mr-2 text-indigo-500 text-[11px]"></i>Verifikasi Tiket</a>
                                <a href="{{ route('events.create') }}" class="block px-3.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50"><i class="fa fa-calendar-plus mr-2 text-indigo-600 text-[11px]"></i>Buat Event</a>
                                <a href="{{ route('donations.create') }}" class="block px-3.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50"><i class="fa fa-hand-holding-heart mr-2 text-emerald-500 text-[11px]"></i>Buka Donasi</a>
                                <a href="{{ route('articles.create') }}" class="block px-3.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50"><i class="fa fa-pen-nib mr-2 text-indigo-600 text-[11px]"></i>Tulis Artikel</a>

                                @if(auth()->user()->isSuperAdmin())
                                    <div class="border-t border-slate-100 my-1"></div>
                                    <p class="px-3.5 pt-1 text-[9px] text-slate-400 font-extrabold uppercase">Super Admin</p>
                                    <a href="{{ route('admin.moderation.articles') }}" class="block px-3.5 py-1.5 text-xs text-amber-600 hover:bg-slate-50"><i class="fa fa-shield-halved mr-2 text-[11px]"></i>Moderasi Artikel</a>
                                    <a href="{{ route('admin.moderation.posts') }}" class="block px-3.5 py-1.5 text-xs text-amber-600 hover:bg-slate-50"><i class="fa fa-comments mr-2 text-[11px]"></i>Moderasi Forum</a>
                                @endif
                            </div>
                        </div>
                    @else
                        <a href="{{ route('communities.create') }}" class="inline-flex items-center gap-1 px-2.5 lg:px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 hover:bg-slate-200 text-indigo-600 border border-indigo-500/20 transition whitespace-nowrap">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                            <span>Komunitas</span>
                        </a>
                    @endif

                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ userMenu: false }">
                        <button @click="userMenu = !userMenu" class="flex items-center space-x-1.5 lg:space-x-2 text-xs font-semibold text-slate-700 hover:text-indigo-600 focus:outline-none p-0.5 rounded-full hover:bg-slate-100 transition whitespace-nowrap">
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-7 h-7 rounded-full object-cover border border-indigo-500/40">
                            <span class="hidden xl:inline text-xs font-bold text-slate-800 max-w-[100px] 2xl:max-w-[140px] truncate">{{ Auth::user()->name }}</span>
                            <svg class="h-3.5 w-3.5 fill-current text-slate-400" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </button>

                        <div x-show="userMenu" @click.away="userMenu = false" x-cloak class="absolute right-0 mt-2 w-48 rounded-xl shadow-xl bg-white border border-slate-200 py-1 z-50">
                            <div class="px-3 py-1.5 border-b border-slate-100">
                                <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('profile.show') }}" class="block px-3 py-1.5 text-xs text-slate-700 hover:bg-slate-50"><i class="fa fa-user mr-2 text-indigo-500 text-[11px]"></i>Profil Saya</a>
                            <a href="{{ route('profile.tickets') }}" class="block px-3 py-1.5 text-xs text-slate-700 hover:bg-slate-50"><i class="fa fa-ticket mr-2 text-indigo-600 text-[11px]"></i>Tiket Saya</a>
                            <a href="{{ route('notifications.index') }}" class="block px-3 py-1.5 text-xs text-slate-700 hover:bg-slate-50"><i class="fa fa-bell mr-2 text-indigo-500 text-[11px]"></i>Notifikasi ({{ Auth::user()->unreadNotificationsCount() }})</a>
                            <a href="{{ route('settings.index') }}" class="block px-3 py-1.5 text-xs text-slate-700 hover:bg-slate-50"><i class="fa fa-gear mr-2 text-slate-400 text-[11px]"></i>Pengaturan</a>
                            <div class="border-t border-slate-100 my-1"></div>
                            
                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-3 py-1.5 text-xs text-rose-600 hover:bg-rose-50 font-semibold flex items-center">
                                    <i class="fa fa-sign-out-alt mr-2 text-xs"></i>
                                    Keluar (Logout)
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-2.5 lg:px-3.5 py-1.5 text-xs font-semibold text-slate-700 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition whitespace-nowrap">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="px-2.5 lg:px-3.5 py-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-lg shadow-sm transition whitespace-nowrap">
                        Daftar<span class="hidden sm:inline"> Gratis</span>
                    </a>
                @endauth
            </div>

            <!-- Mobile App Bar Controls (md:hidden) -->
            <div class="-me-2 flex items-center gap-1.5 md:hidden">
                @auth
                    <!-- Direct Notification Bell on Mobile -->
                    <a href="{{ route('notifications.index') }}" class="relative p-2 rounded-xl text-slate-600 hover:text-indigo-600 hover:bg-slate-100 transition" title="Notifikasi">
                        <i class="fa-regular fa-bell text-base"></i>
                        @if(Auth::user()->unreadNotificationsCount() > 0)
                            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[8px] font-black text-white ring-2 ring-white animate-pulse">
                                {{ Auth::user()->unreadNotificationsCount() > 9 ? '9+' : Auth::user()->unreadNotificationsCount() }}
                            </span>
                        @endif
                    </a>

                    <!-- User Avatar Link -->
                    <a href="{{ route('profile.show') }}" class="p-0.5 rounded-full hover:ring-2 hover:ring-indigo-400 transition" title="Profil Saya">
                        <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-7 h-7 rounded-full object-cover border border-slate-200">
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-2.5 py-1 text-xs font-bold text-slate-700 hover:text-indigo-600">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="px-2.5 py-1 text-xs font-bold text-white bg-indigo-600 rounded-lg shadow-sm">
                        Daftar
                    </a>
                @endauth

                <!-- Hamburger Drawer Toggle Button -->
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 focus:outline-none transition">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Mobile Menu (md:hidden) -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden md:hidden border-t border-slate-200 bg-white">
        
        <!-- LIVE SEARCH GLOBAL (Mobile) -->
        <div class="px-4 pt-4 pb-2" x-data="{
            query: '{{ request('q', '') }}',
            results: { users: [], communities: [] },
            isLoading: false,
            showDropdown: false,
            async fetchSearch() {
                if(this.query.length < 2) {
                    this.results = { users: [], communities: [] };
                    this.showDropdown = false;
                    return;
                }
                this.isLoading = true;
                try {
                    const res = await fetch(`/search/live?q=${encodeURIComponent(this.query)}`);
                    const data = await res.json();
                    this.results = data || { users: [], communities: [] };
                    this.showDropdown = true;
                } catch(e) {
                    console.error('Live Search Mobile Error:', e);
                }
                this.isLoading = false;
            }
        }" @click.away="showDropdown = false">
            <form action="{{ route('search.index') }}" method="GET" class="relative">
                <input type="text" name="q" x-model="query" @input.debounce.500ms="fetchSearch()" @focus="if(query.length >= 2) showDropdown = true" placeholder="Cari pengguna atau komunitas..." 
                       class="w-full pl-9 pr-8 py-2 bg-slate-100 border border-transparent rounded-xl text-xs outline-none focus:bg-white focus:border-indigo-300 focus:ring-4 focus:ring-indigo-600/10 transition-all duration-300 text-slate-700 font-medium placeholder-slate-400" autocomplete="off">
                <i class="fa fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400 text-xs"></i>
                <i x-show="isLoading" style="display: none;" class="fa fa-spinner fa-spin absolute right-3 top-1/2 transform -translate-y-1/2 text-indigo-500 text-[10px]" x-cloak></i>
            </form>

            <div x-show="showDropdown && (results.users?.length > 0 || results.communities?.length > 0)" style="display: none;" x-transition class="mt-2 bg-white rounded-xl border border-slate-200 py-1 shadow-sm overflow-hidden z-50 relative">
                <div class="max-h-60 overflow-y-auto divide-y divide-slate-100">
                    <template x-if="results.users && results.users.length > 0">
                        <div>
                            <div class="px-3 py-1.5 bg-slate-50 text-[9px] font-black text-slate-400 uppercase tracking-wider">Pengguna</div>
                            <template x-for="user in results.users" :key="'mu'+user.id">
                                <a :href="`/users/${user.username}`" class="flex items-center gap-2.5 p-2.5 hover:bg-slate-50 transition">
                                    <img :src="user.avatar_url" class="w-7 h-7 rounded-full object-cover border border-slate-200 shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-slate-800 truncate" x-text="user.name"></p>
                                        <p class="text-[9px] text-slate-400 font-mono truncate" x-text="`@${user.username}`"></p>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>
                    <template x-if="results.communities && results.communities.length > 0">
                        <div>
                            <div class="px-3 py-1.5 bg-slate-50 text-[9px] font-black text-slate-400 uppercase tracking-wider">Komunitas</div>
                            <template x-for="com in results.communities" :key="'mc'+com.id">
                                <a :href="`/communities/${com.slug}`" class="flex items-center gap-2.5 p-2.5 hover:bg-slate-50 transition">
                                    <img :src="com.logo_url" class="w-7 h-7 rounded-xl object-cover border border-slate-200 shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-slate-800 truncate" x-text="com.name"></p>
                                        <p class="text-[8px] font-bold text-indigo-500 uppercase tracking-wider mt-0.5" x-text="com.category"></p>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>
                </div>
                <div class="p-2 border-t border-slate-100 bg-slate-50 text-center">
                    <button type="button" @click="$el.closest('form').submit()" class="text-[10px] font-bold text-indigo-600 hover:underline block w-full">Lihat Semua Hasil &rarr;</button>
                </div>
            </div>
        </div>

        <div class="pt-1 pb-3 space-y-1 px-4">
            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <i class="fa fa-home w-5 text-indigo-500"></i> Dashboard
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                    <i class="fa fa-home w-5 text-indigo-500"></i> Beranda
                </x-responsive-nav-link>
            @endauth
            <x-responsive-nav-link :href="route('events.index')" :active="request()->routeIs('events.*')">
                <i class="fa fa-calendar w-5 {{ request()->routeIs('events.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i> Event
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('communities.index')" :active="request()->routeIs('communities.*')">
                <i class="fa fa-users w-5 {{ request()->routeIs('communities.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i> Komunitas
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('marketplace.index')" :active="request()->routeIs('marketplace.*')">
                <i class="fa-solid fa-bag-shopping w-5 {{ request()->routeIs('marketplace.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i> Jual Beli
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('auctions.index')" :active="request()->routeIs('auctions.*')">
                <i class="fa-solid fa-gavel w-5 {{ request()->routeIs('auctions.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i> Lelang
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('articles.index')" :active="request()->routeIs('articles.*')">
                <i class="fa fa-newspaper w-5 {{ request()->routeIs('articles.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i> Artikel
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('donations.index')" :active="request()->routeIs('donations.*')">
                <i class="fa fa-hand-holding-heart w-5 {{ request()->routeIs('donations.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i> Donasi
            </x-responsive-nav-link>
        </div>

        @auth
            <div class="pt-4 pb-3 border-t border-slate-200 px-4">
                <div class="flex items-center gap-3 mb-3">
                    <img src="{{ Auth::user()->avatar_url }}" class="w-9 h-9 rounded-full object-cover">
                    <div>
                        <div class="font-bold text-sm text-slate-800">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-slate-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="space-y-1">
                    <x-responsive-nav-link :href="route('profile.show')"><i class="fa fa-user w-5"></i> Profil Saya</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('profile.tickets')"><i class="fa fa-ticket w-5"></i> Tiket Saya</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('notifications.index')"><i class="fa fa-bell w-5"></i> Notifikasi</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('settings.index')"><i class="fa fa-gear w-5"></i> Pengaturan</x-responsive-nav-link>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-rose-600 font-bold">
                            <i class="fa fa-sign-out-alt w-5"></i> Keluar
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-3 pb-4 border-t border-slate-200 px-4 space-y-2">
                <a href="{{ route('login') }}" class="block w-full py-2 text-center text-xs font-bold text-slate-700 bg-slate-100 rounded-xl">Masuk</a>
                <a href="{{ route('register') }}" class="block w-full py-2 text-center text-xs font-bold text-white bg-indigo-600 rounded-xl">Daftar Akun Baru</a>
            </div>
        @endauth
    </div>
</nav>