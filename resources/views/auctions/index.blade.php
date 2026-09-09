<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        
        <!-- Header & Statistics Banner -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
            <div>
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600 text-base">
                        <i class="fa fa-gavel"></i>
                    </span>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900">Arena Lelang Komunitas</h1>
                        <p class="text-xs text-slate-500 mt-0.5">Lelang barang hobi langka, merchandise resmi, dan koleksi unik komunitas Sumatera Barat.</p>
                    </div>
                </div>
            </div>

            <!-- Quick Counter Stats -->
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <div class="px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-bold flex items-center gap-1.5 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>{{ $stats['total_live'] }} Live Sesi</span>
                </div>
                <div class="px-3 py-1.5 rounded-xl bg-sky-50 border border-sky-100 text-sky-700 text-xs font-bold flex items-center gap-1.5 shadow-sm">
                    <i class="fa fa-calendar-clock text-[11px]"></i>
                    <span>{{ $stats['total_scheduled'] }} Terjadwal</span>
                </div>
                <div class="px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 text-xs font-bold flex items-center gap-1.5 shadow-sm">
                    <i class="fa fa-hand-holding-dollar text-[11px] text-indigo-600"></i>
                    <span>{{ $stats['total_bids'] }} Tawaran</span>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar (Modern Responsive Pills, Integrated Search) -->
        <div class="p-3 sm:p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
            <form action="{{ route('auctions.index') }}" method="GET" class="relative flex items-center">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                @if(request('community_id'))
                    <input type="hidden" name="community_id" value="{{ request('community_id') }}">
                @endif
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa fa-search text-xs"></i>
                    </div>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Cari barang hobi, diecast, helm bogo, diving mask, jersey..." 
                        class="w-full pl-9 pr-24 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 text-xs focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 py-2.5 transition"
                    >
                    <div class="absolute right-1.5 inset-y-1.5 flex items-center gap-1">
                        @if(request()->filled('search'))
                            <a href="{{ route('auctions.index', request()->except('search', 'page')) }}" class="p-1.5 text-slate-400 hover:text-slate-600 text-xs rounded-lg hover:bg-slate-100 transition" title="Hapus pencarian">
                                <i class="fa fa-times"></i>
                            </a>
                        @endif
                        <button type="submit" class="h-full px-3.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition shadow-sm flex items-center gap-1.5">
                            <i class="fa fa-search text-[10px]"></i>
                            <span>Cari</span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Status Tabs (Horizontal Swipeable on Mobile) -->
            <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100">
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5 w-full sm:w-auto">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mr-1 flex-shrink-0">
                        Status:
                    </span>

                    <!-- Semua -->
                    <a href="{{ route('auctions.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}"
                       class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ (!request('status') || request('status') === 'all') ? 'bg-indigo-600 text-white shadow-sm ring-2 ring-indigo-600/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        <span>Semua</span>
                    </a>

                    <!-- Sedang Live -->
                    <a href="{{ route('auctions.index', array_merge(request()->except('status', 'page'), ['status' => 'live'])) }}"
                       class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'live' ? 'bg-emerald-600 text-white shadow-sm ring-2 ring-emerald-600/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        <span class="w-2 h-2 rounded-full {{ request('status') === 'live' ? 'bg-white animate-pulse' : 'bg-emerald-500' }}"></span>
                        <span>🔴 Sedang Live</span>
                    </a>

                    <!-- Terjadwal -->
                    <a href="{{ route('auctions.index', array_merge(request()->except('status', 'page'), ['status' => 'scheduled'])) }}"
                       class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'scheduled' ? 'bg-sky-600 text-white shadow-sm ring-2 ring-sky-600/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        <span class="w-2 h-2 rounded-full {{ request('status') === 'scheduled' ? 'bg-white' : 'bg-sky-500' }}"></span>
                        <span>📅 Terjadwal</span>
                    </a>

                    <!-- Menunggu Pembayaran -->
                    <a href="{{ route('auctions.index', array_merge(request()->except('status', 'page'), ['status' => 'closed'])) }}"
                       class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'closed' ? 'bg-amber-600 text-white shadow-sm ring-2 ring-amber-600/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        <span class="w-2 h-2 rounded-full {{ request('status') === 'closed' ? 'bg-white' : 'bg-amber-500' }}"></span>
                        <span>⏳ Menunggu Bayar</span>
                    </a>

                    <!-- Selesai / Lunas -->
                    <a href="{{ route('auctions.index', array_merge(request()->except('status', 'page'), ['status' => 'completed'])) }}"
                       class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'completed' ? 'bg-indigo-600 text-white shadow-sm ring-2 ring-indigo-600/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        <span class="w-2 h-2 rounded-full {{ request('status') === 'completed' ? 'bg-white' : 'bg-indigo-500' }}"></span>
                        <span>✅ Selesai</span>
                    </a>
                </div>

                @if(request()->hasAny(['search', 'status', 'community_id']) && (request('status') !== 'all' || request()->filled('search') || request()->filled('community_id')))
                    <a href="{{ route('auctions.index') }}" class="flex-shrink-0 text-[11px] font-bold text-rose-600 hover:text-rose-700 hover:underline flex items-center gap-1 py-1 px-2 rounded-lg hover:bg-rose-50 transition">
                        <i class="fa fa-rotate-left text-[10px]"></i>
                        <span class="hidden sm:inline">Reset Filter</span>
                        <span class="sm:hidden">Reset</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Auctions Grid -->
        @if($auctions->isEmpty())
            <div class="p-8 sm:p-12 rounded-2xl bg-white border border-slate-200 text-center text-slate-500 space-y-3 shadow-sm">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl">
                    <i class="fa fa-gavel"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Tidak ada lelang yang sesuai kriteria pencarian</h3>
                <p class="text-xs text-slate-400 max-w-md mx-auto">Coba gunakan kata kunci pencarian yang lain atau periksa kembali di filter status sesi.</p>
                <a href="{{ route('auctions.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-sm hover:bg-indigo-500 transition">
                    <i class="fa fa-rotate-left text-[10px]"></i>
                    <span>Tampilkan Semua Lelang</span>
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($auctions as $auction)
                    @php
                        $isLive = $auction->isActive();
                        $isScheduled = $auction->isScheduled();
                        $isAwaiting = $auction->isAwaitingPayment();
                        $isCompleted = $auction->isPaid();
                        $isWanprestasi = $auction->isWanprestasi();
                        $isCancelled = $auction->status === 'cancelled';
                    @endphp
                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:border-indigo-200 hover:shadow-md transition flex flex-col justify-between group">
                        
                        <!-- Top Image & Badges -->
                        <div class="h-44 bg-slate-100 relative overflow-hidden">
                            <img src="{{ $auction->image_url }}" alt="{{ $auction->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            
                            <!-- Status Badge -->
                            <div class="absolute top-2.5 left-2.5">
                                @if($isLive)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500 text-white shadow flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                                        <span>LIVE LELANG</span>
                                    </span>
                                @elseif($isScheduled)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-sky-600 text-white shadow flex items-center gap-1.5">
                                        <i class="fa fa-calendar-clock text-[9px]"></i>
                                        <span>TERJADWAL</span>
                                    </span>
                                @elseif($isAwaiting)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-500 text-white shadow flex items-center gap-1">
                                        <i class="fa fa-hourglass-half text-[9px]"></i>
                                        <span>MENUNGGU BAYAR</span>
                                    </span>
                                @elseif($isCompleted)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-indigo-600 text-white shadow flex items-center gap-1">
                                        <i class="fa fa-check text-[9px]"></i>
                                        <span>SELESAI & LUNAS</span>
                                    </span>
                                @elseif($isWanprestasi)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-600 text-white shadow flex items-center gap-1">
                                        <i class="fa fa-triangle-exclamation text-[9px]"></i>
                                        <span>WANPRESTASI</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-700 text-white shadow">
                                        DIBATALKAN
                                    </span>
                                @endif
                            </div>

                            <!-- Total Bids Count Badge -->
                            <div class="absolute top-2.5 right-2.5 px-2 py-1 rounded-xl text-[10px] font-bold bg-slate-900/80 backdrop-blur text-white shadow flex items-center gap-1">
                                <i class="fa fa-hand-holding-dollar text-indigo-300 text-[10px]"></i>
                                <span>{{ $auction->bids->count() }} Tawaran</span>
                            </div>

                            <!-- Community Watermark Pill -->
                            <div class="absolute bottom-2.5 left-2.5">
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-slate-900/75 backdrop-blur text-white truncate max-w-[200px] block">
                                    {{ $auction->community->name }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 flex-grow flex flex-col justify-between space-y-3.5">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-1">
                                    @if($auction->auction_code)
                                        <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 font-mono text-[9px] font-bold">
                                             {{ $auction->auction_code }}
                                        </span>
                                    @endif
                                    @if($auction->anti_sniping)
                                        <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[9px] font-bold border border-emerald-200" title="Anti-Sniping Aktif">
                                            <i class="fa fa-shield-halved text-[8px]"></i> Anti-Sniping
                                        </span>
                                    @endif
                                </div>

                                <h3 class="font-black text-slate-800 text-sm line-clamp-1 group-hover:text-indigo-600 transition">
                                    {{ $auction->title }}
                                </h3>
                                <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">
                                    {{ $auction->description ?: 'Barang koleksi eksklusif persembahan komunitas.' }}
                                </p>

                                <!-- Price Matrix -->
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-[9px] font-bold text-indigo-600 uppercase tracking-wider block">
                                            {{ $isLive ? 'Tawaran Tertinggi' : ($isScheduled ? 'Nilai Pembukaan' : 'Pokok Lelang') }}
                                        </span>
                                        <p class="text-base sm:text-lg font-black text-slate-900">
                                            Rp{{ number_format($auction->current_price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="text-right text-[10px] text-slate-500">
                                        <div>Awal: <strong>Rp{{ number_format($auction->starting_price, 0, ',', '.') }}</strong></div>
                                        <div>Kelipatan: <strong>+Rp{{ number_format($auction->bid_increment, 0, ',', '.') }}</strong></div>
                                    </div>
                                </div>

                                <!-- Countdown / Winner Indicator -->
                                @if($isLive && $auction->end_time)
                                    <div 
                                        x-data="{
                                            endTime: new Date('{{ $auction->end_time->toISOString() }}').getTime(),
                                            timeStr: '',
                                            update() {
                                                const now = new Date().getTime();
                                                const diff = this.endTime - now;
                                                if (diff <= 0) {
                                                    this.timeStr = 'Waktu Habis';
                                                    return;
                                                }
                                                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                                                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                                const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                                                
                                                if (days > 0) {
                                                    this.timeStr = `${days}h ${hours}j ${minutes}m`;
                                                } else {
                                                    this.timeStr = `${hours}j ${minutes}m ${seconds}s`;
                                                }
                                            },
                                            init() {
                                                this.update();
                                                setInterval(() => this.update(), 1000);
                                            }
                                        }"
                                        class="flex items-center justify-between px-2.5 py-1.5 rounded-lg bg-slate-50 text-[11px] text-slate-600 border border-slate-100 font-medium"
                                    >
                                        <span class="flex items-center gap-1.5 text-slate-500 text-[10px]">
                                            <i class="fa fa-clock text-indigo-600"></i> Sisa Waktu:
                                        </span>
                                        <strong x-text="timeStr" class="text-indigo-700 font-mono font-black text-xs">Menghitung...</strong>
                                    </div>
                                @elseif($isScheduled && $auction->start_time)
                                    <div 
                                        x-data="{
                                            startTime: new Date('{{ $auction->start_time->toISOString() }}').getTime(),
                                            timeStr: '',
                                            update() {
                                                const now = new Date().getTime();
                                                const diff = this.startTime - now;
                                                if (diff <= 0) {
                                                    this.timeStr = 'Mulai Sekarang';
                                                    return;
                                                }
                                                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                                                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                                const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                                                
                                                if (days > 0) {
                                                    this.timeStr = `${days}h ${hours}j ${minutes}m`;
                                                } else {
                                                    this.timeStr = `${hours}j ${minutes}m ${seconds}s`;
                                                }
                                            },
                                            init() {
                                                this.update();
                                                setInterval(() => this.update(), 1000);
                                            }
                                        }"
                                        class="flex items-center justify-between px-2.5 py-1.5 rounded-lg bg-sky-50 text-[11px] text-sky-800 border border-sky-100 font-medium"
                                    >
                                        <span class="flex items-center gap-1.5 text-sky-600 text-[10px]">
                                            <i class="fa fa-calendar-clock text-sky-600"></i> Dimulai:
                                        </span>
                                        <strong x-text="timeStr" class="text-sky-800 font-mono font-black text-xs">Menghitung...</strong>
                                    </div>
                                @elseif($auction->winner)
                                    <div class="flex items-center justify-between px-2.5 py-1.5 rounded-lg bg-amber-50/60 text-[11px] border border-amber-100">
                                        <span class="text-amber-800 text-[10px] font-bold flex items-center gap-1">
                                            <i class="fa fa-trophy text-amber-500"></i> Pemenang:
                                        </span>
                                        <span class="font-bold text-slate-800 truncate max-w-[150px]">
                                            {{ $auction->winner->name }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Button -->
                            <a 
                                href="{{ route('communities.show', $auction->community->slug) }}?tab=auctions#auction-{{ $auction->id }}" 
                                class="w-full text-center py-2.5 rounded-xl font-bold text-xs transition shadow-sm flex items-center justify-center gap-1.5 {{ $isLive ? 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-indigo-200' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}"
                            >
                                @if($isLive)
                                    <i class="fa fa-gavel text-[11px]"></i>
                                    <span>Ikuti Lelang Sekarang &rarr;</span>
                                @elseif($isScheduled)
                                    <i class="fa fa-calendar-clock text-[11px]"></i>
                                    <span>Lihat Jadwal Lelang &rarr;</span>
                                @else
                                    <span>Lihat Detail Sesi & Pemenang &rarr;</span>
                                @endif
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 text-center">
                {{ $auctions->links() }}
            </div>
        @endif

    </div>
</x-app-layout>
