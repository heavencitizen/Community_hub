<x-app-layout>
    <div x-data="{ 
        activeTab: '{{ request('tab', 'forum') }}', 
        showProductModal: false, 
        showAuctionModal: false, 
        historyAuctionId: null,
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
    }" class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">

        <!-- 1. Community Header Card with Clean Banner & Polished Structure -->
        <div class="rounded-3xl overflow-hidden bg-white border border-slate-200 shadow-sm">
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

                <!-- Row 2: Community Identity & Metadata (Full Width, 100% on White Background) -->
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
                    <i class="fa fa-comments"></i>
                    <span>Feed & Diskusi</span>
                </button>
                <button type="button" @click="activeTab = 'marketplace'" :class="activeTab === 'marketplace' ? 'text-indigo-600 border-indigo-600 bg-white' : 'text-slate-500 hover:text-slate-800 border-transparent'" class="py-2.5 px-3 border-b-2 transition flex items-center gap-1.5 rounded-t-lg flex-shrink-0">
                    <i class="fa fa-bag-shopping"></i>
                    <span>Jual Beli</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-indigo-100 text-indigo-700">{{ $community->products->count() }}</span>
                </button>
                <button type="button" @click="activeTab = 'auctions'" :class="activeTab === 'auctions' ? 'text-indigo-600 border-indigo-600 bg-white' : 'text-slate-500 hover:text-slate-800 border-transparent'" class="py-2.5 px-3 border-b-2 transition flex items-center gap-1.5 rounded-t-lg flex-shrink-0">
                    <i class="fa fa-gavel"></i>
                    <span>Lelang</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-indigo-100 text-indigo-700">{{ $community->auctions->count() }}</span>
                </button>
                <button type="button" @click="activeTab = 'members'" :class="activeTab === 'members' ? 'text-indigo-600 border-indigo-600 bg-white' : 'text-slate-500 hover:text-slate-800 border-transparent'" class="py-2.5 px-3 border-b-2 transition flex items-center gap-1.5 rounded-t-lg flex-shrink-0">
                    <i class="fa fa-user-group"></i>
                    <span>Anggota</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-slate-200 text-slate-700">{{ $community->members->count() }}</span>
                </button>
                <button type="button" @click="activeTab = 'rules'" :class="activeTab === 'rules' ? 'text-indigo-600 border-indigo-600 bg-white' : 'text-slate-500 hover:text-slate-800 border-transparent'" class="py-2.5 px-3 border-b-2 transition flex items-center gap-1.5 rounded-t-lg flex-shrink-0">
                    <i class="fa fa-scale-balanced"></i>
                    <span>Aturan & Sanksi</span>
                </button>
            </div>
        </div>

        <!-- 2. Main Content Tabs Container -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Left 8 Columns -->
            <div class="lg:col-span-8 space-y-4">

                <!-- ════════════════════════════════════════════════════════════════ -->
                <!-- TAB 1: FORUM & FEED TIMELINE -->
                <!-- ════════════════════════════════════════════════════════════════ -->
                <div x-show="activeTab === 'forum'" class="space-y-4">
                    <!-- Create Post Box -->
                    @auth
                        @if($isMember || auth()->user()->isSuperAdmin())
                            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3" 
                                 x-data="{ 
                                     openComposer: false, 
                                     fileName: '',
                                     mediaPreview: null,
                                     mediaType: null,
                                     handleFileSelect(e) {
                                         const file = e.target.files[0];
                                         if (!file) {
                                             this.clearMedia();
                                             return;
                                         }
                                         this.fileName = file.name;
                                         this.mediaType = file.type.startsWith('video') ? 'video' : 'image';
                                         if (this.mediaPreview) {
                                             URL.revokeObjectURL(this.mediaPreview);
                                         }
                                         this.mediaPreview = URL.createObjectURL(file);
                                     },
                                     clearMedia() {
                                         if (this.mediaPreview) {
                                             URL.revokeObjectURL(this.mediaPreview);
                                         }
                                         this.mediaPreview = null;
                                         this.mediaType = null;
                                         this.fileName = '';
                                         if ($refs.commFileInput) {
                                             $refs.commFileInput.value = '';
                                         }
                                     }
                                 }">
                                <div class="flex items-center gap-3">
                                    <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="w-9 h-9 rounded-xl object-cover border border-slate-200 flex-shrink-0">
                                    <button type="button" @click="openComposer = !openComposer" class="flex-grow text-left py-2.5 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200/80 text-slate-500 text-xs font-medium transition flex items-center justify-between">
                                        <span>Apa cerita, tips hobi, atau agenda di forum {{ explode(' ', $community->name)[0] }}?</span>
                                        <i class="fa fa-pen-to-square text-indigo-500 text-sm"></i>
                                    </button>
                                </div>

                                <!-- Expandable Composer Modal / Form -->
                                <div x-show="openComposer" x-cloak class="pt-3 border-t border-slate-100 space-y-3">
                                    <form action="{{ route('communities.posts.store', $community->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                        @csrf

                                        <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500">
                                            <i class="fa fa-users text-indigo-600"></i>
                                            <span>Posting ke: <strong class="text-indigo-600">{{ $community->name }}</strong></span>
                                        </div>

                                        <div>
                                            <textarea name="content" rows="3" required placeholder="Tuliskan cerita, ajakan mabar, tips hobi, atau info komunitas..." class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs p-3 focus:ring-indigo-500 focus:border-indigo-500 placeholder-slate-400 resize-none"></textarea>
                                        </div>

                                        <!-- Rich Visual Media Preview (Photo / Video) -->
                                        <div x-show="mediaPreview" x-cloak class="relative rounded-2xl overflow-hidden border border-slate-200 bg-slate-900 shadow-md max-h-64 flex items-center justify-center">
                                            <template x-if="mediaType === 'image'">
                                                <img :src="mediaPreview" alt="Pratinjau Foto" class="w-full max-h-64 object-cover rounded-2xl">
                                            </template>
                                            <template x-if="mediaType === 'video'">
                                                <video :src="mediaPreview" controls class="w-full max-h-64 rounded-2xl"></video>
                                            </template>

                                            <!-- Floating Badge & Cancel Button -->
                                            <div class="absolute top-2.5 left-2.5 right-2.5 flex items-center justify-between pointer-events-none">
                                                <span class="px-2.5 py-1 rounded-lg bg-slate-900/80 backdrop-blur text-white text-[10px] font-bold flex items-center gap-1.5 shadow">
                                                    <i class="fa-solid" :class="mediaType === 'video' ? 'fa-video text-indigo-400' : 'fa-image text-emerald-400'"></i>
                                                    <span x-text="fileName" class="max-w-[180px] truncate"></span>
                                                </span>
                                                <button type="button" @click="clearMedia()" class="pointer-events-auto p-1.5 rounded-full bg-slate-900/80 hover:bg-rose-600 text-white transition shadow" title="Batalkan berkas">
                                                    <i class="fa-solid fa-xmark text-xs"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-100">
                                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer transition">
                                                <i class="fa fa-image text-emerald-500 text-sm"></i>
                                                <span>Foto / Video</span>
                                                <input type="file" name="media" x-ref="commFileInput" accept="image/*,video/*" class="hidden" @change="handleFileSelect($event)">
                                            </label>

                                            <div class="flex items-center gap-2">
                                                <button type="button" @click="openComposer = false" class="px-3.5 py-1.5 rounded-xl text-slate-500 hover:bg-slate-100 text-xs font-bold transition">
                                                    Batal
                                                </button>
                                                <button type="submit" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                                                    <i class="fa fa-paper-plane text-[10px]"></i>
                                                    <span>Kirim Post</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="p-3.5 rounded-xl bg-indigo-50 border border-indigo-100 text-xs text-indigo-700 flex items-center justify-between gap-2">
                                <span>Ingin ikut berdiskusi dan posting? Bergabunglah dengan komunitas ini!</span>
                                <form action="{{ route('communities.join', $community->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition whitespace-nowrap">
                                        Gabung
                                    </button>
                                </form>
                            </div>
                        @endif
                    @else
                        <div class="p-3.5 rounded-xl bg-indigo-50 border border-indigo-100 text-xs text-indigo-700 flex items-center justify-between gap-2">
                            <span>Ingin menyapa anggota dan menulis status di forum?</span>
                            <button type="button" @click="triggerGuestModal('membuat postingan di forum')" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition whitespace-nowrap">
                                Masuk / Daftar
                            </button>
                        </div>
                    @endauth

                    <!-- Posts List -->
                    <div class="space-y-4">
                        @if($posts->isEmpty())
                            <div class="p-8 rounded-2xl bg-white border border-slate-200 text-center text-slate-500 space-y-1">
                                <i class="fa fa-comments text-2xl text-slate-300"></i>
                                <p class="text-sm font-bold text-slate-800">Belum ada postingan di forum ini.</p>
                                <p class="text-xs text-slate-400">Jadilah yang pertama menyapa anggota komunitas lainnya!</p>
                            </div>
                        @else
                            @foreach($posts as $post)
                                <x-post-card :post="$post" :show-community="false" />
                            @endforeach
                            <div class="mt-3 text-center">{{ $posts->links() }}</div>
                        @endif
                    </div>
                </div>

                <!-- ════════════════════════════════════════════════════════════════ -->
                <!-- TAB 2: JUAL BELI DI KOMUNITAS (MARKETPLACE) -->
                <!-- ════════════════════════════════════════════════════════════════ -->
                <div x-show="activeTab === 'marketplace'" class="space-y-4">
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-black text-slate-800 flex items-center gap-1.5">
                                <i class="fa fa-store text-emerald-600"></i>
                                <span>Etalase Jual Beli Komunitas</span>
                            </h2>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Fee aplikasi: <strong class="text-indigo-600">1% (Member)</strong> • <strong class="text-slate-600">2% (Umum)</strong>
                            </p>
                        </div>

                        <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
                            <a href="{{ route('marketplace.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                                <i class="fa fa-globe text-[11px] text-amber-500"></i>
                                <span>Semua Produk</span>
                            </a>

                            @if(auth()->check() && ($community->user_id === auth()->id() || auth()->user()->isSuperAdmin()))
                                <button type="button" @click="showProductModal = true" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5 active:scale-95">
                                    <i class="fa fa-plus text-[10px]"></i>
                                    <span>Tambah Produk</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Products Grid -->
                    @if($community->products->isEmpty())
                        <div class="p-8 rounded-2xl bg-white border border-slate-200 text-center text-slate-500 space-y-1">
                            <i class="fa fa-box-open text-3xl text-slate-300"></i>
                            <p class="text-sm font-bold text-slate-800">Belum ada barang yang dijual di komunitas ini.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($community->products as $product)
                                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:border-indigo-200 hover:shadow-md transition flex flex-col justify-between">
                                    <div class="h-36 bg-slate-100 relative overflow-hidden">
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        <div class="absolute top-2 right-2 px-2 py-0.5 rounded-full text-[9px] font-bold {{ $product->stock > 0 ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }}">
                                            {{ $product->stock > 0 ? 'Stok: ' . $product->stock : 'Habis' }}
                                        </div>
                                    </div>
                                    <div class="p-3.5 flex-grow flex flex-col justify-between space-y-3">
                                        <div class="space-y-0.5">
                                            <h3 class="font-bold text-slate-800 text-xs line-clamp-1">{{ $product->name }}</h3>
                                            <p class="text-sm font-black text-indigo-600">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                                            <p class="text-[11px] text-slate-500 line-clamp-2">{{ $product->description }}</p>
                                        </div>

                                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                                            <span class="text-[9px] text-slate-400">Fee: 1% / 2%</span>

                                            @auth
                                                <form action="{{ route('communities.products.buy', $product->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" {{ $product->stock <= 0 ? 'disabled' : '' }} class="px-3.5 py-1.5 rounded-lg {{ $product->stock > 0 ? 'bg-indigo-600 hover:bg-indigo-500 text-white' : 'bg-slate-200 text-slate-400 cursor-not-allowed' }} text-xs font-bold transition flex items-center gap-1">
                                                        <i class="fa fa-cart-shopping text-[10px]"></i>
                                                        <span>Beli</span>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" @click="triggerGuestModal('membeli produk ini')" class="px-3.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-1">
                                                    <i class="fa fa-cart-shopping text-[10px]"></i>
                                                    <span>Beli</span>
                                                </button>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- ════════════════════════════════════════════════════════════════ -->
                <!-- TAB 3: LELANG KOMUNITAS (AUCTION) -->
                <!-- ════════════════════════════════════════════════════════════════ -->
                <div x-show="activeTab === 'auctions'" class="space-y-4">
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-black text-slate-800 flex items-center gap-1.5">
                                <i class="fa fa-gavel text-indigo-600"></i>
                                <span>Arena Lelang Komunitas</span>
                            </h2>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Fee pemenang: <strong class="text-indigo-600">1% (Member)</strong> • <strong class="text-slate-600">2% (Umum)</strong> • Transaksi aman bergaransi komunitas
                            </p>
                        </div>

                        <div class="flex items-center gap-2 self-start sm:self-auto">
                            <a href="{{ route('auctions.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                                <i class="fa fa-globe text-[11px] text-slate-500"></i>
                                <span>Semua Lelang</span>
                            </a>

                            @if(auth()->check() && ($community->user_id === auth()->id() || auth()->user()->isSuperAdmin()))
                                <button type="button" @click="showAuctionModal = true" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                                    <i class="fa fa-plus text-[10px]"></i>
                                    <span>Buka Lelang</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Auctions List -->
                    @if($community->auctions->isEmpty())
                        <div class="p-8 sm:p-12 rounded-2xl bg-white border border-slate-200 text-center text-slate-500 space-y-2">
                            <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                                <i class="fa fa-gavel"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-800">Belum ada sesi lelang aktif di komunitas ini.</p>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto">Ketua komunitas dapat membuka sesi lelang untuk koleksi atau merchandise khusus.</p>
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
                                @endphp
                                <div id="auction-{{ $auction->id }}" class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4 transition hover:border-indigo-200">
                                    <div class="flex flex-col md:flex-row gap-4">
                                        
                                        <!-- Auction Image & Status Badge -->
                                        <div class="w-full md:w-56 h-48 md:h-auto bg-slate-100 rounded-xl overflow-hidden relative flex-shrink-0">
                                            <img src="{{ $auction->image_url }}" alt="{{ $auction->title }}" class="w-full h-full object-cover">
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
                                                        <span>PELUNASAN</span>
                                                    </span>
                                                @elseif($isCompleted)
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-indigo-600 text-white shadow flex items-center gap-1">
                                                        <i class="fa fa-check text-[9px]"></i>
                                                        <span>LUNAS & SELESAI</span>
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

                                            @if($auction->auction_code)
                                                <div class="absolute bottom-2.5 left-2.5">
                                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-mono font-bold bg-slate-900/80 backdrop-blur text-white shadow">
                                                        {{ $auction->auction_code }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Content & Bidding Controls -->
                                        <div class="flex-grow space-y-3 flex flex-col justify-between">
                                            <div class="space-y-1.5">
                                                
                                                <!-- Header: Title & Badges & Countdowns -->
                                                <div class="flex flex-wrap items-center justify-between gap-2">
                                                    <div class="space-y-0.5">
                                                        <div class="flex items-center gap-2">
                                                            <h3 class="text-base font-black text-slate-900">{{ $auction->title }}</h3>
                                                            @if($auction->anti_sniping)
                                                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[9px] font-bold flex items-center gap-1 flex-shrink-0" title="Anti-Sniping Aktif: Perpanjangan +2 menit jika ada penawaran di <120 detik terakhir">
                                                                    <i class="fa fa-shield-halved text-[8px]"></i>
                                                                    <span>Anti-Sniping</span>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Live Countdown Timer -->
                                                    @if($isLive && $auction->end_time)
                                                        <div 
                                                            x-data="{
                                                                endTime: new Date('{{ $auction->end_time->toISOString() }}').getTime(),
                                                                timeStr: '',
                                                                update() {
                                                                    const now = new Date().getTime();
                                                                    const diff = this.endTime - now;
                                                                    if (diff <= 0) {
                                                                        this.timeStr = 'Sesi Berakhir';
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
                                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-bold border border-slate-100"
                                                        >
                                                            <i class="fa fa-clock text-[11px] text-indigo-600"></i>
                                                            <span class="text-[10px] text-slate-500 font-medium">Sisa Waktu:</span>
                                                            <strong x-text="timeStr" class="font-mono font-black">...</strong>
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
                                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 text-xs font-bold border border-sky-100"
                                                        >
                                                            <i class="fa fa-calendar-clock text-[11px] text-sky-600"></i>
                                                            <span class="text-[10px] text-slate-500 font-medium">Dimulai Dalam:</span>
                                                            <strong x-text="timeStr" class="font-mono font-black">...</strong>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Visual Lifecycle Stepper -->
                                                <div class="py-1 flex items-center gap-1 sm:gap-2 text-[10px] font-bold overflow-x-auto no-scrollbar">
                                                    <span class="flex items-center gap-1 {{ $isScheduled ? 'text-sky-600 font-extrabold' : 'text-slate-400' }}">
                                                        <i class="fa {{ $isScheduled ? 'fa-circle-dot text-sky-500' : 'fa-circle-check text-emerald-500' }} text-[9px]"></i>
                                                        <span>1. Terjadwal</span>
                                                    </span>
                                                    <span class="text-slate-300">&rarr;</span>
                                                    <span class="flex items-center gap-1 {{ $isLive ? 'text-indigo-600 font-extrabold' : ($isScheduled ? 'text-slate-400' : 'text-emerald-600') }}">
                                                        <i class="fa {{ $isLive ? 'fa-gavel text-indigo-600 animate-pulse' : ($isScheduled ? 'fa-circle text-slate-300' : 'fa-circle-check text-emerald-500') }} text-[9px]"></i>
                                                        <span>2. Open Bidding</span>
                                                    </span>
                                                    <span class="text-slate-300">&rarr;</span>
                                                    <span class="flex items-center gap-1 {{ $isAwaiting ? 'text-amber-600 font-extrabold' : ($isCompleted ? 'text-emerald-600' : 'text-slate-400') }}">
                                                        <i class="fa {{ $isAwaiting ? 'fa-hourglass-half text-amber-500' : ($isCompleted ? 'fa-circle-check text-emerald-500' : 'fa-circle text-slate-300') }} text-[9px]"></i>
                                                        <span>3. Pelunasan</span>
                                                    </span>
                                                    <span class="text-slate-300">&rarr;</span>
                                                    <span class="flex items-center gap-1 {{ $isCompleted ? 'text-emerald-600 font-extrabold' : ($isWanprestasi ? 'text-rose-600 font-extrabold' : 'text-slate-400') }}">
                                                        <i class="fa {{ $isCompleted ? 'fa-circle-check text-emerald-500' : ($isWanprestasi ? 'fa-triangle-exclamation text-rose-500' : 'fa-circle text-slate-300') }} text-[9px]"></i>
                                                        <span>4. {{ $isWanprestasi ? 'Wanprestasi' : 'Selesai' }}</span>
                                                    </span>
                                                </div>

                                                <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">{{ $auction->description ?: 'Barang koleksi eksklusif persembahan komunitas.' }}</p>

                                                <div class="flex flex-wrap items-center gap-3 text-[11px] text-slate-500 pt-1">
                                                    <div>Nilai Awal: <strong class="text-slate-700">Rp{{ number_format($auction->starting_price, 0, ',', '.') }}</strong></div>
                                                    <div>Kelipatan: <strong class="text-indigo-600">+Rp{{ number_format($auction->bid_increment, 0, ',', '.') }}</strong></div>
                                                    <div>Jadwal Mulai: <strong class="text-slate-700">{{ $auction->start_time ? $auction->start_time->format('d M Y, H:i') . ' WIB' : '-' }}</strong></div>
                                                    <div>Batas Lelang: <strong class="text-slate-700">{{ $auction->end_time ? $auction->end_time->format('d M Y, H:i') . ' WIB' : '-' }}</strong></div>
                                                </div>
                                            </div>

                                            <!-- Highest Bid / Current Status Box with Real-Time Auto-Polling -->
                                            <div 
                                                x-data="{
                                                    currentPrice: {{ (float) $auction->current_price }},
                                                    formattedPrice: 'Rp{{ number_format($auction->current_price, 0, ',', '.') }}',
                                                    leaderName: '{{ $auction->winner ? $auction->winner->name : '' }}',
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
                                                            })
                                                            .catch(() => {});
                                                        @endif
                                                    },
                                                    init() {
                                                        @if($isLive)
                                                        setInterval(() => this.poll(), 6000);
                                                        @endif
                                                    }
                                                }"
                                                class="p-3.5 rounded-2xl bg-indigo-50/80 border transition-all duration-300 space-y-3"
                                                :class="isNewBid ? 'border-emerald-400 ring-2 ring-emerald-400/50 bg-emerald-50/60' : 'border-slate-100'"
                                            >
                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                                    <div>
                                                        <span class="text-[9px] uppercase font-extrabold text-indigo-700 tracking-wider block">
                                                            {{ $isLive ? 'Tawaran Tertinggi Saat Ini' : ($isScheduled ? 'Nilai Pembukaan' : 'Pokok Lelang Terbentuk') }}
                                                        </span>
                                                        <p class="text-xl sm:text-2xl font-black text-slate-900 flex items-center gap-2">
                                                            <span x-text="formattedPrice">Rp{{ number_format($auction->current_price, 0, ',', '.') }}</span>
                                                            <span x-show="isNewBid" x-cloak class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-500 text-white animate-bounce shadow-sm">Bid Baru!</span>
                                                        </p>
                                                    </div>

                                                    <!-- Bidder Spotlight, History Button & Share Button -->
                                                    <div class="flex items-center gap-2">
                                                        <template x-if="leaderName">
                                                            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white border border-slate-100 shadow-sm text-xs">
                                                                <img :src="leaderAvatar" :alt="leaderName" class="w-6 h-6 rounded-full object-cover border border-slate-200">
                                                                <div>
                                                                    <div class="text-[9px] text-slate-400 font-bold leading-none">
                                                                        {{ $isLive ? 'Memimpin:' : 'Pemenang:' }}
                                                                    </div>
                                                                    <div class="font-black text-slate-800 text-[11px] flex items-center gap-1">
                                                                        <span x-text="leaderName">{{ $auction->winner ? $auction->winner->name : '' }}</span>
                                                                        @if($isWinner)
                                                                            <span class="px-1.5 py-0.2 rounded text-[8px] font-black bg-emerald-100 text-emerald-700 uppercase">Anda 👑</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>

                                                        <!-- Share Link Button with Toast Dispatch -->
                                                        <button 
                                                            type="button" 
                                                            @click="
                                                                navigator.clipboard.writeText('{{ route('communities.show', $community->slug) }}#auction-{{ $auction->id }}');
                                                                $dispatch('toast', { type: 'success', message: 'Tautan lelang berhasil disalin ke clipboard!' });
                                                            "
                                                            class="px-2.5 py-2 rounded-xl bg-white hover:bg-indigo-100 border border-slate-200 text-indigo-700 text-xs font-bold shadow-sm transition flex items-center gap-1"
                                                            title="Salin tautan lelang"
                                                        >
                                                            <i class="fa-solid fa-share-nodes text-[11px]"></i>
                                                            <span class="hidden sm:inline">Bagikan</span>
                                                        </button>

                                                        <button 
                                                            type="button" 
                                                            @click="historyAuctionId = {{ $auction->id }}" 
                                                            class="px-3 py-2 rounded-xl bg-white hover:bg-indigo-100 border border-slate-200 text-indigo-700 text-xs font-bold shadow-sm transition flex items-center gap-1.5"
                                                        >
                                                            <i class="fa fa-list-ol text-[10px]"></i>
                                                            <span>Riwayat (<span x-text="totalBids">{{ $auction->bids->count() }}</span>)</span>
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Action Area: Scheduled, Live Bidding, Payment Deadline, or Certificate -->
                                                @if($isScheduled)
                                                    <div class="p-3 rounded-xl bg-sky-50 border border-sky-100 flex items-center justify-between text-xs text-sky-900">
                                                        <span class="flex items-center gap-2 font-medium">
                                                            <i class="fa fa-calendar text-sky-600"></i>
                                                            <span>Sesi open bidding baru akan dibuka pada <strong>{{ $auction->start_time->format('d M Y, H:i') }} WIB</strong>.</span>
                                                        </span>
                                                    </div>

                                                @elseif($isLive)
                                                    @auth
                                                        @if($auction->user_id === auth()->id())
                                                            <div class="text-[11px] text-slate-500 bg-white/80 p-2.5 rounded-xl border border-slate-100 flex items-center justify-between">
                                                                <span class="flex items-center gap-1.5 font-medium">
                                                                    <i class="fa fa-info-circle text-indigo-600"></i>
                                                                    <span>Anda adalah penyelenggara lelang ini.</span>
                                                                </span>
                                                                <form action="{{ route('communities.auctions.close', $auction->id) }}" method="POST" onsubmit="return confirm('Tutup sesi lelang sekarang dan tetapkan penawar tertinggi sebagai pemenang?')">
                                                                    @csrf
                                                                    <button type="submit" class="px-3 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-[11px] font-bold shadow-sm transition">
                                                                        Tutup Sesi & Tetapkan Pemenang
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @else
                                                            <div class="space-y-2 pt-1 border-t border-slate-100">
                                                                <form action="{{ route('communities.auctions.bid', $auction->id) }}" method="POST" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                                                    @csrf
                                                                    <div class="relative flex-grow">
                                                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs font-bold">Rp</span>
                                                                        <input 
                                                                            type="number" 
                                                                            name="bid_amount" 
                                                                            x-model="bidVal"
                                                                            :min="minNextBid" 
                                                                            step="{{ $auction->bid_increment }}" 
                                                                            class="w-full pl-9 pr-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-xs font-bold py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm"
                                                                            :placeholder="minNextBid"
                                                                        >
                                                                    </div>
                                                                    
                                                                    <div class="flex items-center gap-1.5">
                                                                        <!-- Quick Preset Buttons -->
                                                                        <button 
                                                                            type="button" 
                                                                            @click="bidVal = minNextBid" 
                                                                            class="px-2 py-2 rounded-xl bg-white hover:bg-indigo-100 border border-slate-200 text-indigo-700 text-[10px] font-bold transition flex-shrink-0"
                                                                            title="Pasang tawaran minimal"
                                                                        >
                                                                            +{{ number_format($auction->bid_increment, 0, ',', '.') }}
                                                                        </button>
                                                                        <button 
                                                                            type="button" 
                                                                            @click="bidVal = Number(minNextBid) + Number({{ $auction->bid_increment }})" 
                                                                            class="px-2 py-2 rounded-xl bg-white hover:bg-indigo-100 border border-slate-200 text-indigo-700 text-[10px] font-bold transition flex-shrink-0"
                                                                            title="Pasang 2x kelipatan"
                                                                        >
                                                                            +{{ number_format($auction->bid_increment * 2, 0, ',', '.') }}
                                                                        </button>

                                                                        <button 
                                                                            type="submit" 
                                                                            class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-black shadow-md shadow-indigo-200 transition flex items-center justify-center gap-1.5 flex-shrink-0"
                                                                        >
                                                                            <i class="fa fa-gavel text-[10px]"></i>
                                                                            <span>Pasang Bid</span>
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                                <p class="text-[10px] text-indigo-700 font-medium flex items-center justify-between">
                                                                    <span>Tawaran minimal berikutnya: <strong class="font-bold" x-text="'Rp' + Number(minNextBid).toLocaleString('id-ID')">Rp{{ number_format($minNextBid, 0, ',', '.') }}</strong></span>
                                                                    @if($isLive)
                                                                        <span class="text-[9px] text-emerald-600 flex items-center gap-1 font-semibold">
                                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                                            <span>Live Auto-Update</span>
                                                                        </span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-white border border-slate-100">
                                                            <span class="text-xs text-slate-600">Ingin mengajukan penawaran lelang ini?</span>
                                                            <button type="button" @click="triggerGuestModal('mengikuti lelang ini')" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition">
                                                                Masuk untuk Bid
                                                            </button>
                                                        </div>
                                                    @endauth

                                                @elseif($isAwaiting)
                                                    <!-- Menunggu Pembayaran Pemenang & Tenggat Pelunasan -->
                                                    <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 space-y-3">
                                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                                            <div class="space-y-0.5">
                                                                <span class="text-xs font-bold text-amber-900 flex items-center gap-1.5">
                                                                    <i class="fa fa-trophy text-amber-600"></i>
                                                                    <span>Pemenang Ditetapkan (Menunggu Pelunasan)</span>
                                                                </span>
                                                                <p class="text-[11px] text-amber-800">
                                                                    Pemenang: <strong>{{ $auction->winner->name ?? 'Tidak ada pemenang' }}</strong> (Rp{{ number_format($auction->current_price, 0, ',', '.') }})
                                                                </p>
                                                                @if($auction->runnerUp)
                                                                    <p class="text-[10px] text-amber-700">
                                                                        Pemenang Cadangan (Runner-up): <strong>{{ $auction->runnerUp->name }}</strong> (Rp{{ number_format($auction->runner_up_bid, 0, ',', '.') }})
                                                                    </p>
                                                                @endif
                                                            </div>

                                                            <!-- Payment Deadline Countdown -->
                                                            @if($auction->payment_deadline)
                                                                <div class="px-2.5 py-1 rounded-lg bg-white border border-amber-200 text-amber-900 text-[10px] font-bold flex items-center gap-1.5 self-start sm:self-auto">
                                                                    <i class="fa fa-hourglass-half text-amber-500"></i>
                                                                    <span>Batas Bayar: <strong>{{ $auction->payment_deadline->format('d M, H:i') }} WIB</strong></span>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-amber-200/70">
                                                            <div class="flex items-center gap-2">
                                                                <a href="{{ route('communities.auctions.certificate', $auction->id) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white hover:bg-indigo-50 text-indigo-700 border border-slate-200 text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                                                                    <i class="fa fa-file-invoice text-indigo-600 text-[11px]"></i>
                                                                    <span>Kutipan Hasil Lelang</span>
                                                                </a>
                                                            </div>

                                                            <div class="flex items-center gap-2">
                                                                @if($isWinner)
                                                                    <form action="{{ route('communities.auctions.checkout', $auction->id) }}" method="POST">
                                                                        @csrf
                                                                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-md shadow-emerald-200 transition flex items-center gap-1.5 animate-bounce">
                                                                            <i class="fa fa-credit-card"></i>
                                                                            <span>Bayar Sekarang &rarr;</span>
                                                                        </button>
                                                                    </form>
                                                                @elseif($isOwner)
                                                                    <form action="{{ route('communities.auctions.checkout', $auction->id) }}" method="POST">
                                                                        @csrf
                                                                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition">
                                                                            Bantu Checkout Pemenang
                                                                        </button>
                                                                    </form>

                                                                    <form action="{{ route('communities.auctions.wanprestasi', $auction->id) }}" method="POST" onsubmit="return confirm('Peringatan: Nyatakan pemenang lelang ini wanprestasi? Jika terdapat penawar cadangan (runner-up), hak lelang akan dialihkan kepadanya.')">
                                                                        @csrf
                                                                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition flex items-center gap-1.5">
                                                                            <i class="fa fa-triangle-exclamation text-[10px]"></i>
                                                                            <span>Gugurkan (Wanprestasi)</span>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                @elseif($isCompleted)
                                                    <!-- Lunas & Selesai -->
                                                    <div class="p-3.5 rounded-xl bg-indigo-50 border border-indigo-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                                        <div class="space-y-0.5">
                                                            <span class="text-xs font-bold text-indigo-900 flex items-center gap-1.5">
                                                                <i class="fa fa-circle-check text-emerald-500 text-sm"></i>
                                                                <span>Lelang Berhasil Diselesaikan & Lunas</span>
                                                            </span>
                                                            <p class="text-[11px] text-indigo-700 font-medium">
                                                                Pemenang Resmi: <strong>{{ $auction->winner->name ?? '-' }}</strong> (Rp{{ number_format($auction->current_price, 0, ',', '.') }})
                                                            </p>
                                                        </div>

                                                        <a href="{{ route('communities.auctions.certificate', $auction->id) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm transition flex items-center justify-center gap-1.5 self-start sm:self-auto">
                                                            <i class="fa fa-file-invoice text-xs"></i>
                                                            <span>Lihat Kutipan Hasil Lelang</span>
                                                        </a>
                                                    </div>

                                                @elseif($isWanprestasi)
                                                    <!-- Wanprestasi Status -->
                                                    <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                                        <div class="space-y-0.5">
                                                            <span class="text-xs font-bold text-rose-900 flex items-center gap-1.5">
                                                                <i class="fa fa-triangle-exclamation text-rose-600"></i>
                                                                <span>Lelang Ditutup Karena Wanprestasi</span>
                                                            </span>
                                                            <p class="text-[11px] text-rose-700">
                                                                Pemenang utama tidak melakukan pelunasan dalam batas waktu yang ditentukan.
                                                            </p>
                                                        </div>

                                                        @if($auction->winner_id)
                                                            <a href="{{ route('communities.auctions.certificate', $auction->id) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                                                                <i class="fa fa-file-invoice text-[10px]"></i>
                                                                <span>Catatan Hasil Lelang</span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Admin Controls: Cancel Button -->
                                            @if($isOwner && $auction->status !== 'completed' && $auction->status !== 'cancelled')
                                                <div class="flex items-center justify-end gap-2 pt-1">
                                                    @if($isLive)
                                                        <form action="{{ route('communities.auctions.close', $auction->id) }}" method="POST" onsubmit="return confirm('Tutup lelang sekarang dan tetapkan pemenang?')">
                                                            @csrf
                                                            <button type="submit" class="text-[11px] font-bold text-indigo-600 hover:underline">
                                                                Tutup Sesi Manual
                                                            </button>
                                                        </form>
                                                        <span class="text-slate-300">•</span>
                                                    @endif
                                                    <form action="{{ route('communities.auctions.cancel', $auction->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan program lelang ini?')">
                                                        @csrf
                                                        <button type="submit" class="text-[11px] font-bold text-rose-500 hover:underline">
                                                            Batalkan Lelang
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- ════════════════════════════════════════════════════════════════ -->
                <!-- TAB 4: DAFTAR ANGGOTA KOMUNITAS & SANKSI MODERASI -->
                <!-- ════════════════════════════════════════════════════════════════ -->
                <div x-show="activeTab === 'members'" class="space-y-4">
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
                        <h2 class="text-sm font-black text-slate-800 flex items-center gap-1.5">
                            <i class="fa fa-users text-indigo-600"></i>
                            <span>Daftar Anggota Terdaftar ({{ $community->members->count() }})</span>
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">Semua anggota dapat melihat profil dan tanggal bergabung sesama anggota.</p>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm divide-y divide-slate-100 overflow-hidden text-xs">
                        @foreach($community->members as $member)
                            <div class="p-3.5 flex items-center justify-between gap-3 hover:bg-slate-50 transition">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('users.show', $member->user->username) }}" class="flex-shrink-0 group">
                                        <img src="{{ $member->user->avatar_url }}" alt="{{ $member->user->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 group-hover:ring-2 group-hover:ring-indigo-400 transition">
                                    </a>
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <h4 class="font-bold text-slate-800 text-xs">
                                                <a href="{{ route('users.show', $member->user->username) }}" class="hover:text-indigo-600 transition">{{ $member->user->name }}</a>
                                            </h4>
                                            <span class="text-[10px] text-slate-400 font-mono">{{ '@' . $member->user->username }}</span>
                                            @if($member->user_id === $community->user_id)
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold bg-amber-100 text-amber-700">Ketua</span>
                                            @elseif($member->role === 'moderator')
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold bg-indigo-100 text-indigo-700">Moderator</span>
                                            @endif

                                            @if($member->status === 'suspended')
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-rose-100 text-rose-700">Dinonaktifkan</span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-slate-400 mt-0.5">
                                            <i class="fa fa-calendar-check text-slate-400 mr-1"></i>Bergabung: <strong>{{ $member->joined_at ? $member->joined_at->format('d M Y') : $member->created_at->format('d M Y') }}</strong>
                                        </p>
                                    </div>
                                </div>

                                @if(auth()->check() && ($community->user_id === auth()->id() || auth()->user()->isSuperAdmin()) && $member->user_id !== $community->user_id)
                                    <div>
                                        @if($member->status === 'active')
                                            <form action="{{ route('communities.members.updateStatus', [$community->id, $member->id]) }}" method="POST" onsubmit="return confirm('Nonaktifkan anggota ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="suspended">
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-[10px] font-bold border border-rose-200 transition">
                                                    Nonaktifkan
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('communities.members.updateStatus', [$community->id, $member->id]) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="active">
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 text-[10px] font-bold border border-emerald-200 transition">
                                                    Aktifkan
                                                </button>
                                            </form>
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
                <div x-show="activeTab === 'rules'" class="space-y-4">
                    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="text-sm font-black text-slate-800 flex items-center gap-1.5">
                                <i class="fa fa-scale-balanced text-indigo-600"></i>
                                <span>Aturan & Kebijakan Komunitas</span>
                            </h3>

                            @if(auth()->check() && ($community->user_id === auth()->id() || auth()->user()->isSuperAdmin()))
                                <button type="button" @click="showRulesModal = true" class="px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-bold border border-indigo-200 transition">
                                    <i class="fa fa-pen mr-1 text-[10px]"></i> Edit Aturan
                                </button>
                            @endif
                        </div>

                        <div class="prose prose-sm text-slate-700 leading-relaxed whitespace-pre-line text-xs bg-slate-50 p-4 rounded-xl border border-slate-200">
                            {{ $community->rules ?? "1. Saling menghormati sesama anggota komunitas.\n2. Dilarang keras memposting ujaran kebencian, spam, atau konten tidak senonoh.\n3. Transaksi jual beli dan lelang harus dilakukan dengan jujur.\n4. Pelanggaran aturan akan dikenakan sanksi penonaktifan akun oleh Ketua Komunitas." }}
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right 4 Columns Sidebar -->
            <div class="lg:col-span-4 space-y-4 sticky top-20">
                <!-- About Community Card -->
                <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-2.5 shadow-sm text-xs">
                    <h3 class="text-[11px] font-extrabold text-indigo-600 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa fa-circle-info"></i> Tentang Komunitas
                    </h3>
                    <p class="text-slate-600 leading-relaxed whitespace-pre-line">{{ $community->description }}</p>
                </div>

                <!-- Event Komunitas -->
                <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-3 shadow-sm text-xs">
                    <div class="flex items-center justify-between">
                        <h3 class="text-[11px] font-extrabold text-indigo-600 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa fa-calendar-days"></i> Agenda & Event
                        </h3>
                        @if(auth()->check() && ($community->user_id === auth()->id() || auth()->user()->isSuperAdmin()))
                            <a href="{{ route('events.create') }}" class="text-[11px] text-indigo-600 hover:underline font-bold">+ Buat</a>
                        @endif
                    </div>
                    @forelse($community->events as $ev)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-black uppercase {{ $ev->price == 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-100 text-indigo-700' }}">
                                    {{ $ev->price == 0 ? 'Gratis' : 'Rp' . number_format($ev->price, 0, ',', '.') }}
                                </span>
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
        <!-- POP-UP BAGIKAN KE PIHAK EKSTERNAL (OFFICIAL BRAND LOGOS) -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <div x-show="openShareModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="openShareModal = false" class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-black text-slate-800 flex items-center gap-1.5 uppercase tracking-wider">
                        <i class="fa fa-share-nodes text-indigo-600"></i>
                        <span>Bagikan ke Sosial Media</span>
                    </h3>
                    <button type="button" @click="openShareModal = false" class="text-slate-400 hover:text-slate-600">
                        <i class="fa fa-times text-xs"></i>
                    </button>
                </div>

                <div class="grid grid-cols-3 gap-2.5 text-center">
                    <!-- WhatsApp -->
                    <a :href="'https://api.whatsapp.com/send?text=' + encodeURIComponent(shareTitle + ' ' + shareUrl)" target="_blank" class="p-3 rounded-xl bg-slate-50 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-200 transition flex flex-col items-center gap-1.5 group">
                        <x-brand-logo name="whatsapp" class="h-7 w-auto" />
                        <span class="text-[10px] font-bold text-slate-700 group-hover:text-emerald-600">WhatsApp</span>
                    </a>

                    <!-- Instagram -->
                    <button type="button" @click="navigator.clipboard.writeText(shareUrl); alert('Tautan berhasil disalin! Silakan bagikan di Story Instagram Anda 📸')" class="p-3 rounded-xl bg-slate-50 hover:bg-pink-50 border border-slate-200 hover:border-pink-200 transition flex flex-col items-center gap-1.5 group">
                        <x-brand-logo name="instagram" class="h-7 w-auto" />
                        <span class="text-[10px] font-bold text-slate-700 group-hover:text-pink-600">Instagram</span>
                    </button>

                    <!-- Twitter / X -->
                    <a :href="'https://twitter.com/intent/tweet?text=' + encodeURIComponent(shareTitle) + '&url=' + encodeURIComponent(shareUrl)" target="_blank" class="p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 transition flex flex-col items-center gap-1.5 group">
                        <x-brand-logo name="twitter" class="h-7 w-auto" />
                        <span class="text-[10px] font-bold text-slate-700">Twitter (X)</span>
                    </a>

                    <!-- TikTok -->
                    <button type="button" @click="navigator.clipboard.writeText(shareUrl); alert('Tautan disalin ke clipboard! Siap ditempel di TikTok 🎵')" class="p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 transition flex flex-col items-center gap-1.5 group">
                        <x-brand-logo name="tiktok" class="h-7 w-auto" />
                        <span class="text-[10px] font-bold text-slate-700">TikTok</span>
                    </button>

                    <!-- Facebook -->
                    <a :href="'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(shareUrl)" target="_blank" class="p-3 rounded-xl bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-200 transition flex flex-col items-center gap-1.5 group">
                        <x-brand-logo name="facebook" class="h-7 w-auto" />
                        <span class="text-[10px] font-bold text-slate-700 group-hover:text-blue-600">Facebook</span>
                    </a>

                    <!-- Telegram -->
                    <a :href="'https://t.me/share/url?url=' + encodeURIComponent(shareUrl) + '&text=' + encodeURIComponent(shareTitle)" target="_blank" class="p-3 rounded-xl bg-slate-50 hover:bg-sky-50 border border-slate-200 hover:border-sky-200 transition flex flex-col items-center gap-1.5 group">
                        <x-brand-logo name="telegram" class="h-7 w-auto" />
                        <span class="text-[10px] font-bold text-slate-700 group-hover:text-sky-600">Telegram</span>
                    </a>
                </div>

                <div class="pt-1">
                    <button type="button" @click="navigator.clipboard.writeText(shareUrl); alert('Tautan berhasil disalin! 📋')" class="w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center gap-1.5 transition">
                        <i class="fa fa-link text-[10px]"></i>
                        <span>Salin Tautan</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- POP-UP GUEST AUTH PROMPT -->
        <div x-show="showGuestModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="showGuestModal = false" class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl text-center space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto text-2xl">
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>
                
                <div class="space-y-1">
                    <h3 class="text-base font-black text-slate-800">Ingin Berinteraksi di Forum?</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Untuk <span class="font-bold text-indigo-600" x-text="guestModalAction"></span>, silakan masuk ke akun Anda atau daftar gratis di <strong>CommunityHub</strong>!
                    </p>
                </div>

                <div class="space-y-1.5 pt-1">
                    <a href="{{ route('register') }}" class="block w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md transition">
                        Daftar Akun Baru (Gratis)
                    </a>
                    <a href="{{ route('login') }}" class="block w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        Sudah punya akun? Masuk
                    </a>
                </div>
            </div>
        </div>

        <!-- MODAL TAMBAH PRODUK -->
        <div x-show="showProductModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="showProductModal = false" class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-black text-slate-800 uppercase">Tambah Produk Jual Beli</h3>
                    <button type="button" @click="showProductModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times"></i></button>
                </div>

                <form action="{{ route('communities.products.store', $community->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-0.5">Nama Produk</label>
                        <input type="text" name="name" required placeholder="Contoh: Jersey Running 2026" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-0.5">Harga (Rp)</label>
                            <input type="number" name="price" required min="1000" placeholder="150000" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-0.5">Stok</label>
                            <input type="number" name="stock" required min="1" value="10" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-0.5">Deskripsi</label>
                        <textarea name="description" rows="2" placeholder="Detail produk..." class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3"></textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-0.5">Foto Produk</label>
                        <input type="file" name="image" accept="image/*" class="text-[11px] text-slate-500">
                    </div>

                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="showProductModal = false" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold">Batal</button>
                        <button type="submit" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL BUKA LELANG -->
        <div x-show="showAuctionModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="showAuctionModal = false" class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-black text-slate-800 uppercase">Buka Lelang Baru</h3>
                    <button type="button" @click="showAuctionModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times"></i></button>
                </div>

                <form action="{{ route('communities.auctions.store', $community->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-0.5">Judul Barang</label>
                        <input type="text" name="title" required placeholder="Contoh: Helm Vintage Retro" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-0.5">Harga Awal (Rp)</label>
                            <input type="number" name="starting_price" required min="10000" placeholder="100000" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-0.5">Kelipatan (Rp)</label>
                            <input type="number" name="bid_increment" required min="5000" value="10000" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-0.5">Jadwal Mulai</label>
                            <input type="datetime-local" name="start_time" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3">
                            <span class="text-[9px] text-slate-400">Kosongkan jika langsung live</span>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-0.5">Waktu Selesai</label>
                            <input type="datetime-local" name="end_time" required class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-0.5">Catatan Serah Terima / Ketentuan (Opsional)</label>
                        <textarea name="notes" rows="2" placeholder="Contoh: COD sekitaran Padang Baru, atau via ekspedisi terasuransi." class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3"></textarea>
                    </div>

                    <div class="p-2.5 rounded-xl bg-indigo-50/70 border border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="block font-bold text-slate-900 text-xs">Perlindungan Anti-Sniping</span>
                            <span class="text-[10px] text-indigo-700">Perpanjang +2 menit jika ada penawaran di <120 detik terakhir</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="anti_sniping" value="1" checked class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-0.5">Foto Barang</label>
                        <input type="file" name="image" accept="image/*" class="text-[11px] text-slate-500">
                    </div>

                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="showAuctionModal = false" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold">Batal</button>
                        <button type="submit" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-sm">Buka Lelang</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL RIWAYAT PENAWARAN LELANG (BID HISTORY) -->
        @foreach($community->auctions as $auction)
            <div x-show="historyAuctionId === {{ $auction->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
                <div @click.away="historyAuctionId = null" class="bg-white rounded-3xl max-w-md w-full p-5 shadow-2xl space-y-4 max-h-[85vh] flex flex-col">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 flex items-center gap-1.5">
                                <i class="fa fa-gavel text-indigo-600"></i>
                                <span>Riwayat Tawaran Lelang</span>
                            </h3>
                            <p class="text-[11px] text-slate-500 truncate max-w-[280px]">{{ $auction->title }}</p>
                        </div>
                        <button type="button" @click="historyAuctionId = null" class="text-slate-400 hover:text-slate-600 p-1">
                            <i class="fa fa-times text-sm"></i>
                        </button>
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
                                            @if(auth()->check() && $bid->user_id === auth()->id())
                                                <span class="px-1 rounded text-[8px] font-bold bg-indigo-100 text-indigo-700">Anda</span>
                                            @endif
                                        </div>
                                        <span class="text-[9px] text-slate-400">{{ $bid->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="font-black text-xs {{ $index === 0 ? 'text-indigo-700 font-mono text-sm' : 'text-slate-700 font-mono' }}">
                                        Rp{{ number_format($bid->bid_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-slate-400 space-y-1">
                                <i class="fa fa-hand-holding-dollar text-2xl text-slate-300"></i>
                                <p class="text-xs">Belum ada tawaran yang masuk untuk lelang ini.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="pt-2 border-t border-slate-100 text-right">
                        <button type="button" @click="historyAuctionId = null" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- MODAL EDIT ATURAN -->
        <div x-show="showRulesModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="showRulesModal = false" class="bg-white rounded-2xl max-w-md w-full p-5 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-black text-slate-800 uppercase">Perbarui Aturan Komunitas</h3>
                    <button type="button" @click="showRulesModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times"></i></button>
                </div>

                <form action="{{ route('communities.rules.update', $community->id) }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    @method('PUT')
                    <div>
                        <textarea name="rules" rows="6" required class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs p-3 font-mono leading-relaxed">{{ $community->rules }}</textarea>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showRulesModal = false" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold">Batal</button>
                        <button type="submit" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!-- MODAL EDIT & KELOLA KOMUNITAS (KHUSUS KETUA KOMUNITAS) -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        @if(auth()->check() && ($community->user_id === auth()->id() || auth()->user()->isSuperAdmin()))
            <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 backdrop-blur-sm p-4 overflow-y-auto">
                <div @click.away="showEditModal = false" class="bg-white rounded-3xl max-w-lg w-full p-5 sm:p-6 shadow-2xl space-y-4 my-8">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-sm">
                                <i class="fa fa-pen-to-square"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-900">Edit & Kelola Komunitas</h3>
                                <p class="text-[10px] text-slate-400">Hak Akses Khusus Ketua Komunitas</p>
                            </div>
                        </div>
                        <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                            <i class="fa fa-times text-sm"></i>
                        </button>
                    </div>

                    <form action="{{ route('communities.update', $community->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3.5 text-xs">
                        @csrf
                        @method('PUT')

                        <!-- 1. Nama Komunitas / Grup -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nama Komunitas / Grup</label>
                            <input type="text" name="name" value="{{ old('name', $community->name) }}" required class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3 focus:ring-indigo-500 focus:bg-white transition font-bold text-slate-800">
                        </div>

                        <!-- 2. Kategori Komunitas -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Kategori Bidang</label>
                            <select name="category" required class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs py-2 px-3 focus:ring-indigo-500 focus:bg-white transition text-slate-800">
                                @php
                                    $categories = [
                                        'olahraga' => 'Olahraga & Kebugaran',
                                        'otomotif' => 'Otomotif & Motor/Mobil',
                                        'hobi' => 'Hobi, Kreatif & Kerajinan',
                                        'seni_musik' => 'Seni, Musik & Budaya',
                                        'kuliner' => 'Kuliner & Masakan',
                                        'teknologi' => 'Teknologi, Coding & Game',
                                        'sosial_amal' => 'Sosial, Kemanusiaan & Amal',
                                        'keluarga' => 'Rumah Tangga & Parenting',
                                        'bisnis' => 'Bisnis, UMKM & Karir',
                                    ];
                                @endphp
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ (old('category', $community->category) === $key) ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 3. Foto Profil / Logo Komunitas -->
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <label class="block font-bold text-slate-700">Foto Profil / Logo Komunitas</label>
                            <div class="flex items-center gap-3">
                                <img src="{{ $community->logo_url }}" alt="Logo Saat Ini" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-sm flex-shrink-0">
                                <div class="flex-grow">
                                    <input type="file" name="logo" accept="image/*" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                    <p class="text-[9px] text-slate-400 mt-0.5">Format: JPG, PNG, WEBP. Maks 5MB.</p>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Banner Sampul Komunitas -->
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <label class="block font-bold text-slate-700">Banner Sampul Komunitas</label>
                            <div class="space-y-2">
                                <img src="{{ $community->banner_url }}" alt="Banner Saat Ini" class="w-full h-20 rounded-xl object-cover border border-slate-200 shadow-sm">
                                <input type="file" name="banner" accept="image/*" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                <p class="text-[9px] text-slate-400">Rasio 16:9 atau lebar disarankan. Maks 10MB.</p>
                            </div>
                        </div>

                        <!-- 5. Deskripsi Komunitas -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Deskripsi & Visi Komunitas</label>
                            <textarea name="description" rows="3" required placeholder="Tuliskan tentang komunitas, jadwal rutin, dan informasi penting..." class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs p-3 focus:ring-indigo-500 focus:bg-white transition leading-relaxed text-slate-800">{{ old('description', $community->description) }}</textarea>
                        </div>

                        <!-- 6. Aturan & Kebijakan -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Aturan Komunitas (Rules & Sanksi)</label>
                            <textarea name="rules" rows="3" placeholder="Aturan interaksi forum..." class="w-full rounded-xl bg-slate-50 border border-slate-200 text-xs p-3 font-mono leading-relaxed focus:ring-indigo-500 focus:bg-white transition text-slate-800">{{ old('rules', $community->rules) }}</textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                            <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-md shadow-indigo-500/25 transition flex items-center gap-1.5">
                                <i class="fa fa-save text-xs"></i>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
