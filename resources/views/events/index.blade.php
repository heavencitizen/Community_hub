<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        <!-- Header & Action -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900">Katalog Event Komunitas & Amal</h1>
                <p class="text-xs text-slate-500">Temukan fun run, sunmori vespa klasik, open trip selam, dan konser amal donasi lokal.</p>
            </div>
            @auth
                @if(auth()->user()->isCommunityAdmin() || auth()->user()->isSuperAdmin())
                    <a href="{{ route('events.create') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm transition self-start sm:self-auto">
                        + Buat Event Baru
                    </a>
                @endif
            @endauth
        </div>

        <!-- Filter & Search Bar (Neat, Balanced, No Native Overflow) -->
        <div class="p-3 sm:p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
            <form x-ref="filterForm" action="{{ route('events.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-center">
                
                <!-- 1. Search Field (12 cols on mobile, 6 cols on sm) -->
                <div class="sm:col-span-6 relative flex items-center">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa fa-search text-xs"></i>
                    </div>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Cari nama event, lari, vespa, konser, atau lokasi..." 
                        class="w-full pl-9 pr-8 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 text-xs focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 py-2.5 transition"
                    >
                    @if(request()->filled('search'))
                        <a href="{{ route('events.index', request()->except('search', 'page')) }}" class="absolute right-2.5 p-1 text-slate-400 hover:text-slate-600 text-xs rounded transition" title="Hapus teks pencarian">
                            <i class="fa fa-times"></i>
                        </a>
                    @endif
                </div>

                <!-- 2. Dropdown + Buttons Wrapper (Mobile: flex row side-by-side; sm: 6 cols grid) -->
                <div class="sm:col-span-6 flex items-center gap-2">
                    
                    <!-- Custom Styled Dropdown (Never Offside, Pure CSS/Alpine Container Bounds) -->
                    @php
                        $selectedPrice = request('price_type', '');
                    @endphp
                    <div 
                        x-data="{
                            open: false,
                            selected: '{{ $selectedPrice }}',
                            select(val) {
                                this.selected = val;
                                this.open = false;
                                this.$nextTick(() => {
                                    $refs.filterForm.submit();
                                });
                            }
                        }" 
                        class="relative flex-1"
                    >
                        <input type="hidden" name="price_type" :value="selected">

                        <!-- Trigger Button -->
                        <button 
                            type="button" 
                            @click="open = !open" 
                            @click.away="open = false"
                            class="w-full flex items-center justify-between gap-2 px-3 py-2.5 rounded-xl bg-slate-50 hover:bg-white border border-slate-200 text-slate-800 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm"
                        >
                            <span class="flex items-center gap-2 truncate">
                                <span x-show="selected === ''" class="flex items-center gap-1.5 text-slate-700 {{ $selectedPrice !== '' ? 'hidden' : '' }}">
                                    <i class="fa fa-ticket-alt text-indigo-500 text-[11px]"></i>
                                    <span>Semua Tiket</span>
                                </span>
                                <span x-show="selected === 'free'" class="flex items-center gap-1.5 text-emerald-700 font-bold {{ $selectedPrice !== 'free' ? 'hidden' : '' }}">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span>Gratis RSVP</span>
                                </span>
                                <span x-show="selected === 'paid'" class="flex items-center gap-1.5 text-indigo-700 font-bold {{ $selectedPrice !== 'paid' ? 'hidden' : '' }}">
                                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                    <span>Berbayar / Donasi</span>
                                </span>
                            </span>
                            <i class="fa fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 flex-shrink-0" :class="open ? 'rotate-180 text-indigo-600' : ''"></i>
                        </button>

                        <!-- Dropdown Menu (Strictly within container, Never Offside) -->
                        <div 
                            x-show="open" 
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1 scale-98"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 -translate-y-1 scale-98"
                            class="absolute left-0 right-0 top-full mt-1.5 w-full bg-white rounded-xl border border-slate-200 shadow-xl py-1 z-30 overflow-hidden divide-y divide-slate-50"
                            style="display: none;"
                        >
                            <!-- Option 1: Semua Tiket -->
                            <button 
                                type="button" 
                                @click="select('')"
                                class="w-full flex items-center justify-between px-3 py-2.5 text-xs text-left transition hover:bg-slate-50"
                                :class="selected === '' ? 'bg-indigo-50/70 text-indigo-700 font-bold' : 'text-slate-700'"
                            >
                                <span class="flex items-center gap-2">
                                    <i class="fa fa-ticket-alt text-indigo-500 text-[11px] w-4 text-center"></i>
                                    <span>Semua Tiket</span>
                                </span>
                                <i x-show="selected === ''" class="fa fa-check text-indigo-600 text-xs"></i>
                            </button>

                            <!-- Option 2: Gratis RSVP -->
                            <button 
                                type="button" 
                                @click="select('free')"
                                class="w-full flex items-center justify-between px-3 py-2.5 text-xs text-left transition hover:bg-slate-50"
                                :class="selected === 'free' ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-700'"
                            >
                                <span class="flex items-center gap-2">
                                    <span class="w-4 flex items-center justify-center">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    </span>
                                    <span>Gratis RSVP</span>
                                </span>
                                <i x-show="selected === 'free'" class="fa fa-check text-emerald-600 text-xs"></i>
                            </button>

                            <!-- Option 3: Berbayar / Donasi -->
                            <button 
                                type="button" 
                                @click="select('paid')"
                                class="w-full flex items-center justify-between px-3 py-2.5 text-xs text-left transition hover:bg-slate-50"
                                :class="selected === 'paid' ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700'"
                            >
                                <span class="flex items-center gap-2">
                                    <span class="w-4 flex items-center justify-center">
                                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                    </span>
                                    <span>Berbayar / Donasi</span>
                                </span>
                                <i x-show="selected === 'paid'" class="fa fa-check text-indigo-600 text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Filter Button -->
                    <button 
                        type="submit" 
                        class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm flex-shrink-0"
                    >
                        <i class="fa fa-filter text-[10px]"></i>
                        <span>Filter</span>
                    </button>

                    <!-- Reset Button -->
                    @if(request()->hasAny(['search', 'price_type', 'community_id']))
                        <a 
                            href="{{ route('events.index') }}" 
                            class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 text-xs font-semibold transition flex items-center justify-center border border-slate-200 flex-shrink-0"
                            title="Reset Filter"
                        >
                            <i class="fa fa-rotate-left text-[11px]"></i>
                        </a>
                    @endif
                </div>

            </form>
        </div>

        <!-- Event Cards Grid -->
        @if($events->isEmpty())
            <div class="p-8 rounded-2xl bg-white border border-slate-200 text-center text-slate-500">
                <p class="text-sm font-bold text-slate-800">Tidak ada event yang sesuai kriteria pencarian.</p>
                <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($events as $event)
                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:border-indigo-200 hover:shadow-md transition flex flex-col justify-between">
                        <div class="h-36 bg-slate-100 relative overflow-hidden">
                            <img src="{{ $event->banner_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                            <div class="absolute top-2 right-2 px-2 py-0.5 rounded-full text-[9px] font-black bg-slate-900/85 backdrop-blur text-white shadow">
                                {{ $event->price == 0 ? 'Gratis' : 'Rp' . number_format($event->price, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="p-4 flex-grow flex flex-col justify-between space-y-3">
                            <div class="space-y-2">
                                <span class="text-[9px] font-extrabold text-indigo-600 uppercase tracking-wider">{{ $event->community->name ?? 'Komunitas' }}</span>
                                <h3 class="font-bold text-slate-800 text-xs sm:text-sm line-clamp-1 hover:text-indigo-600 transition">{{ $event->title }}</h3>
                                <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">{{ Str::limit(strip_tags($event->description), 90) }}</p>

                                <div class="space-y-1.5 pt-2 border-t border-slate-100 text-[11px] text-slate-600">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa fa-calendar-day text-indigo-600 w-3 text-[10px]"></i>
                                        <span>{{ $event->event_date->format('d M Y, H:i') }} WIB</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa fa-map-marker-alt text-slate-400 w-3 text-[10px]"></i>
                                        <span class="line-clamp-1">{{ $event->location }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa fa-user-friends text-slate-400 w-3 text-[10px]"></i>
                                        <span>Sisa Kuota: <strong class="text-slate-800">{{ $event->remainingQuota() }}</strong> / {{ $event->quota }}</span>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('events.show', $event->slug) }}" class="w-full text-center py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm transition">
                                Detail & Pesan Tiket &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 text-center">
                {{ $events->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
