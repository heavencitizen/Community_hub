<x-app-layout>
    <div x-data="{ 
        activeTab: '{{ request('tab', 'forum') }}', 
        showProductModal: false, 
        showAuctionModal: false, 
        historyAuctionId: null,
        checkoutAuctionId: null,
        showRulesModal: false,
        showEditModal: false,
        showGuestModal: false,
        guestModalAction: 'berinteraksi dengan forum',
        openShareModal: false,
        shareUrl: '',
        shareTitle: '',
        triggerGuestModal(action) {
            this.guestModalAction = action;
            this.showGuestModal = true;
        },
        openShare(url, title) {
            this.shareUrl = url;
            this.shareTitle = title;
            this.openShareModal = true;
        }
    }" class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6 relative">

        <!-- 1. Community Header Card with Clean Banner & Polished Structure -->
        <div class="rounded-3xl overflow-hidden bg-white border border-slate-200 shadow-sm relative z-0">
            <!-- Banner Cover Image Container -->
            <div class="h-48 sm:h-64 relative bg-slate-900 overflow-hidden">
                <img src="{{ $community->banner_url }}" alt="{{ $community->name }}" class="w-full h-full object-cover">
                <!-- Vignette Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-slate-950/20"></div>

                <!-- Top-Left: Back to Catalog Pill -->
                <div class="absolute top-3.5 left-3.5 sm:top-4 sm:left-4">
                    <a href="{{ route('communities.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-900/60 hover:bg-slate-900 text-white backdrop-blur-md transition shadow border border-white/20">
                        <i class="fa fa-arrow-left text-[10px]"></i>
                        <span>Jelajah Komunitas</span>
                    </a>
                </div>

                <!-- Top-Right: Category Badge on Banner -->
                <div class="absolute top-3.5 right-3.5 sm:top-4 sm:right-4">
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-slate-900/70 text-white backdrop-blur-md shadow border border-white/20">
                        <i class="fa-solid fa-tag text-indigo-400"></i>
                        <span>{{ $community->category ?? 'Hobi' }}</span>
                    </span>
                </div>

                <!-- Bottom-Right: Quick Change Banner Button for Ketua -->
                @if(auth()->check() && ($community->user_id === auth()->id() || auth()->user()->isSuperAdmin()))
                    <button type="button" @click="showEditModal = true" class="absolute bottom-3.5 right-3.5 sm:bottom-4 sm:right-4 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-900/80 hover:bg-slate-900 text-white text-xs font-bold backdrop-blur-md shadow-md transition border border-white/20">
                        <i class="fa fa-camera text-indigo-300 text-xs"></i>
                        <span>Ubah Sampul</span>
                    </button>
                @endif
            </div>

            <!-- Profile Info & Action Bar -->
            <div class="px-5 sm:px-8 pb-6 pt-3 bg-white space-y-4">
                <!-- Top Row: Avatar overlapping cover (Left) & Action Buttons (Right) -->
                <div class="flex items-end justify-between -mt-14 sm:-mt-20 mb-2 sm:mb-3 gap-4">
                    <!-- Logo Squircle (Overlapping Banner) -->
                    <div class="relative z-10 flex-shrink-0">
                        <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-2xl sm:rounded-3xl bg-white ring-4 ring-white shadow-xl overflow-hidden flex items-center justify-center relative group border border-slate-100">
                            <img src="{{ $community->logo_url }}" alt="{{ $community->name }}" class="w-full h-full object-cover">
                            @if(auth()->check() && ($community->user_id === auth()->id() || auth()->user()->isSuperAdmin()))
                                <button type="button" @click="showEditModal = true" class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white text-[11px] font-bold" title="Ubah Foto Logo">
                                    <i class="fa fa-camera text-sm mb-0.5"></i>
                                    <span>Ganti</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons & Status (Right-Aligned on White Background) -->
                    <div class="flex items-center gap-2 sm:gap-2.5 flex-wrap justify-end pb-1">
                        @if(auth()->check() && $community->user_id === auth()->id())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:py-2 rounded-xl bg-amber-50 text-amber-800 border border-amber-200/80 text-xs font-extrabold shadow-sm">
                                <i class="fa-solid fa-crown text-amber-500 text-xs"></i>
                                <span>Ketua<span class="hidden sm:inline"> Komunitas</span></span>
                            </span>
                        @endif

                        <!-- Tombol Bagikan Komunitas -->
                        <button type="button" 
                                @click="openShare('{{ route('communities.show', $community->slug) }}', 'Ayo bergabung dengan komunitas {{ $community->name }} di CommunityHub! {{ Str::limit($community->description, 60) }}')" 
                                class="px-3 sm:px-3.5 py-1.5 sm:py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 shadow-sm active:scale-95">
                            <i class="fa fa-share-nodes text-indigo-600"></i>
                            <span>Bagikan</span>
                        </button>

                        <!-- Khusus Ketua Komunitas: Tombol Edit & Kelola Profil -->
                        @if(auth()->check() && ($community->user_id === auth()->id() || auth()->user()->isSuperAdmin()))
                            <button type="button" 
                                    @click="showEditModal = true" 
                                    class="px-3.5 sm:px-4 py-1.5 sm:py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm active:scale-95">
                                <i class="fa fa-pen-to-square"></i>
                                <span>Edit<span class="hidden sm:inline"> Komunitas</span></span>
                            </button>
                        @endif

                        <!-- Status Keanggotaan / Tombol Gabung -->
                        @auth
                            @if($isMember)
                                @if($community->user_id !== auth()->id())
                                    <form action="{{ route('communities.leave', $community->id) }}" method="POST" onsubmit="return confirm('Yakin ingin keluar dari komunitas ini?')">
                                        @csrf
                                        <button type="submit" class="px-3.5 sm:px-4 py-1.5 sm:py-2 rounded-xl bg-white hover:bg-rose-50 text-slate-700 hover:text-rose-600 text-xs font-bold border border-slate-200 transition shadow-sm">
                                            ✓ Anggota (Keluar)
                                        </button>
                                    </form>
                                @endif
                            @else
                                <form action="{{ route('communities.join', $community->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-1.5 sm:py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-500/25 transition flex items-center gap-1.5 active:scale-95">
                                        <i class="fa fa-user-plus"></i>
                                        <span>+ Gabung Komunitas</span>
                                    </button>
                                </form>
                            @endif
                        @else
                            <button type="button" @click="triggerGuestModal('bergabung dengan komunitas ini')" class="px-4 py-1.5 sm:py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-500/25 transition flex items-center gap-1.5 active:scale-95">
                                <i class="fa fa-user-plus"></i>
                                <span>+ Gabung Komunitas</span>
                            </button>
                        @endauth
                    </div>
                </div>

                <!-- Row 2: Community Identity & Metadata -->
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-slate-900 tracking-tight leading-tight">
                            {{ $community->name }}
                        </h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                            <i class="fa fa-circle text-[6px] text-emerald-500 animate-pulse"></i>
                            <span>Aktif</span>
                        </span>
                    </div>

                    <p class="text-xs sm:text-sm text-slate-500 flex items-center gap-2 sm:gap-3 flex-wrap font-medium">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-crown text-amber-500 text-xs"></i>
                            <span>Ketua:</span> 
                            @if($community->owner)
                                <a href="{{ route('users.show', $community->owner->username) }}" class="text-indigo-600 font-bold hover:underline">{{ $community->owner->name }}</a>
                            @else
                                <strong class="text-slate-800 font-bold">Admin</strong>
                            @endif
                        </span>
                        <span class="text-slate-300">•</span>
                        <span class="text-slate-700 font-bold flex items-center gap-1.5">
                            <i class="fa fa-users text-indigo-500 text-xs"></i>
                            <span>{{ $community->members_count }} Anggota</span>
                        </span>
                        <span class="text-slate-300">•</span>
                        <span class="text-slate-400 flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar text-slate-400 text-xs"></i>
                            <span>Terbentuk {{ $community->created_at->format('M Y') }}</span>
                        </span>
                    </p>

                    <!-- Row 3: Community Description Box -->
                    @if($community->description)
                        <div class="pt-1.5">
                            <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs sm:text-sm text-slate-600 leading-relaxed max-w-5xl">
                                <p>{{ $community->description }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Compact Tab Navigation Bar -->
            <div class="flex items-center gap-1 px-4 sm:px-6 border-t border-slate-100 bg-slate-50/70 overflow-x-auto no-scrollbar whitespace-nowrap text-xs font-bold">
                <button type="button" @click="activeTab = 'forum'" :class="activeTab === 'forum' ? 'text-indigo-600 border-indigo-600 bg-white' : 'text-slate-500 hover:text-slate-800 border-transparent'" class="py-2.5 px-3 border-b-2 transition flex items-center gap-1.5 rounded-t-lg flex-shrink-0">
                    <i class="fa fa-comments"></i><span>Feed & Diskusi</span>
                </button>
                <button type="button" @click="activeTab = 'marketplace'" :class="activeTab === 'marketplace' ? 'text-indigo-600 border-indigo-600 bg-white' : 'text-slate-500 hover:text-slate-800 border-transparent'" class="py-2.5 px-3 border-b-2 transition flex items-center gap-1.5 rounded-t-lg flex-shrink-0">
                    <i class="fa fa-bag-shopping"></i><span>Jual Beli</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-indigo-100 text-indigo-700">{{ $community->products->count() }}</span>
                </button>
                <button type="button" @click="activeTab = 'auctions'" :class="activeTab === 'auctions' ? 'text-indigo-600 border-indigo-600 bg-white' : 'text-slate-500 hover:text-slate-800 border-transparent'" class="py-2.5 px-3 border-b-2 transition flex items-center gap-1.5 rounded-t-lg flex-shrink-0">
                    <i class="fa fa-gavel"></i><span>Lelang</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-indigo-100 text-indigo-700">{{ $community->auctions->count() }}</span>
                </button>
                <button type="button" @click="activeTab = 'members'" :class="activeTab === 'members' ? 'text-indigo-600 border-indigo-600 bg-white' : 'text-slate-500 hover:text-slate-800 border-transparent'" class="py-2.5 px-3 border-b-2 transition flex items-center gap-1.5 rounded-t-lg flex-shrink-0">
                    <i class="fa fa-user-group"></i><span>Anggota</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-slate-200 text-slate-700">{{ $community->members->count() }}</span>
                </button>
                <button type="button" @click="activeTab = 'rules'" :class="activeTab === 'rules' ? 'text-indigo-600 border-indigo-600 bg-white' : 'text-slate-500 hover:text-slate-800 border-transparent'" class="py-2.5 px-3 border-b-2 transition flex items-center gap-1.5 rounded-t-lg flex-shrink-0">
                    <i class="fa fa-scale-balanced"></i><span>Aturan & Sanksi</span>
                </button>
            </div>
        </div>

        <!-- 2. Main Content Tabs Container (GRID UTAMA) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Left 8 Columns (Konten Tab) -->
            <div class="lg:col-span-8 space-y-4">

                <!-- ════════════════════════════════════════════════════════════════ -->
                <!-- TAB 1: FORUM & FEED TIMELINE -->
                <!-- ════════════════════════════════════════════════════════════════ -->
                <div x-show="activeTab === 'forum'" class="space-y-4" x-cloak>
                    @auth
                        @if($isMember || auth()->user()->isSuperAdmin())
                            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3" 
                                 x-data="{ openComposer: false, fileName: '', mediaPreview: null, mediaType: null, handleFileSelect(e) { const file = e.target.files[0]; if (!file) { this.clearMedia(); return; } this.fileName = file.name; this.mediaType = file.type.startsWith('video') ? 'video' : 'image'; if (this.mediaPreview) { URL.revokeObjectURL(this.mediaPreview); } this.mediaPreview = URL.createObjectURL(file); }, clearMedia() { if (this.mediaPreview) { URL.revokeObjectURL(this.mediaPreview); } this.mediaPreview = null; this.mediaType = null; this.fileName = ''; if ($refs.commFileInput) { $refs.commFileInput.value = ''; } } }">
                                <div class="flex items-center gap-3">
                                    <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="w-9 h-9 rounded-xl object-cover border border-slate-200 flex-shrink-0">
                                    <button type="button" @click="openComposer = !openComposer" class="flex-grow text-left py-2.5 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200/80 text-slate-500 text-xs font-medium transition flex items-center justify-between">
                                        <span>Apa cerita, tips hobi, atau agenda di forum {{ explode(' ', $community->name)[0] }}?</span>
                                        <i class="fa fa-pen-to-square text-indigo-500 text-sm"></i>
                                    </button>
                                </div>
                                <div x-show="openComposer" x-cloak class="pt-3 border-t border-slate-100 space-y-3">
                                    <form action="{{ route('communities.posts.store', $community->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                        @csrf
                                        <div><textarea name="content" rows="3" required placeholder="Tuliskan cerita..." class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs p-3 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea></div>
                                        <div x-show="mediaPreview" x-cloak class="relative rounded-2xl overflow-hidden border border-slate-200 bg-slate-900 shadow-md max-h-64 flex items-center justify-center">
                                            <template x-if="mediaType === 'image'"><img :src="mediaPreview" class="w-full max-h-64 object-cover rounded-2xl"></template>
                                            <template x-if="mediaType === 'video'"><video :src="mediaPreview" controls class="w-full max-h-64 rounded-2xl"></video></template>
                                            <div class="absolute top-2.5 left-2.5 right-2.5 flex items-center justify-between pointer-events-none">
                                                <span class="px-2.5 py-1 rounded-lg bg-slate-900/80 backdrop-blur text-white text-[10px] font-bold flex items-center gap-1.5 shadow"><i class="fa-solid" :class="mediaType === 'video' ? 'fa-video text-indigo-400' : 'fa-image text-emerald-400'"></i><span x-text="fileName" class="max-w-[180px] truncate"></span></span>
                                                <button type="button" @click="clearMedia()" class="pointer-events-auto p-1.5 rounded-full bg-slate-900/80 hover:bg-rose-600 text-white transition"><i class="fa-solid fa-xmark text-xs"></i></button>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-100">
                                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer transition"><i class="fa fa-image text-emerald-500 text-sm"></i><span>Foto / Video</span><input type="file" name="media" x-ref="commFileInput" accept="image/*,video/*" class="hidden" @change="handleFileSelect($event)"></label>
                                            <div class="flex items-center gap-2 ml-auto">
                                                <button type="button" @click="openComposer = false" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:bg-slate-100 text-xs font-bold">Batal</button>
                                                <button type="submit" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm">Kirim Post</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="p-3.5 rounded-xl bg-indigo-50 border border-indigo-100 text-xs text-indigo-700 flex items-center justify-between gap-2">
                                <span>Ingin ikut berdiskusi dan posting? Bergabunglah dengan komunitas ini!</span>
                                <form action="{{ route('communities.join', $community->id) }}" method="POST">@csrf<button type="submit" class="px-3 py-1.5 rounded-xl bg-indigo-600 text-white font-bold">Gabung</button></form>
                            </div>
                        @endif
                    @else
                        <div class="p-3.5 rounded-xl bg-indigo-50 border border-indigo-100 text-xs text-indigo-700 flex items-center justify-between gap-2">
                            <span>Ingin menyapa anggota forum?</span>
                            <button type="button" @click="triggerGuestModal('membuat postingan di forum')" class="px-3 py-1.5 rounded-xl bg-indigo-600 text-white font-bold">Masuk / Daftar</button>
                        </div>
                    @endauth

                    <div class="space-y-4">
                        @forelse($posts as $post) 
                            <x-post-card :post="$post" :show-community="false" /> 
                        @empty
                            <div class="p-8 rounded-2xl bg-white border border-slate-200 text-center text-slate-500 space-y-1"><i class="fa fa-comments text-2xl text-slate-300"></i><p class="text-sm font-bold text-slate-800">Belum ada postingan di forum ini.</p></div>
                        @endforelse
                        <div class="mt-3 text-center">{{ $posts->links() }}</div>
                    </div>
                </div>

                <!-- ════════════════════════════════════════════════════════════════ -->
                <!-- TAB 2: JUAL BELI DI KOMUNITAS (MARKETPLACE) -->
                <!-- ════════════════════════════════════════════════════════════════ -->
                <div x-show="activeTab === 'marketplace'" class="space-y-4" x-cloak>
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-black text-slate-800 flex items-center gap-1.5"><i class="fa fa-store text-emerald-600"></i><span>Etalase Jual Beli Komunitas</span></h2>
                            <p class="text-[11px] text-slate-500 mt-0.5">Fee aplikasi: <strong class="text-indigo-600">1% (Member)</strong> • <strong class="text-slate-600">2% (Umum)</strong></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('marketplace.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold"><i class="fa fa-globe text-[11px] text-amber-500"></i> Semua Produk</a>
                            @if(auth()->check() && ($community->user_id === auth()->id() || auth()->user()->isSuperAdmin()))
                                <button type="button" @click="showProductModal = true" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold"><i class="fa fa-plus text-[10px]"></i> Tambah Produk</button>
                            @endif
                        </div>
                    </div>
                    @if($community->products->isEmpty())
                        <div class="p-8 rounded-2xl bg-white border border-slate-200 text-center text-slate-500"><i class="fa fa-box-open text-3xl text-slate-300"></i><p class="text-sm font-bold mt-2">Belum ada barang.</p></div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($community->products as $product)
                                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:border-indigo-200 hover:shadow-md transition flex flex-col justify-between">
                                    <div class="h-36 bg-slate-100 relative overflow-hidden"><img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover"></div>
                                    <div class="p-3.5 flex-grow flex flex-col justify-between space-y-3">
                                        <div class="space-y-0.5">
                                            <h3 class="font-bold text-slate-800 text-xs line-clamp-1">{{ $product->name }}</h3>
                                            <p class="text-sm font-black text-indigo-600">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                                            <span class="text-[9px] text-slate-400">Fee: 1% / 2%</span>
                                            @auth
                                                <form action="{{ route('communities.products.buy', $product->id) }}" method="POST">@csrf<button type="submit" {{ $product->stock <= 0 ? 'disabled' : '' }} class="px-3.5 py-1.5 rounded-lg {{ $product->stock > 0 ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-400' }} text-xs font-bold">Beli</button></form>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- ════════════════════════════════════════════════════════════════ -->
                <!-- TAB 3: LELANG KOMUNITAS (AUCTION) DENGAN SISTEM COD & JAMINAN -->
                <!-- ════════════════════════════════════════════════════════════════ -->
                <div x-show="activeTab === 'auctions'" class="space-y-4" x-cloak>
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-black text-slate-800 flex items-center gap-1.5">
                                <i class="fa fa-gavel text-indigo-600"></i><span>Arena Lelang Komunitas</span>
                            </h2>
                            <p class="text-[11px] text-slate-500 mt-0.5">Fee pemenang: <strong class="text-indigo-600">1% (Member)</strong> • <strong class="text-slate-600">2% (Umum)</strong> • Mendukung COD</p>
                        </div>
                        <div class="flex items-center gap-2 self-start sm:self-auto">
                            <a href="{{ route('auctions.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5"><i class="fa fa-globe text-[11px] text-slate-500"></i><span>Semua Lelang</span></a>
                            @if(auth()->check() && ($community->user_id === auth()->id() || auth()->user()->isSuperAdmin()))
                                <button type="button" @click="showAuctionModal = true" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5"><i class="fa fa-plus text-[10px]"></i><span>Buka Lelang</span></button>
                            @endif
                        </div>
                    </div>

                    @if($community->auctions->isEmpty())
                        <div class="p-8 sm:p-12 rounded-2xl bg-white border border-slate-200 text-center text-slate-500 space-y-2">
                            <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl"><i class="fa fa-gavel"></i></div>
                            <p class="text-sm font-bold text-slate-800">Belum ada sesi lelang aktif di komunitas ini.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($community->auctions as $auction)
                                @php
                                    $isLive = $auction->isActive();
                                    $isScheduled = $auction->isScheduled();
                                    $isAwaiting = $auction->isAwaitingPayment();
                                    $isCompleted = $auction->isPaid();
                                    $isWanprestasi = $auction->isWanprestasi();
                                    $isCancelled = $auction->status === 'cancelled';
                                    $isWinner = auth()->check() && $auction->winner_id === auth()->id();
                                    $isOwner = auth()->check() && ($auction->user_id === auth()->id() || $community->user_id === auth()->id() || auth()->user()->isSuperAdmin());
                                    $minNextBid = $auction->current_price + $auction->bid_increment;
                                    
                                    $depositPaid = \App\Models\Transaction::where('type', 'auction_deposit')->where('reference_id', $auction->id)->where('user_id', $auction->winner_id)->where('payment_status', 'completed')->value('amount') ?? 0;
                                    $feeData = $auction->calculateFee($auction->winner_id);
                                    $finalTagihan = max(0, $feeData['total_amount'] - $depositPaid);
                                    $isCodPending = \App\Models\Transaction::where('type', 'auction')->where('reference_id', $auction->id)->where('payment_method', 'cod')->where('payment_status', 'pending_cod')->exists();
                                @endphp
                                
                                <div id="auction-{{ $auction->id }}" class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-sm transition hover:border-indigo-200">
                                    <div x-data="{
                                            currentPrice: {{ (float) $auction->current_price }},
                                            formattedPrice: 'Rp{{ number_format($auction->current_price, 0, ',', '.') }}',
                                            leaderName: '{{ $auction->winner ? addslashes($auction->winner->name) : '' }}',
                                            leaderAvatar: '{{ $auction->winner ? $auction->winner->avatar_url : '' }}',
                                            totalBids: {{ $auction->bids->count() }},
                                            minNextBid: {{ $minNextBid }},
                                            bidVal: {{ $minNextBid }},
                                            isNewBid: false,
                                            poll() {
                                                @if($isLive)
                                                fetch('{{ route('auctions.ticker', $auction->id) }}')
                                                    .then(res => res.json())
                                                    .then(data => {
                                                        if (data.current_price > this.currentPrice) {
                                                            this.currentPrice = data.current_price;
                                                            this.formattedPrice = data.current_price_formatted;
                                                            this.minNextBid = data.min_next_bid;
                                                            this.bidVal = data.min_next_bid;
                                                            this.totalBids = data.total_bids;
                                                            if (data.winner) {
                                                                this.leaderName = data.winner.name;
                                                                this.leaderAvatar = data.winner.avatar;
                                                            }
                                                            this.isNewBid = true;
                                                            setTimeout(() => { this.isNewBid = false; }, 2500);
                                                        }
                                                    }).catch(() => {});
                                                @endif
                                            },
                                            init() { @if($isLive) setInterval(() => this.poll(), 6000); @endif }
                                        }" 
                                        class="flex flex-col md:flex-row items-start gap-4 md:gap-5"
                                    >
                                        <!-- BAGIAN KIRI: Foto (Anti Melar) -->
<div class="w-full md:w-56 h-48 md:h-[260px] bg-slate-100 rounded-xl overflow-hidden relative flex-shrink-0 border border-slate-100">
    <img src="{{ $auction->image_url }}" alt="{{ $auction->title }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300">
    <div class="absolute top-2.5 left-2.5">
        @if($isLive)
            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500 text-white shadow flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-white animate-pulse"></span><span>LIVE LELANG</span></span>
        @elseif($isScheduled)
            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-sky-600 text-white shadow flex items-center gap-1.5"><i class="fa fa-calendar-clock text-[9px]"></i><span>TERJADWAL</span></span>
        @elseif($isAwaiting)
            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-500 text-white shadow flex items-center gap-1"><i class="fa fa-hourglass-half text-[9px]"></i><span>PELUNASAN</span></span>
        @elseif($isCompleted)
            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-indigo-600 text-white shadow flex items-center gap-1"><i class="fa fa-check text-[9px]"></i><span>LUNAS & SELESAI</span></span>
        @elseif($isWanprestasi)
            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-600 text-white shadow flex items-center gap-1"><i class="fa fa-triangle-exclamation text-[9px]"></i><span>WANPRESTASI</span></span>
        @else
            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-700 text-white shadow">DIBATALKAN</span>
        @endif
    </div>
    <div class="absolute top-2.5 right-2.5 px-2 py-1 rounded-xl text-[10px] font-bold bg-slate-900/80 backdrop-blur text-white shadow flex items-center gap-1">
        <i class="fa fa-hand-holding-dollar text-indigo-300 text-[10px]"></i><span x-text="totalBids + ' Tawaran'"></span>
    </div>
</div>
                                        <!-- BAGIAN KANAN: Konten & Aksi -->
                                        <div class="flex-grow flex flex-col justify-between py-0.5 space-y-3 w-full">
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between gap-1">
                                                    @if($auction->auction_code)<span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 font-mono text-[9px] font-bold">{{ $auction->auction_code }}</span>@endif
                                                    @if($auction->anti_sniping)<span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[9px] font-bold border border-emerald-200"><i class="fa fa-shield-halved text-[8px]"></i> Anti-Sniping</span>@endif
                                                </div>
                                                <h3 class="font-black text-slate-800 text-sm sm:text-base line-clamp-1 group-hover:text-indigo-600 transition">{{ $auction->title }}</h3>
                                                <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">{{ $auction->description ?: 'Barang koleksi eksklusif persembahan komunitas.' }}</p>

                                                <div class="p-3 mt-2 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                                                    <div>
                                                        <span class="text-[9px] font-bold text-indigo-600 uppercase tracking-wider block">{{ $isLive ? 'Tawaran Tertinggi' : ($isScheduled ? 'Nilai Pembukaan' : 'Pokok Lelang') }}</span>
                                                        <p class="text-base sm:text-lg font-black text-slate-900 flex items-center gap-2">
                                                            <span x-text="formattedPrice">Rp{{ number_format($auction->current_price, 0, ',', '.') }}</span>
                                                            <span x-show="isNewBid" x-cloak class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-500 text-white animate-bounce shadow-sm">Bid Baru!</span>
                                                        </p>
                                                    </div>
                                                    <div class="text-right text-[10px] text-slate-500">
                                                        <div>Awal: <strong>Rp{{ number_format($auction->starting_price, 0, ',', '.') }}</strong></div>
                                                        <div>Kelipatan: <strong>+Rp{{ number_format($auction->bid_increment, 0, ',', '.') }}</strong></div>
                                                    </div>
                                                </div>

                                                @if($isLive && $auction->end_time)
                                                    <div x-data="{ endTime: new Date('{{ $auction->end_time->toISOString() }}').getTime(), timeStr: '', updateTimer() { const now = new Date().getTime(); const diff = this.endTime - now; if (diff <= 0) { this.timeStr = 'Waktu Habis'; return; } const days = Math.floor(diff / (1000 * 60 * 60 * 24)); const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)); const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60)); const seconds = Math.floor((diff % (1000 * 60)) / 1000); if (days > 0) { this.timeStr = `${days}h ${hours}j ${minutes}m`; } else { this.timeStr = `${hours}j ${minutes}m ${seconds}s`; } }, init() { this.updateTimer(); setInterval(() => this.updateTimer(), 1000); } }" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg bg-slate-50 text-[11px] text-slate-600 border border-slate-100 font-medium mt-2">
                                                        <span class="flex items-center gap-1.5 text-slate-500 text-[10px]"><i class="fa fa-clock text-indigo-600"></i> Sisa Waktu:</span><strong x-text="timeStr" class="text-indigo-700 font-mono font-black text-xs">Menghitung...</strong>
                                                    </div>
                                                @elseif($isScheduled && $auction->start_time)
                                                    <div x-data="{ startTime: new Date('{{ $auction->start_time->toISOString() }}').getTime(), timeStr: '', updateTimer() { const now = new Date().getTime(); const diff = this.startTime - now; if (diff <= 0) { this.timeStr = 'Mulai Sekarang'; return; } const days = Math.floor(diff / (1000 * 60 * 60 * 24)); const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)); const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60)); const seconds = Math.floor((diff % (1000 * 60)) / 1000); if (days > 0) { this.timeStr = `${days}h ${hours}j ${minutes}m`; } else { this.timeStr = `${hours}j ${minutes}m ${seconds}s`; } }, init() { this.updateTimer(); setInterval(() => this.updateTimer(), 1000); } }" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg bg-sky-50 text-[11px] text-sky-800 border border-sky-100 font-medium mt-2">
                                                        <span class="flex items-center gap-1.5 text-sky-600 text-[10px]"><i class="fa fa-calendar-clock text-sky-600"></i> Dimulai Dalam:</span><strong x-text="timeStr" class="text-sky-800 font-mono font-black text-xs">Menghitung...</strong>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- AREA KONDISIONAL (TIDAK ADA TAG DIV YANG HILANG DI SINI) -->
                                            <div class="mt-auto pt-3">
                                                @if($isScheduled)
                                                    <div class="p-3 rounded-xl bg-sky-50 border border-sky-100 text-xs text-sky-900"><span class="flex items-center gap-2 font-medium"><i class="fa fa-calendar text-sky-600"></i><span>Sesi open bidding baru dibuka pada <strong>{{ $auction->start_time->format('d M Y, H:i') }} WIB</strong>.</span></span></div>
                                                @elseif($isLive)
                                                    @auth
                                                        @if($isOwner)
                                                            <div class="text-[11px] text-slate-500 bg-slate-50 p-3 rounded-xl border border-slate-100 flex items-center justify-between">
                                                                <span class="flex items-center gap-1.5 font-bold"><i class="fa fa-info-circle text-indigo-600"></i><span>Anda adalah pengelola lelang ini.</span></span>
                                                                <form action="{{ route('communities.auctions.close', $auction->id) }}" method="POST" onsubmit="return confirm('Tutup sesi lelang sekarang dan tetapkan penawar tertinggi sebagai pemenang?')">
                                                                    @csrf<button type="submit" class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-[11px] font-bold shadow-sm transition">Tutup Manual</button>
                                                                </form>
                                                            </div>
                                                        @else
                                                            @php
                                                                $hasPaidDeposit = \App\Models\Transaction::where('type', 'auction_deposit')->where('reference_id', $auction->id)->where('user_id', auth()->id())->where('payment_status', 'completed')->exists();
                                                                $depositAmount = max(50000, $auction->starting_price * 0.05);
                                                            @endphp

                                                            @if($hasPaidDeposit)
                                                                <div class="space-y-2 pt-2 border-t border-slate-100 mt-2">
                                                                    <form action="{{ route('communities.auctions.bid', $auction->id) }}" method="POST" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                                                        @csrf
                                                                        <div class="relative flex-grow">
                                                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs font-bold">Rp</span>
                                                                            <input type="number" name="bid_amount" x-model="bidVal" :min="minNextBid" step="{{ $auction->bid_increment }}" class="w-full pl-9 pr-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs font-bold py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm" :placeholder="minNextBid">
                                                                        </div>
                                                                        <div class="flex items-center gap-1.5">
                                                                            <button type="button" @click="bidVal = minNextBid" class="px-2 py-2 rounded-xl bg-white hover:bg-indigo-100 border border-slate-200 text-indigo-700 text-[10px] font-bold transition flex-shrink-0">+{{ number_format($auction->bid_increment, 0, ',', '.') }}</button>
                                                                            <button type="button" @click="bidVal = Number(minNextBid) + Number({{ $auction->bid_increment }})" class="px-2 py-2 rounded-xl bg-white hover:bg-indigo-100 border border-slate-200 text-indigo-700 text-[10px] font-bold transition flex-shrink-0">+{{ number_format($auction->bid_increment * 2, 0, ',', '.') }}</button>
                                                                            <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-black shadow-md shadow-indigo-200 transition flex items-center justify-center gap-1.5 flex-shrink-0"><i class="fa fa-gavel text-[10px]"></i><span>Pasang Bid</span></button>
                                                                        </div>
                                                                    </form>
                                                                    <p class="text-[10px] text-indigo-700 font-medium flex items-center justify-between">
                                                                        <span>Tawaran minimal berikutnya: <strong class="font-bold" x-text="'Rp' + Number(minNextBid).toLocaleString('id-ID')">Rp{{ number_format($minNextBid, 0, ',', '.') }}</strong></span>
                                                                        <span class="text-[9px] text-emerald-600 flex items-center gap-1 font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span><span>Live Auto-Update</span></span>
                                                                    </p>
                                                                </div>
                                                            @else
                                                                <div class="p-3 sm:p-4 rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200/60 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
                                                                    <div class="flex items-center gap-3">
                                                                        <div class="w-8 h-8 rounded-full bg-amber-100 border border-amber-200 text-amber-600 flex items-center justify-center shadow-inner flex-shrink-0"><i class="fa fa-shield-halved text-xs"></i></div>
                                                                        <div class="space-y-0.5">
                                                                            <h4 class="font-black text-amber-900 text-[11px] uppercase tracking-wide">Deposit Jaminan Lelang</h4>
                                                                            <p class="text-[10px] text-amber-700 leading-tight">Cegah Bid & Run. Setor Rp{{ number_format($depositAmount, 0, ',', '.') }} untuk menawar. <span class="hidden xl:inline">Otomatis memotong tagihan akhir.</span></p>
                                                                        </div>
                                                                    </div>
                                                                    <form action="{{ route('communities.auctions.deposit', $auction->id) }}" method="POST" class="w-full sm:w-auto flex-shrink-0">
                                                                        @csrf<button type="submit" class="w-full px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-black rounded-lg shadow-md transition whitespace-nowrap"><i class="fa fa-qrcode"></i> Bayar Jaminan</button>
                                                                    </form>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @else
                                                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 mt-2">
                                                            <span class="text-xs font-medium text-slate-600">Masuk untuk mengajukan penawaran.</span>
                                                            <button type="button" @click="triggerGuestModal('mengikuti lelang ini')" class="px-4 py-1.5 rounded-xl bg-indigo-600 text-white text-xs font-bold transition">Masuk / Daftar</button>
                                                        </div>
                                                    @endauth
                                            @elseif($isAwaiting)
                                                <div class="p-3.5 rounded-xl {{ $isCodPending ? 'bg-sky-50 border-sky-200' : 'bg-amber-50 border-amber-200' }} border space-y-3 mt-2">
                                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                                        <div class="space-y-0.5">
                                                            <span class="text-xs font-bold {{ $isCodPending ? 'text-sky-900' : 'text-amber-900' }} flex items-center gap-1.5">
                                                                <i class="fa {{ $isCodPending ? 'fa-handshake text-sky-600' : 'fa-trophy text-amber-600' }}"></i>
                                                                <span>{{ $isCodPending ? 'Menunggu Serah Terima (COD)' : 'Pemenang Ditetapkan (Menunggu Pelunasan)' }}</span>
                                                            </span>
                                                            <p class="text-[11px] {{ $isCodPending ? 'text-sky-800' : 'text-amber-800' }}">
                                                                Pemenang: <strong x-text="leaderName">{{ $auction->winner->name ?? 'Tidak ada pemenang' }}</strong> (Sisa: Rp{{ number_format($finalTagihan, 0, ',', '.') }})
                                                            </p>
                                                        </div>
                                                        @if($auction->payment_deadline && !$isCodPending)
                                                            <div class="px-2.5 py-1 rounded-lg bg-white border border-amber-200 text-amber-900 text-[10px] font-bold"><i class="fa fa-hourglass-half text-amber-500"></i> Batas: <strong>{{ $auction->payment_deadline->format('d M, H:i') }} WIB</strong></div>
                                                        @endif
                                                    </div>
                                                    <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t {{ $isCodPending ? 'border-sky-200/70' : 'border-amber-200/70' }}">
                                                        <a href="{{ route('communities.auctions.certificate', $auction->id) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white text-indigo-700 border border-slate-200 text-[10px] font-bold shadow-sm flex items-center gap-1.5"><i class="fa fa-file-invoice text-indigo-600"></i> Kutipan Lelang</a>
                                                        @if($isWinner)
                                                            @if($isCodPending)
                                                                <span class="px-3 py-1.5 rounded-xl bg-sky-200 text-sky-800 text-[10px] font-bold">Silakan hubungi penjual untuk bertemu.</span>
                                                            @else
                                                                <button type="button" @click="checkoutAuctionId = {{ $auction->id }}" class="px-4 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-md shadow-emerald-200 transition flex items-center gap-1.5 animate-bounce">
                                                                    <i class="fa fa-credit-card"></i><span>Pilih Pelunasan &rarr;</span>
                                                                </button>
                                                            @endif
                                                        @elseif($isOwner)
                                                            <div class="flex items-center gap-1.5">
                                                                @if($isCodPending)
                                                                    <form action="{{ route('communities.auctions.codComplete', $auction->id) }}" method="POST" onsubmit="return confirm('Konfirmasi COD Selesai? Pemenang sudah menerima barang?')">
                                                                        @csrf<button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-1.5"><i class="fa fa-check-double text-[10px]"></i> Konfirmasi COD</button>
                                                                    </form>
                                                                @endif
                                                                <form action="{{ route('communities.auctions.wanprestasi', $auction->id) }}" method="POST" onsubmit="return confirm('Nyatakan pemenang wanprestasi?')">
                                                                    @csrf<button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition">Gugurkan (Wanprestasi)</button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @elseif($isCompleted)
                                                <div class="p-3.5 rounded-xl bg-indigo-50 border border-indigo-100 flex justify-between mt-2">
                                                    <div class="space-y-0.5"><span class="text-xs font-bold text-indigo-900"><i class="fa fa-circle-check text-emerald-500"></i> Lunas & Selesai</span><p class="text-[11px] text-indigo-700 font-medium">Pemenang: <strong x-text="leaderName">{{ $auction->winner->name ?? '-' }}</strong></p></div>
                                                    <a href="{{ route('communities.auctions.certificate', $auction->id) }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-[10px] font-bold shadow-sm flex items-center gap-1.5 self-center"><i class="fa fa-file-invoice"></i> Kutipan</a>
                                                </div>
                                            @elseif($isWanprestasi)
                                                <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 flex justify-between mt-2">
                                                    <div class="space-y-0.5"><span class="text-xs font-bold text-rose-900"><i class="fa fa-triangle-exclamation text-rose-600"></i> Lelang Ditutup (Wanprestasi)</span><p class="text-[11px] text-rose-700">Pemenang tidak melunasi tagihan.</p></div>
                                                    @if($auction->winner_id)<a href="{{ route('communities.auctions.certificate', $auction->id) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white text-rose-700 border border-rose-200 text-xs font-bold self-center">Kutipan</a>@endif
                                                </div>
                                            @endif
                                            </div> <!-- INI TAG PENUTUP MT-AUTO AREA KONDISIONAL -->

                                            <div class="flex items-center justify-between pt-3 mt-3 border-t border-slate-100">
                                                <div class="flex items-center gap-2">
                                                    <template x-if="leaderName">
                                                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-100 shadow-sm text-xs transition-all duration-300" :class="isNewBid ? 'ring-2 ring-emerald-400 bg-emerald-50' : ''">
                                                            <img :src="leaderAvatar" :alt="leaderName" class="w-6 h-6 rounded-full object-cover border border-slate-200">
                                                            <div>
                                                                <div class="text-[9px] text-slate-400 font-bold leading-none">{{ $isLive ? 'Memimpin:' : 'Pemenang:' }}</div>
                                                                <div class="font-black text-slate-800 text-[11px] flex items-center gap-1">
                                                                    <span x-text="leaderName"></span>
                                                                    @if($isWinner)<span class="px-1.5 py-0.2 rounded text-[8px] bg-emerald-100 text-emerald-700 uppercase">Anda</span>@endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <button type="button" @click="navigator.clipboard.writeText('{{ route('communities.show', $community->slug) }}#auction-{{ $auction->id }}'); $dispatch('toast', { type: 'success', message: 'Tautan lelang disalin!' });" class="px-2 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-[10px] font-bold"><i class="fa-solid fa-share-nodes"></i></button>
                                                    <button type="button" @click="historyAuctionId = {{ $auction->id }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold"><i class="fa fa-list-ol text-[9px] mr-1"></i> Riwayat (<span x-text="totalBids"></span>)</button>
                                                </div>
                                            </div>

                                        </div> <!-- End Kanan Card Body -->
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- ════════════════════════════════════════════════════════════════ -->
                <!-- TAB 4: DAFTAR ANGGOTA KOMUNITAS -->
                <!-- ════════════════════════════════════════════════════════════════ -->
                <div x-show="activeTab === 'members'" class="space-y-4" x-cloak>
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
                        <h2 class="text-sm font-black text-slate-800 flex items-center gap-1.5"><i class="fa fa-users text-indigo-600"></i><span>Daftar Anggota Terdaftar ({{ $community->members->count() }})</span></h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">Semua anggota dapat melihat profil dan tanggal bergabung sesama anggota.</p>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm divide-y divide-slate-100 overflow-hidden text-xs">
                        @foreach($community->members as $member)
                            <div class="p-3.5 flex items-center justify-between gap-3 hover:bg-slate-50 transition">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('users.show', $member->user->username) }}" class="flex-shrink-0 group"><img src="{{ $member->user->avatar_url }}" alt="{{ $member->user->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 group-hover:ring-2 group-hover:ring-indigo-400 transition"></a>
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <h4 class="font-bold text-slate-800 text-xs"><a href="{{ route('users.show', $member->user->username) }}" class="hover:text-indigo-600 transition">{{ $member->user->name }}</a></h4>
                                            <span class="text-[10px] text-slate-400 font-mono">{{ '@' . $member->user->username }}</span>
                                            @if($member->user_id === $community->user_id) <span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold bg-amber-100 text-amber-700">Ketua</span>
                                            @elseif($member->role === 'moderator') <span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold bg-indigo-100 text-indigo-700">Moderator</span>
                                            @endif
                                            @if($member->status === 'suspended') <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-rose-100 text-rose-700">Dinonaktifkan</span> @endif
                                        </div>
                                        <p class="text-[10px] text-slate-400 mt-0.5"><i class="fa fa-calendar-check text-slate-400 mr-1"></i>Bergabung: <strong>{{ $member->joined_at ? $member->joined_at->format('d M Y') : $member->created_at->format('d M Y') }}</strong></p>
                                    </div>
                                </div>
                                @if(auth()->check() && ($community->user_id === auth()->id() || auth()->user()->isSuperAdmin()) && $member->user_id !== $community->user_id)
                                    <div>
                                        @if($member->status === 'active')
                                            <form action="{{ route('communities.members.updateStatus', [$community->id, $member->id]) }}" method="POST" onsubmit="return confirm('Nonaktifkan anggota ini?')">@csrf @method('PATCH')<input type="hidden" name="status" value="suspended"><button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-[10px] font-bold border border-rose-200 transition">Nonaktifkan</button></form>
                                        @else
                                            <form action="{{ route('communities.members.updateStatus', [$community->id, $member->id]) }}" method="POST">@csrf @method('PATCH')<input type="hidden" name="status" value="active"><button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 text-[10px] font-bold border border-emerald-200 transition">Aktifkan</button></form>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- ════════════════════════════════════════════════════════════════ -->
                <!-- TAB 5: ATURAN & KETENTUAN KOMUNITAS -->
                <!-- ════════════════════════════════════════════════════════════════ -->
                <div x-show="activeTab === 'rules'" class="space-y-4" x-cloak>
                    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="text-sm font-black text-slate-800 flex items-center gap-1.5"><i class="fa fa-scale-balanced text-indigo-600"></i><span>Aturan & Kebijakan Komunitas</span></h3>
                            @if(auth()->check() && ($community->user_id === auth()->id() || auth()->user()->isSuperAdmin()))
                                <button type="button" @click="showRulesModal = true" class="px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-bold border border-indigo-200 transition">Edit Aturan</button>
                            @endif
                        </div>
                        <div class="prose prose-sm text-slate-700 leading-relaxed whitespace-pre-line text-xs bg-slate-50 p-4 rounded-xl border border-slate-200">{{ $community->rules ?? "Aturan standar komunitas." }}</div>
                    </div>
                </div>

            </div>

            <!-- Right 4 Columns Sidebar -->
            <div class="lg:col-span-4 space-y-4 sticky top-20 z-0">
                <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-2.5 shadow-sm text-xs">
                    <h3 class="text-[11px] font-extrabold text-indigo-600 uppercase tracking-wider flex items-center gap-1.5"><i class="fa fa-circle-info"></i> Tentang Komunitas</h3>
                    <p class="text-slate-600 leading-relaxed whitespace-pre-line">{{ $community->description }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-3 shadow-sm text-xs">
                    <div class="flex items-center justify-between">
                        <h3 class="text-[11px] font-extrabold text-indigo-600 uppercase tracking-wider flex items-center gap-1.5"><i class="fa fa-calendar-days"></i> Agenda & Event</h3>
                        @if(auth()->check() && ($community->user_id === auth()->id() || auth()->user()->isSuperAdmin()))
                            <a href="{{ route('events.create') }}" class="text-[11px] text-indigo-600 hover:underline font-bold">+ Buat</a>
                        @endif
                    </div>
                    @forelse($community->events as $ev)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-black uppercase {{ $ev->price == 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-100 text-indigo-700' }}">{{ $ev->price == 0 ? 'Gratis' : 'Rp' . number_format($ev->price, 0, ',', '.') }}</span>
                                <span class="text-[10px] text-slate-400">{{ $ev->event_date->format('d M') }}</span>
                            </div>
                            <h4 class="font-bold text-slate-800 text-xs line-clamp-1">{{ $ev->title }}</h4>
                            <a href="{{ route('events.show', $ev->slug) }}" class="block w-full text-center py-1 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-[10px] font-bold shadow-sm">Lihat & Pesan</a>
                        </div>
                    @empty
                        <p class="text-slate-400 text-[11px] text-center py-2">Belum ada agenda event.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!-- AREA KHUSUS MODAL (BEBAS DARI GRID LAYER AGAR TIDAK TERTIMPA) -->
        <!-- ════════════════════════════════════════════════════════════════ -->

        <!-- MODAL CHECKOUT METHOD SELECTION (PILIH METODE PEMBAYARAN) -->
        @foreach($community->auctions as $auction)
            @php
                $isWinner = auth()->check() && $auction->winner_id === auth()->id();
                $isAwaiting = $auction->isAwaitingPayment();
                $isCodPending = \App\Models\Transaction::where('type', 'auction')->where('reference_id', $auction->id)->where('payment_method', 'cod')->where('payment_status', 'pending_cod')->exists();
            @endphp
            @if($isWinner && $isAwaiting && !$isCodPending)
                @php
                    $depositPaid = \App\Models\Transaction::where('type', 'auction_deposit')->where('reference_id', $auction->id)->where('user_id', $auction->winner_id)->where('payment_status', 'completed')->value('amount') ?? 0;
                    $feeData = $auction->calculateFee($auction->winner_id);
                    $finalTagihan = max(0, $feeData['total_amount'] - $depositPaid);
                @endphp
                <div x-show="checkoutAuctionId === {{ $auction->id }}" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
                    <div @click.away="checkoutAuctionId = null" x-show="checkoutAuctionId === {{ $auction->id }}" x-transition.scale.origin.bottom class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="text-sm font-black text-slate-900 flex items-center gap-2"><i class="fa fa-credit-card text-indigo-600"></i> Pilih Pelunasan</h3>
                            <button type="button" @click="checkoutAuctionId = null" class="text-slate-400 hover:text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-full w-7 h-7 flex items-center justify-center transition"><i class="fa fa-times text-sm"></i></button>
                        </div>
                        
                        <div class="text-xs text-slate-600 space-y-2 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <p class="flex justify-between items-center"><span>Harga Lelang Akhir</span> <strong class="text-slate-900 font-mono text-sm">Rp{{ number_format($auction->current_price, 0, ',', '.') }}</strong></p>
                            <p class="flex justify-between items-center"><span>Jaminan (Telah Dibayar)</span> <strong class="text-emerald-600 font-mono">-Rp{{ number_format($depositPaid, 0, ',', '.') }}</strong></p>
                            <div class="border-t border-slate-200 my-2"></div>
                            <p class="flex justify-between items-center text-sm font-black text-indigo-700"><span>Sisa Tagihan</span> <span class="font-mono text-base">Rp{{ number_format($finalTagihan, 0, ',', '.') }}</span></p>
                        </div>

                        <div class="grid grid-cols-1 gap-3 mt-4">
                            <!-- Opsi Sistem -->
                            <form action="{{ route('communities.auctions.checkout', $auction->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="payment_method" value="system">
                                <button type="submit" class="w-full text-left p-4 rounded-2xl border-2 border-indigo-100 bg-indigo-50/50 hover:border-indigo-500 hover:bg-indigo-50 transition flex items-center gap-4 group">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition"><i class="fa fa-wallet text-lg"></i></div>
                                    <div><span class="block font-black text-indigo-900 text-sm mb-0.5">Bayar via Sistem (Otomatis)</span><span class="block text-[10px] text-indigo-700/70 font-medium">QRIS, Transfer Bank (VA), atau E-Wallet.</span></div>
                                </button>
                            </form>

                            <!-- Opsi COD -->
                            <form action="{{ route('communities.auctions.checkout', $auction->id) }}" method="POST" onsubmit="return confirm('Pilih Bayar di Tempat (COD)? Pastikan Anda segera menghubungi penyelenggara untuk janjian bertemu.')">
                                @csrf
                                <input type="hidden" name="payment_method" value="cod">
                                <button type="submit" class="w-full text-left p-4 rounded-2xl border-2 border-emerald-100 bg-emerald-50/50 hover:border-emerald-500 hover:bg-emerald-50 transition flex items-center gap-4 group">
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition"><i class="fa fa-handshake text-lg"></i></div>
                                    <div><span class="block font-black text-emerald-900 text-sm mb-0.5">Bayar di Tempat (COD)</span><span class="block text-[10px] text-emerald-700/70 font-medium">Bertemu langsung dengan penjual.</span></div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        <!-- POP-UP BAGIKAN KE PIHAK EKSTERNAL -->
        <div x-show="openShareModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="openShareModal = false" class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-black text-slate-800 flex items-center gap-1.5 uppercase tracking-wider"><i class="fa fa-share-nodes text-indigo-600"></i><span>Bagikan ke Sosial Media</span></h3>
                    <button type="button" @click="openShareModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times text-xs"></i></button>
                </div>
                <div class="grid grid-cols-3 gap-2.5 text-center">
                    <a :href="'https://api.whatsapp.com/send?text=' + encodeURIComponent(shareTitle + ' ' + shareUrl)" target="_blank" class="p-3 rounded-xl bg-slate-50 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-200 transition flex flex-col items-center gap-1.5 group"><x-brand-logo name="whatsapp" class="h-7 w-auto" /><span class="text-[10px] font-bold text-slate-700 group-hover:text-emerald-600">WhatsApp</span></a>
                    <button type="button" @click="navigator.clipboard.writeText(shareUrl); alert('Tautan berhasil disalin! Silakan bagikan di Story Instagram Anda 📸')" class="p-3 rounded-xl bg-slate-50 hover:bg-pink-50 border border-slate-200 hover:border-pink-200 transition flex flex-col items-center gap-1.5 group"><x-brand-logo name="instagram" class="h-7 w-auto" /><span class="text-[10px] font-bold text-slate-700 group-hover:text-pink-600">Instagram</span></button>
                    <a :href="'https://twitter.com/intent/tweet?text=' + encodeURIComponent(shareTitle) + '&url=' + encodeURIComponent(shareUrl)" target="_blank" class="p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 transition flex flex-col items-center gap-1.5 group"><x-brand-logo name="twitter" class="h-7 w-auto" /><span class="text-[10px] font-bold text-slate-700">Twitter (X)</span></a>
                    <button type="button" @click="navigator.clipboard.writeText(shareUrl); alert('Tautan disalin ke clipboard! Siap ditempel di TikTok 🎵')" class="p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 transition flex flex-col items-center gap-1.5 group"><x-brand-logo name="tiktok" class="h-7 w-auto" /><span class="text-[10px] font-bold text-slate-700">TikTok</span></button>
                    <a :href="'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(shareUrl)" target="_blank" class="p-3 rounded-xl bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-200 transition flex flex-col items-center gap-1.5 group"><x-brand-logo name="facebook" class="h-7 w-auto" /><span class="text-[10px] font-bold text-slate-700 group-hover:text-blue-600">Facebook</span></a>
                    <a :href="'https://t.me/share/url?url=' + encodeURIComponent(shareUrl) + '&text=' + encodeURIComponent(shareTitle)" target="_blank" class="p-3 rounded-xl bg-slate-50 hover:bg-sky-50 border border-slate-200 hover:border-sky-200 transition flex flex-col items-center gap-1.5 group"><x-brand-logo name="telegram" class="h-7 w-auto" /><span class="text-[10px] font-bold text-slate-700 group-hover:text-sky-600">Telegram</span></a>
                </div>
                <div class="pt-1">
                    <button type="button" @click="navigator.clipboard.writeText(shareUrl); alert('Tautan berhasil disalin! 📋')" class="w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center gap-1.5 transition"><i class="fa fa-link text-[10px]"></i><span>Salin Tautan</span></button>
                </div>
            </div>
        </div>

        <!-- POP-UP GUEST AUTH PROMPT -->
        <div x-show="showGuestModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="showGuestModal = false" class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl text-center space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto text-2xl"><i class="fa-solid fa-heart-pulse"></i></div>
                <div class="space-y-1">
                    <h3 class="text-base font-black text-slate-800">Ingin Berinteraksi di Forum?</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Untuk <span class="font-bold text-indigo-600" x-text="guestModalAction"></span>, silakan masuk ke akun Anda atau daftar gratis di <strong>CommunityHub</strong>!</p>
                </div>
                <div class="space-y-1.5 pt-1">
                    <a href="{{ route('register') }}" class="block w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md transition">Daftar Akun Baru (Gratis)</a>
                    <a href="{{ route('login') }}" class="block w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">Sudah punya akun? Masuk</a>
                </div>
            </div>
        </div>

        <!-- MODAL TAMBAH PRODUK -->
        <div x-show="showProductModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="showProductModal = false" class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-black text-slate-800 uppercase">Tambah Produk Jual Beli</h3>
                    <button type="button" @click="showProductModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times"></i></button>
                </div>
                <form action="{{ route('communities.products.store', $community->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                    @csrf
                    <div><label class="block font-bold text-slate-700 mb-0.5">Nama Produk</label><input type="text" name="name" required class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3"></div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block font-bold text-slate-700 mb-0.5">Harga (Rp)</label><input type="number" name="price" required class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3"></div>
                        <div><label class="block font-bold text-slate-700 mb-0.5">Stok</label><input type="number" name="stock" required class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3"></div>
                    </div>
                    <div><label class="block font-bold text-slate-700 mb-0.5">Deskripsi</label><textarea name="description" rows="2" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3"></textarea></div>
                    <div><label class="block font-bold text-slate-700 mb-0.5">Foto Produk</label><input type="file" name="image" accept="image/*" class="text-[11px] text-slate-500"></div>
                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="showProductModal = false" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold">Batal</button>
                        <button type="submit" class="px-4 py-1.5 rounded-xl bg-indigo-600 text-white font-bold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL BUKA LELANG -->
        <div x-show="showAuctionModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="showAuctionModal = false" class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-black text-slate-800 uppercase">Buka Lelang Baru</h3>
                    <button type="button" @click="showAuctionModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times"></i></button>
                </div>
                <form action="{{ route('communities.auctions.store', $community->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                    @csrf
                    <div><label class="block font-bold text-slate-700 mb-0.5">Judul Barang</label><input type="text" name="title" required class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3"></div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block font-bold text-slate-700 mb-0.5">Harga Awal</label><input type="number" name="starting_price" required class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3"></div>
                        <div><label class="block font-bold text-slate-700 mb-0.5">Kelipatan</label><input type="number" name="bid_increment" required class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block font-bold text-slate-700 mb-0.5">Mulai</label><input type="datetime-local" name="start_time" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3"></div>
                        <div><label class="block font-bold text-slate-700 mb-0.5">Selesai</label><input type="datetime-local" name="end_time" required class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3"></div>
                    </div>
                    <div><label class="block font-bold text-slate-700 mb-0.5">Catatan Serah Terima</label><textarea name="notes" rows="2" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3"></textarea></div>
                    <div class="p-2.5 rounded-xl bg-indigo-50/70 border border-slate-100 flex justify-between">
                        <div><span class="block font-bold text-slate-900 text-xs">Anti-Sniping</span><span class="text-[10px] text-indigo-700">+2 mnt di detik akhir</span></div>
                        <input type="checkbox" name="anti_sniping" value="1" checked class="w-5 h-5 rounded border-slate-300 text-indigo-600">
                    </div>
                    <div><label class="block font-bold text-slate-700 mb-0.5">Foto Barang</label><input type="file" name="image" accept="image/*" class="text-[11px] text-slate-500"></div>
                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="showAuctionModal = false" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold">Batal</button>
                        <button type="submit" class="px-4 py-1.5 rounded-xl bg-indigo-600 text-white font-bold">Buka Lelang</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL RIWAYAT PENAWARAN LELANG -->
        @foreach($community->auctions as $auction)
            <div x-show="historyAuctionId === {{ $auction->id }}" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
                <div @click.away="historyAuctionId = null" class="bg-white rounded-3xl max-w-md w-full p-5 shadow-2xl space-y-4 max-h-[85vh] flex flex-col">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 flex items-center gap-1.5"><i class="fa fa-gavel text-indigo-600"></i><span>Riwayat Tawaran Lelang</span></h3>
                            <p class="text-[11px] text-slate-500 truncate max-w-[280px]">{{ $auction->title }}</p>
                        </div>
                        <button type="button" @click="historyAuctionId = null" class="text-slate-400 hover:text-slate-600 p-1"><i class="fa fa-times text-sm"></i></button>
                    </div>
                    <div class="flex-grow overflow-y-auto space-y-2 pr-1 divide-y divide-slate-50">
                        @forelse($auction->bids as $index => $bid)
                            <div class="pt-2 pb-1 flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center font-black text-[10px] flex-shrink-0 {{ $index === 0 ? 'bg-amber-100 text-amber-800' : ($index === 1 ? 'bg-slate-200 text-slate-700' : ($index === 2 ? 'bg-amber-700/20 text-amber-900' : 'bg-slate-100 text-slate-500')) }}">
                                        @if($index === 0) 🥇 @elseif($index === 1) 🥈 @elseif($index === 2) 🥉 @else #{{ $index + 1 }} @endif
                                    </div>
                                    <img src="{{ $bid->user->avatar_url }}" alt="{{ $bid->user->name }}" class="w-7 h-7 rounded-full object-cover border border-slate-100">
                                    <div>
                                        <div class="font-bold text-slate-800 text-[11px] flex items-center gap-1">
                                            <span>{{ $bid->user->name }}</span>
                                            @if(auth()->check() && $bid->user_id === auth()->id())<span class="px-1 rounded text-[8px] font-bold bg-indigo-100 text-indigo-700">Anda</span>@endif
                                        </div>
                                        <span class="text-[9px] text-slate-400">{{ $bid->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="font-black text-xs {{ $index === 0 ? 'text-indigo-700 font-mono text-sm' : 'text-slate-700 font-mono' }}">Rp{{ number_format($bid->bid_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-slate-400 space-y-1"><i class="fa fa-hand-holding-dollar text-2xl text-slate-300"></i><p class="text-xs">Belum ada tawaran yang masuk untuk lelang ini.</p></div>
                        @endforelse
                    </div>
                    <div class="pt-2 border-t border-slate-100 text-right">
                        <button type="button" @click="historyAuctionId = null" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">Tutup</button>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- MODAL EDIT ATURAN -->
        <div x-show="showRulesModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="showRulesModal = false" class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-black text-slate-800 uppercase">Perbarui Aturan Komunitas</h3>
                    <button type="button" @click="showRulesModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times"></i></button>
                </div>
                <form action="{{ route('communities.rules.update', $community->id) }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    @method('PUT')
                    <div><textarea name="rules" rows="6" required class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs p-3 font-mono leading-relaxed">{{ $community->rules }}</textarea></div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showRulesModal = false" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold">Batal</button>
                        <button type="submit" class="px-4 py-1.5 rounded-xl bg-indigo-600 text-white font-bold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL EDIT KOMUNITAS -->
        @if(auth()->check() && ($community->user_id === auth()->id() || auth()->user()->isSuperAdmin()))
            <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/70 backdrop-blur-sm p-4 overflow-y-auto">
                <div @click.away="showEditModal = false" class="bg-white rounded-3xl max-w-lg w-full p-5 sm:p-6 shadow-2xl space-y-4 my-8">
                    <div class="flex justify-between pb-3 border-b border-slate-100">
                        <h3 class="font-black text-slate-900">Edit Komunitas</h3>
                        <button type="button" @click="showEditModal = false"><i class="fa fa-times"></i></button>
                    </div>
                    <form action="{{ route('communities.update', $community->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3.5 text-xs">
                        @csrf @method('PUT')
                        <input type="text" name="name" value="{{ $community->name }}" class="w-full rounded-xl bg-slate-50 border-slate-200">
                        <select name="category" class="w-full rounded-xl bg-slate-50 border-slate-200"><option value="otomotif" selected>Otomotif & Motor/Mobil</option></select>
                        <input type="file" name="logo" class="w-full">
                        <input type="file" name="banner" class="w-full">
                        <textarea name="description" class="w-full rounded-xl bg-slate-50 border-slate-200">{{ $community->description }}</textarea>
                        <div class="flex justify-end pt-2"><button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white font-bold">Simpan</button></div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>