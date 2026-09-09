<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 sm:py-6 space-y-4 sm:space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Direktori Komunitas Hobi</h1>
                <p class="text-xs text-slate-500">Temukan teman sehobi, ikuti forum, jual beli merchandise, dan lelang.</p>
            </div>
            @auth
                <div class="self-start sm:self-auto">
                    <a href="{{ route('communities.create') }}" class="inline-flex items-center px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm transition active:scale-95">
                        <i class="fa fa-plus me-1.5 text-[10px]"></i>
                        <span>Bentuk Komunitas</span>
                    </a>
                </div>
            @endauth
        </div>

        <!-- Responsive Category Navigation (Horizontal Swipeable Chips on Mobile, Grid on Tablet/Desktop) -->
        <div class="bg-white p-3 sm:p-4 rounded-2xl border border-slate-200 shadow-sm space-y-2.5 sm:space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Kategori Komunitas</span>
                    <span class="text-[10px] text-slate-400 sm:hidden">• Geser</span>
                </div>
                @if(request('category'))
                    <a href="{{ route('communities.index') }}" class="text-[11px] font-bold text-indigo-600 hover:underline">Reset Filter</a>
                @endif
            </div>

            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 sm:pb-0 sm:grid sm:grid-cols-4 md:grid-cols-7 sm:gap-2">
                <!-- All -->
                <a href="{{ route('communities.index', array_merge(request()->except('category', 'page'), ['category' => 'all'])) }}" class="flex-shrink-0 inline-flex items-center gap-2 px-3 py-2 rounded-xl border transition-all text-xs font-bold whitespace-nowrap sm:flex-col sm:p-2.5 sm:text-center sm:gap-1 {{ (!request('category') || request('category') === 'all') ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-700' }}">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-xs sm:text-sm {{ (!request('category') || request('category') === 'all') ? 'bg-white/20 text-white' : 'bg-white text-indigo-600 shadow-sm' }}">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <span class="text-xs sm:text-[11px] font-bold">Semua</span>
                </a>

                <!-- Olahraga -->
                <a href="{{ route('communities.index', array_merge(request()->except('category', 'page'), ['category' => 'olahraga'])) }}" class="flex-shrink-0 inline-flex items-center gap-2 px-3 py-2 rounded-xl border transition-all text-xs font-bold whitespace-nowrap sm:flex-col sm:p-2.5 sm:text-center sm:gap-1 {{ request('category') === 'olahraga' ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-700' }}">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-xs sm:text-sm {{ request('category') === 'olahraga' ? 'bg-white/20 text-white' : 'bg-white text-indigo-600 shadow-sm' }}">
                        <i class="fa-solid fa-person-running"></i>
                    </div>
                    <span class="text-xs sm:text-[11px] font-bold">Olahraga</span>
                </a>

                <!-- Otomotif -->
                <a href="{{ route('communities.index', array_merge(request()->except('category', 'page'), ['category' => 'otomotif'])) }}" class="flex-shrink-0 inline-flex items-center gap-2 px-3 py-2 rounded-xl border transition-all text-xs font-bold whitespace-nowrap sm:flex-col sm:p-2.5 sm:text-center sm:gap-1 {{ request('category') === 'otomotif' ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-700' }}">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-xs sm:text-sm {{ request('category') === 'otomotif' ? 'bg-white/20 text-white' : 'bg-white text-indigo-600 shadow-sm' }}">
                        <i class="fa-solid fa-motorcycle"></i>
                    </div>
                    <span class="text-xs sm:text-[11px] font-bold">Otomotif</span>
                </a>

                <!-- Kesehatan -->
                <a href="{{ route('communities.index', array_merge(request()->except('category', 'page'), ['category' => 'kesehatan'])) }}" class="flex-shrink-0 inline-flex items-center gap-2 px-3 py-2 rounded-xl border transition-all text-xs font-bold whitespace-nowrap sm:flex-col sm:p-2.5 sm:text-center sm:gap-1 {{ request('category') === 'kesehatan' ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-700' }}">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-xs sm:text-sm {{ request('category') === 'kesehatan' ? 'bg-white/20 text-white' : 'bg-white text-indigo-600 shadow-sm' }}">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>
                    <span class="text-xs sm:text-[11px] font-bold">Kesehatan</span>
                </a>

                <!-- Kuliner -->
                <a href="{{ route('communities.index', array_merge(request()->except('category', 'page'), ['category' => 'kuliner'])) }}" class="flex-shrink-0 inline-flex items-center gap-2 px-3 py-2 rounded-xl border transition-all text-xs font-bold whitespace-nowrap sm:flex-col sm:p-2.5 sm:text-center sm:gap-1 {{ request('category') === 'kuliner' ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-700' }}">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-xs sm:text-sm {{ request('category') === 'kuliner' ? 'bg-white/20 text-white' : 'bg-white text-indigo-600 shadow-sm' }}">
                        <i class="fa-solid fa-mug-hot"></i>
                    </div>
                    <span class="text-xs sm:text-[11px] font-bold">Kuliner</span>
                </a>

                <!-- Rumah Tangga -->
                <a href="{{ route('communities.index', array_merge(request()->except('category', 'page'), ['category' => 'rumah_tangga'])) }}" class="flex-shrink-0 inline-flex items-center gap-2 px-3 py-2 rounded-xl border transition-all text-xs font-bold whitespace-nowrap sm:flex-col sm:p-2.5 sm:text-center sm:gap-1 {{ request('category') === 'rumah_tangga' ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-700' }}">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-xs sm:text-sm {{ request('category') === 'rumah_tangga' ? 'bg-white/20 text-white' : 'bg-white text-indigo-600 shadow-sm' }}">
                        <i class="fa-solid fa-house-chimney-window"></i>
                    </div>
                    <span class="text-xs sm:text-[11px] font-bold">Rumah</span>
                </a>

                <!-- Seni & Kreatif -->
                <a href="{{ route('communities.index', array_merge(request()->except('category', 'page'), ['category' => 'seni'])) }}" class="flex-shrink-0 inline-flex items-center gap-2 px-3 py-2 rounded-xl border transition-all text-xs font-bold whitespace-nowrap sm:flex-col sm:p-2.5 sm:text-center sm:gap-1 {{ request('category') === 'seni' ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-700' }}">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-xs sm:text-sm {{ request('category') === 'seni' ? 'bg-white/20 text-white' : 'bg-white text-indigo-600 shadow-sm' }}">
                        <i class="fa-solid fa-palette"></i>
                    </div>
                    <span class="text-xs sm:text-[11px] font-bold">Seni</span>
                </a>
            </div>
        </div>

        <!-- 2-Column Main Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Left: Community List & Search -->
            <div class="lg:col-span-8 space-y-4">
                <!-- Search Bar -->
                <div class="p-2 bg-white rounded-xl border border-slate-200 shadow-sm">
                    <form action="{{ route('communities.index') }}" method="GET" class="flex gap-2">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama komunitas..." class="flex-grow rounded-lg bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 text-xs focus:ring-indigo-500 py-2 px-3">
                        <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-1.5">
                            <i class="fa fa-search text-[10px]"></i>
                            <span>Cari</span>
                        </button>
                    </form>
                </div>

                <!-- Communities Grid Cards -->
                @if($communities->isEmpty())
                    <div class="p-8 rounded-2xl bg-white border border-slate-200 text-center text-slate-500">
                        <i class="fa fa-users-slash text-2xl text-slate-300 mb-1"></i>
                        <p class="text-xs font-bold text-slate-800">Tidak ada komunitas yang sesuai filter.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($communities as $community)
                            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:border-indigo-200 hover:shadow-md transition flex flex-col justify-between">
                                <div class="h-28 bg-gradient-to-r from-slate-900 to-indigo-900 relative">
                                    <img src="{{ $community->banner_url }}" alt="{{ $community->name }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                    <div class="absolute -bottom-4 left-4 w-12 h-12 rounded-xl bg-indigo-600 border-2 border-white overflow-hidden flex items-center justify-center font-black text-white text-base shadow-md flex-shrink-0">
                                        <img src="{{ $community->logo_url }}" alt="{{ $community->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="absolute top-2 right-2 px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-white/90 backdrop-blur text-slate-800">
                                        {{ $community->category ?? 'Hobi' }}
                                    </div>
                                </div>

                                <div class="pt-6 p-4 flex-grow flex flex-col justify-between space-y-3">
                                    <div class="space-y-1">
                                        <h3 class="font-bold text-slate-800 text-sm hover:text-indigo-600 transition line-clamp-1">
                                            <a href="{{ route('communities.show', $community->slug) }}">{{ $community->name }}</a>
                                        </h3>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.2 rounded-full text-[9px] font-bold bg-indigo-50 text-indigo-600">
                                                <i class="fa fa-users text-[8px]"></i> {{ $community->members_count }} Anggota
                                            </span>
                                            <span class="text-[10px] text-slate-400">Ketua: {{ $community->owner->name ?? 'Admin' }}</span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">{{ $community->description }}</p>
                                    </div>

                                    <a href="{{ route('communities.show', $community->slug) }}" class="w-full text-center py-1.5 rounded-lg bg-slate-100 hover:bg-indigo-600 text-slate-700 hover:text-white text-xs font-bold transition">
                                        Masuk Komunitas &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $communities->links() }}
                    </div>
                @endif
            </div>

            <!-- Right Sidebar -->
            <div class="lg:col-span-4 space-y-4 sticky top-20">
                <!-- Widget 1: Agenda Event -->
                <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-3 shadow-sm text-xs">
                    <div class="flex items-center justify-between">
                        <h3 class="text-[11px] font-extrabold text-indigo-600 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa fa-calendar-alt"></i> Event Terdekat
                        </h3>
                        <a href="{{ route('events.index') }}" class="text-[10px] text-indigo-600 hover:underline font-bold">Semua &rarr;</a>
                    </div>

                    <div class="space-y-2">
                        @foreach($upcomingEvents as $ev)
                            <a href="{{ route('events.show', $ev->slug) }}" class="block p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/60 transition group space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded uppercase {{ $ev->price == 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-50 text-indigo-700' }}">
                                        {{ $ev->price == 0 ? 'Gratis' : 'Rp' . number_format($ev->price, 0, ',', '.') }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">{{ $ev->event_date->format('d M') }}</span>
                                </div>
                                <h4 class="font-bold text-slate-800 text-xs group-hover:text-indigo-600 transition line-clamp-1">{{ $ev->title }}</h4>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Widget 2: Komunitas Terpopuler -->
                <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-3 shadow-sm text-xs">
                    <h3 class="text-[11px] font-extrabold text-indigo-600 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa fa-users"></i> Komunitas Terpopuler
                    </h3>
                    <div class="space-y-2">
                        @foreach($popularCommunities as $pop)
                            <a href="{{ route('communities.show', $pop->slug) }}" class="flex items-center justify-between p-2 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/60 transition group">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $pop->logo_url }}" alt="{{ $pop->name }}" class="w-8 h-8 rounded-lg object-cover">
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-xs group-hover:text-indigo-600 transition line-clamp-1">{{ $pop->name }}</h4>
                                        <p class="text-[9px] text-slate-400">{{ $pop->members_count }} Anggota</p>
                                    </div>
                                </div>
                                <span class="text-xs text-indigo-600 font-bold">&rarr;</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
