<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        
        <!-- Header & Statistics Banner -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="p-2.5 rounded-2xl bg-indigo-50 text-indigo-600 text-lg shadow-xs">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </span>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900">Etalase Jual Beli Komunitas</h1>
                        <p class="text-xs text-slate-500 mt-0.5">Merchandise resmi, sparepart hobi, peralatan, & kreasi eksklusif komunitas se-Sumatera Barat.</p>
                    </div>
                </div>
            </div>

            <!-- Quick Counter Stats -->
            <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
                <div class="px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 text-xs font-bold flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-boxes-stacked text-indigo-600 text-[11px]"></i>
                    <span>{{ $stats['total_products'] }} Produk Aktif</span>
                </div>
                <div class="px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 text-xs font-bold flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-users text-indigo-600 text-[11px]"></i>
                    <span>{{ $stats['total_communities'] }} Komunitas Berjualan</span>
                </div>
                <div class="px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold flex items-center gap-1.5 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>{{ $stats['total_in_stock'] }} Stok Ready</span>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar (Modern Responsive Pills, Integrated Search) -->
        <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
            <form action="{{ route('marketplace.index') }}" method="GET" class="relative flex items-center">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
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
                        placeholder="Cari produk hobi, jersey komunitas, helm, sparepart, kopi bubuk, stiker..." 
                        class="w-full pl-9 pr-24 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 text-xs focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 py-2.5 transition"
                    >
                    <div class="absolute right-1.5 inset-y-1.5 flex items-center gap-1">
                        @if(request()->filled('search'))
                            <a href="{{ route('marketplace.index', request()->except('search', 'page')) }}" class="p-1.5 text-slate-400 hover:text-slate-600 text-xs rounded-lg hover:bg-slate-100 transition" title="Hapus pencarian">
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

            <!-- Category & Sort Controls (Horizontal Swipeable on Mobile) -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pt-2 border-t border-slate-100">
                <!-- Categories Strip -->
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5 w-full sm:w-auto">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mr-1 flex-shrink-0">
                        Kategori:
                    </span>

                    <!-- Semua -->
                    <a href="{{ route('marketplace.index', array_merge(request()->except('category', 'page'), ['category' => 'all'])) }}"
                       class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ (!request('category') || request('category') === 'all') ? 'bg-indigo-600 text-white shadow-sm ring-2 ring-indigo-600/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        <span>Semua</span>
                    </a>

                    @foreach($categories as $cat)
                        <a href="{{ route('marketplace.index', array_merge(request()->except('category', 'page'), ['category' => $cat])) }}"
                           class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all capitalize {{ request('category') === $cat ? 'bg-indigo-600 text-white shadow-sm ring-2 ring-indigo-600/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                            <span>{{ str_replace('_', ' ', $cat) }}</span>
                        </a>
                    @endforeach
                </div>

                <!-- Sorting Dropdown & Reset -->
                <div class="flex items-center gap-2 self-end sm:self-auto flex-shrink-0">
                    <div class="flex items-center gap-1.5 text-xs text-slate-500">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Urutkan:</span>
                        <select onchange="location = this.value;" class="text-xs font-bold rounded-xl bg-slate-50 border border-slate-200 text-slate-700 py-1 pl-2.5 pr-7 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="{{ route('marketplace.index', array_merge(request()->except('sort', 'page'), ['sort' => 'latest'])) }}" {{ $sort === 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="{{ route('marketplace.index', array_merge(request()->except('sort', 'page'), ['sort' => 'price_asc'])) }}" {{ $sort === 'price_asc' ? 'selected' : '' }}>Harga: Termurah</option>
                            <option value="{{ route('marketplace.index', array_merge(request()->except('sort', 'page'), ['sort' => 'price_desc'])) }}" {{ $sort === 'price_desc' ? 'selected' : '' }}>Harga: Termahal</option>
                            <option value="{{ route('marketplace.index', array_merge(request()->except('sort', 'page'), ['sort' => 'stock_low'])) }}" {{ $sort === 'stock_low' ? 'selected' : '' }}>Stok Menipis</option>
                        </select>
                    </div>

                    @if(request()->hasAny(['search', 'category', 'sort', 'community_id']) && (request('category') !== 'all' || request()->filled('search') || request()->filled('community_id') || request('sort') !== 'latest'))
                        <a href="{{ route('marketplace.index') }}" class="text-[11px] font-bold text-rose-600 hover:text-rose-700 hover:underline flex items-center gap-1 py-1 px-2 rounded-lg hover:bg-rose-50 transition" title="Reset semua filter">
                            <i class="fa fa-rotate-left text-[10px]"></i>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        @if($products->isEmpty())
            <div class="p-8 sm:p-12 rounded-2xl bg-white border border-slate-200 text-center text-slate-500 space-y-3 shadow-sm">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-basket-shopping"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Tidak ada produk yang sesuai kriteria pencarian</h3>
                <p class="text-xs text-slate-400 max-w-md mx-auto">Coba gunakan kata kunci lain atau periksa kategori komunitas yang berbeda.</p>
                <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-sm hover:bg-indigo-500 transition">
                    <i class="fa fa-rotate-left text-[10px]"></i>
                    <span>Tampilkan Semua Produk</span>
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($products as $product)
                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:border-indigo-200 hover:shadow-md transition flex flex-col justify-between group">
                        
                        <!-- Top Image & Badges -->
                        <div class="h-44 bg-slate-100 relative overflow-hidden flex-shrink-0">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            
                            <!-- Stock Status Badge -->
                            <div class="absolute top-2.5 left-2.5">
                                @if($product->stock > 0)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-600 text-white shadow flex items-center gap-1">
                                        <i class="fa-solid fa-check text-[9px]"></i>
                                        <span>Stok: {{ $product->stock }}</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-700 text-white shadow flex items-center gap-1">
                                        <i class="fa-solid fa-xmark text-[9px]"></i>
                                        <span>Stok Habis</span>
                                    </span>
                                @endif
                            </div>

                            <!-- Category Badge -->
                            @if($product->community && $product->community->category)
                                <div class="absolute top-2.5 right-2.5">
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-900/80 backdrop-blur text-white shadow capitalize">
                                        {{ str_replace('_', ' ', $product->community->category) }}
                                    </span>
                                </div>
                            @endif

                            <!-- Community Watermark Pill -->
                            <div class="absolute bottom-2.5 left-2.5">
                                <a href="{{ route('communities.show', $product->community->slug) }}?tab=marketplace" class="px-2.5 py-1 rounded-xl text-[10px] font-extrabold bg-slate-900/80 hover:bg-slate-900 backdrop-blur text-white shadow flex items-center gap-1.5 transition truncate max-w-[220px]">
                                    <img src="{{ $product->community->logo_url }}" alt="{{ $product->community->name }}" class="w-4 h-4 rounded-full object-cover flex-shrink-0">
                                    <span class="truncate">{{ $product->community->name }}</span>
                                </a>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 flex-grow flex flex-col justify-between space-y-3">
                            <div class="space-y-1.5">
                                <h3 class="font-black text-slate-800 text-sm line-clamp-1 group-hover:text-indigo-600 transition">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">
                                    {{ $product->description ?: 'Produk resmi yang ditawarkan untuk anggota dan publik di CommunityHub.' }}
                                </p>
                            </div>

                            <!-- Price & Member Fee Notice -->
                            <div class="pt-2 border-t border-slate-100 flex items-end justify-between">
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Harga Produk</span>
                                    <p class="text-base sm:text-lg font-black text-slate-900 leading-tight">
                                        Rp{{ number_format($product->price, 0, ',', '.') }}
                                    </p>
                                </div>
                                <span class="text-[10px] text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100" title="Fee 1% untuk anggota komunitas, 2% untuk umum">
                                    Biaya 1% - 2%
                                </span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="grid grid-cols-2 gap-2 pt-1">
                                <!-- Link ke Forum Komunitas Tab Marketplace -->
                                <a 
                                    href="{{ route('communities.show', $product->community->slug) }}?tab=marketplace#product-{{ $product->id }}" 
                                    class="text-center py-2 rounded-xl font-bold text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 transition shadow-xs flex items-center justify-center gap-1 active:scale-95"
                                    title="Buka forum jual beli komunitas ini"
                                >
                                    <i class="fa-solid fa-comments text-[10px] text-indigo-500"></i>
                                    <span>Di Forum</span>
                                </a>

                                <!-- Tombol Beli / Checkout -->
                                @if($product->stock > 0)
                                    @auth
                                        <form action="{{ route('communities.products.buy', $product->id) }}" method="POST" class="w-full">
                                            @csrf
                                            <button type="submit" class="w-full text-center py-2 rounded-xl font-bold text-xs bg-indigo-600 hover:bg-indigo-500 text-white transition shadow-sm shadow-indigo-500/25 flex items-center justify-center gap-1 active:scale-95">
                                                <i class="fa-solid fa-cart-shopping text-[10px]"></i>
                                                <span>Beli</span>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}" class="w-full text-center py-2 rounded-xl font-bold text-xs bg-indigo-600 hover:bg-indigo-500 text-white transition shadow-sm shadow-indigo-500/25 flex items-center justify-center gap-1 active:scale-95">
                                            <i class="fa-solid fa-cart-shopping text-[10px]"></i>
                                            <span>Beli</span>
                                        </a>
                                    @endauth
                                @else
                                    <button disabled class="w-full text-center py-2 rounded-xl font-bold text-xs bg-slate-100 text-slate-400 cursor-not-allowed flex items-center justify-center gap-1">
                                        <span>Habis</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 text-center">
                {{ $products->links() }}
            </div>
        @endif

    </div>
</x-app-layout>
