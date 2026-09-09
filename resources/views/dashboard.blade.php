<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6" x-data="{ 
        shareModal: false, 
        shareUrl: '', 
        shareTitle: '',
        likedUsersModal: false,
        currentLikedUsers: [],
        openShare(url, title) {
            this.shareUrl = url;
            this.shareTitle = title;
            this.shareModal = true;
        },
        openLikes(users) {
            this.currentLikedUsers = users;
            this.likedUsersModal = true;
        }
    }">

        <!-- 3-Column Social Media Feed Layout (Responsive: Mobile 1-col, Tablet 2-col, Desktop 3-col) -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-start">

            <!-- ════════════════════════════════════════════════════════════════ -->
            <!-- LEFT COLUMN: User Profile & Komunitas yang Saya Ikuti -->
            <!-- ════════════════════════════════════════════════════════════════ -->
            <div class="order-2 md:order-2 lg:order-1 md:col-span-5 lg:col-span-3 space-y-4">
                <!-- User Mini Card -->
                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('users.show', $user->username) }}" class="flex-shrink-0 group">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-sm group-hover:ring-2 group-hover:ring-indigo-500 transition">
                        </a>
                        <div class="min-w-0">
                            <h3 class="font-extrabold text-xs sm:text-sm text-slate-800 truncate">
                                <a href="{{ route('users.show', $user->username) }}" class="hover:text-indigo-600 transition">{{ $user->name }}</a>
                            </h3>
                            <span class="text-[10px] text-slate-400 font-mono block">{{ '@' . $user->username }}</span>
                            <span class="inline-block mt-0.5 px-2 py-0.2 rounded text-[9px] font-bold {{ $user->isSuperAdmin() ? 'bg-slate-900 text-white' : ($user->isCommunityAdmin() ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-600') }} capitalize">
                                {{ str_replace('_', ' ', $user->role) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 text-center">
                        <div class="p-1.5 rounded-lg bg-slate-50 border border-slate-100">
                            <span class="block text-[10px] text-slate-400 font-bold uppercase">Komunitas</span>
                            <strong class="text-xs font-black text-slate-800">{{ $myCommunities->count() }}</strong>
                        </div>
                        <div class="p-1.5 rounded-lg bg-slate-50 border border-slate-100">
                            <span class="block text-[10px] text-slate-400 font-bold uppercase">Tiket Saya</span>
                            <strong class="text-xs font-black text-indigo-600">{{ $myTickets->count() }}</strong>
                        </div>
                    </div>

                    <a href="{{ route('profile.show') }}" class="block w-full text-center py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        Profil & Aktivitas &rarr;
                    </a>
                </div>

                <!-- Komunitas yang Saya Ikuti -->
                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                            <i class="fa fa-users text-indigo-600"></i>
                            <span>Komunitas Saya</span>
                        </h4>
                        <span class="text-[10px] font-bold text-slate-400">{{ $myCommunities->count() }}</span>
                    </div>

                    <div class="space-y-2">
                        @forelse($myCommunities as $comm)
                            <a href="{{ route('communities.show', $comm->slug) }}" class="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-slate-50 transition group">
                                <img src="{{ $comm->logo_url }}" alt="{{ $comm->name }}" class="w-8 h-8 rounded-lg object-cover border border-slate-100 flex-shrink-0">
                                <div class="min-w-0 flex-grow">
                                    <h5 class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 truncate">{{ $comm->name }}</h5>
                                    <span class="text-[10px] text-slate-400">{{ $comm->members_count }} anggota</span>
                                </div>
                            </a>
                        @empty
                            <div class="py-3 text-center text-slate-400 text-xs">
                                <p class="text-[11px]">Belum bergabung dengan komunitas.</p>
                                <a href="{{ route('communities.index') }}" class="text-indigo-600 font-bold hover:underline text-[11px] block mt-1">
                                    + Cari Komunitas
                                </a>
                            </div>
                        @endforelse
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <a href="{{ route('communities.create') }}" class="w-full flex items-center justify-center gap-1.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold transition">
                            <i class="fa fa-plus text-[10px]"></i>
                            <span>Bentuk Komunitas Baru</span>
                        </a>
                    </div>
                </div>

                <!-- Pintasan Cepat Navigasi -->
                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-2 text-xs">
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Eksplorasi Cepat</h4>
                    <a href="{{ route('events.index') }}" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-50 text-slate-700 font-bold transition">
                        <i class="fa fa-ticket text-indigo-600 w-4"></i>
                        <span>Jelajah Event & Tiket</span>
                    </a>
                    <a href="{{ route('marketplace.index') }}" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-50 text-slate-700 font-bold transition">
                        <i class="fa-solid fa-bag-shopping text-indigo-600 w-4"></i>
                        <span>Etalase Jual Beli</span>
                    </a>
                    <a href="{{ route('auctions.index') }}" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-50 text-slate-700 font-bold transition">
                        <i class="fa fa-gavel text-indigo-600 w-4"></i>
                        <span>Arena Lelang Komunitas</span>
                    </a>
                    <a href="{{ route('donations.index') }}" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-50 text-slate-700 font-bold transition">
                        <i class="fa fa-hand-holding-heart text-emerald-600 w-4"></i>
                        <span>Donasi Amal (0% Fee)</span>
                    </a>
                    <a href="{{ route('articles.index') }}" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-50 text-slate-700 font-bold transition">
                        <i class="fa fa-newspaper text-indigo-600 w-4"></i>
                        <span>Artikel & Media Berita</span>
                    </a>
                </div>
            </div>

            <!-- ════════════════════════════════════════════════════════════════ -->
            <!-- CENTER COLUMN: Social Media Feed Postingan (PRIORITY 1) -->
            <!-- ════════════════════════════════════════════════════════════════ -->
            <div class="order-1 md:order-1 lg:order-2 md:col-span-7 lg:col-span-6 space-y-4">

                <!-- Mobile Community Story Pills (md:hidden) -->
                @if($myCommunities->isNotEmpty())
                    <div class="md:hidden -mx-4 px-4 overflow-x-auto no-scrollbar flex items-center gap-3 py-1">
                        <a href="{{ route('communities.create') }}" class="flex-shrink-0 flex flex-col items-center gap-1 group">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 border-2 border-dashed border-indigo-300 flex items-center justify-center text-indigo-600 group-active:scale-95 transition">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </div>
                            <span class="text-[10px] font-bold text-slate-500 max-w-[54px] truncate">Bentuk</span>
                        </a>
                        @foreach($myCommunities as $comm)
                            <a href="{{ route('communities.show', $comm->slug) }}" class="flex-shrink-0 flex flex-col items-center gap-1 group">
                                <div class="relative p-0.5 rounded-2xl bg-gradient-to-tr from-indigo-500 to-indigo-700 group-active:scale-95 transition">
                                    <img src="{{ $comm->logo_url }}" alt="{{ $comm->name }}" class="w-11 h-11 rounded-[14px] object-cover border-2 border-white">
                                </div>
                                <span class="text-[10px] font-bold text-slate-700 max-w-[58px] truncate">{{ $comm->name }}</span>
                            </a>
                        @endforeach
                        <a href="{{ route('communities.index') }}" class="flex-shrink-0 flex flex-col items-center gap-1 group">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 group-active:scale-95 transition">
                                <i class="fa-solid fa-compass text-xs"></i>
                            </div>
                            <span class="text-[10px] font-bold text-slate-500 max-w-[54px] truncate">Jelajah</span>
                        </a>
                    </div>
                @endif

                <!-- 1. Quick Post Composer Box -->
                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3" x-data="{ openComposer: false, fileName: '' }">
                    <div class="flex items-center gap-3">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-xl object-cover border border-slate-200 flex-shrink-0">
                        <button type="button" @click="openComposer = !openComposer" class="flex-grow text-left py-2.5 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200/80 text-slate-500 text-xs font-medium transition flex items-center justify-between">
                            <span>Apa cerita, tips hobi, atau agenda hari ini, {{ explode(' ', $user->name)[0] }}?</span>
                            <i class="fa fa-pen-to-square text-indigo-500 text-sm"></i>
                        </button>
                    </div>

                    <!-- Expandable Composer Modal / Form -->
                    <div x-show="openComposer" x-cloak class="pt-3 border-t border-slate-100 space-y-3">
                        @if($myCommunities->isEmpty() && !$user->isSuperAdmin())
                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center justify-between">
                                <span><strong>Perhatian:</strong> Anda harus bergabung dengan minimal 1 komunitas untuk membagikan postingan ke forum.</span>
                                <a href="{{ route('communities.index') }}" class="font-bold text-indigo-600 underline ml-2">Jelajahi Komunitas &rarr;</a>
                            </div>
                        @else
                            <form action="{{ route('posts.quickStore') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                @csrf

                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-bold text-slate-500 uppercase whitespace-nowrap">Posting ke:</span>
                                    <select name="community_id" required class="flex-grow rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-xs py-1.5 px-3 focus:ring-indigo-500 focus:border-indigo-500 font-bold">
                                        @foreach($myCommunities as $comm)
                                            <option value="{{ $comm->id }}">{{ $comm->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <textarea name="content" rows="3" required placeholder="Tuliskan cerita, ajakan mabar, tips hobi, atau info komunitas..." class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs p-3 focus:ring-indigo-500 focus:border-indigo-500 placeholder-slate-400 resize-none"></textarea>
                                </div>

                                <!-- File Preview / Selected Indicator -->
                                <div x-show="fileName" class="flex items-center justify-between px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-semibold">
                                    <span class="truncate flex items-center gap-1.5">
                                        <i class="fa fa-file-lines text-indigo-500"></i>
                                        <span x-text="fileName"></span>
                                    </span>
                                    <button type="button" @click="fileName = ''; $refs.dashFileInput.value = ''" class="text-rose-500 hover:text-rose-700 ml-2 font-bold text-sm">&times;</button>
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-100">
                                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer transition">
                                        <i class="fa fa-image text-emerald-500 text-sm"></i>
                                        <span>Foto / Video</span>
                                        <input type="file" name="media" x-ref="dashFileInput" accept="image/*,video/*" class="hidden" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
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
                        @endif
                    </div>
                </div>

                <!-- 2. Feed Filter Tabs -->
                <div class="p-1.5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-1 text-xs overflow-x-auto no-scrollbar whitespace-nowrap">
                    <a href="{{ route('dashboard', ['filter' => 'personalized']) }}" class="flex-1 min-w-[100px] sm:min-w-0 py-2 sm:py-1.5 text-center rounded-xl font-bold transition {{ $feedFilter === 'personalized' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="fa-solid fa-layer-group text-[11px] mr-1"></i>
                        <span>Untuk Anda</span>
                    </a>
                    <a href="{{ route('dashboard', ['filter' => 'following']) }}" class="flex-1 min-w-[120px] sm:min-w-0 py-2 sm:py-1.5 text-center rounded-xl font-bold transition {{ $feedFilter === 'following' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="fa fa-user-check text-[11px] mr-1"></i>
                        <span>Komunitas Saya</span>
                    </a>
                    <a href="{{ route('dashboard', ['filter' => 'all']) }}" class="flex-1 min-w-[85px] sm:min-w-0 py-2 sm:py-1.5 text-center rounded-xl font-bold transition {{ $feedFilter === 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="fa fa-globe text-[11px] mr-1"></i>
                        <span>Semua</span>
                    </a>
                </div>

                <!-- 3. Social Stream Feed Cards -->
                <div class="space-y-4">
                    @forelse($posts as $post)
                        <x-post-card :post="$post" />
                    @empty
                        <div class="p-8 rounded-2xl bg-white border border-slate-200 text-center text-slate-500 space-y-2">
                            <i class="fa fa-comments text-3xl text-slate-300"></i>
                            <h4 class="text-sm font-bold text-slate-800">Belum ada postingan di feed ini.</h4>
                            <p class="text-xs text-slate-400">Jadilah yang pertama membuat postingan atau bergabunglah dengan komunitas baru!</p>
                            <a href="{{ route('communities.index') }}" class="inline-block mt-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm transition">
                                Jelajahi Komunitas
                            </a>
                        </div>
                    @endforelse

                    <div class="mt-4">
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>

            <!-- ════════════════════════════════════════════════════════════════ -->
            <!-- RIGHT COLUMN: Agenda Saya (Priority 2) & Saran (Priority 3) -->
            <!-- ════════════════════════════════════════════════════════════════ -->
            <div class="order-3 md:order-3 lg:order-3 md:col-span-12 lg:col-span-3 space-y-4 md:grid md:grid-cols-2 md:gap-4 md:space-y-0 lg:block lg:space-y-4">

                <!-- SECTION 1: Hal-Hal Berkaitan dengan User (Tiket & Lelang Saya) -->
                @if($myTickets->isNotEmpty() || $myBids->isNotEmpty())
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-1 border-b border-slate-100">
                            <h4 class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                                <i class="fa fa-calendar-check text-emerald-600"></i>
                                <span>Agenda & Tiket Saya</span>
                            </h4>
                            <a href="{{ route('profile.tickets') }}" class="text-[10px] font-bold text-indigo-600 hover:underline">Semua</a>
                        </div>

                        <!-- Tiket Saya yang Aktif -->
                        <div class="space-y-2">
                            @foreach($myTickets as $ticket)
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-mono text-[9px] font-bold text-slate-400">#{{ $ticket->ticket_code }}</span>
                                        <span class="px-1.5 py-0.2 rounded text-[8px] font-black uppercase {{ $ticket->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $ticket->status }}
                                        </span>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-xs line-clamp-1">{{ $ticket->event->title }}</h5>
                                    <div class="flex items-center justify-between pt-1">
                                        <span class="text-[9px] text-slate-400">{{ $ticket->event->event_date->format('d M, H:i') }}</span>
                                        <a href="{{ route('tickets.show', $ticket->id) }}" class="text-[10px] font-bold text-indigo-600 hover:underline flex items-center gap-1">
                                            <i class="fa fa-qrcode"></i>
                                            <span>QR E-Ticket</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- SECTION 1.5: Rekomendasi Pertemanan (Social Recommendation Engine) -->
                @if(isset($friendRecommendations) && $friendRecommendations->isNotEmpty())
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-1 border-b border-slate-100">
                            <h4 class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                                <i class="fa fa-user-plus text-indigo-600"></i>
                                <span>Saran Teman Komunitas</span>
                            </h4>
                            <span class="px-1.5 py-0.2 rounded text-[8px] font-black bg-indigo-50 text-indigo-700 uppercase">Sosial</span>
                        </div>

                        <div class="space-y-2.5">
                            @foreach($friendRecommendations as $friend)
                                <div x-data="{ following: false, loading: false }" class="flex items-center justify-between gap-2 p-1.5 rounded-xl hover:bg-slate-50 transition">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <a href="{{ route('users.show', $friend->username) }}" class="flex-shrink-0 group">
                                            <img src="{{ $friend->avatar_url }}" alt="{{ $friend->name }}" class="w-8 h-8 rounded-xl object-cover border border-slate-200 group-hover:ring-2 group-hover:ring-indigo-400 transition">
                                        </a>
                                        <div class="min-w-0">
                                            <h5 class="text-xs font-bold text-slate-800 truncate">
                                                <a href="{{ route('users.show', $friend->username) }}" class="hover:text-indigo-600 transition">{{ $friend->name }}</a>
                                            </h5>
                                            <span class="text-[9px] text-slate-400 font-mono block truncate">{{ '@' . $friend->username }}</span>
                                            <span class="inline-flex items-center gap-1 text-[9px] font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.2 rounded-md truncate max-w-[130px]">
                                                <i class="fa fa-users text-[8px]"></i>
                                                <span class="truncate">{{ $friend->recommendation_reason }}</span>
                                            </span>
                                        </div>
                                    </div>

                                    <button type="button" 
                                            :disabled="loading"
                                            @click="
                                                loading = true;
                                                fetch('{{ route('users.follow', $friend->id) }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        'Accept': 'application/json',
                                                        'X-Requested-With': 'XMLHttpRequest'
                                                    }
                                                })
                                                .then(r => r.json())
                                                .then(data => {
                                                    if (data.success) {
                                                        following = data.following;
                                                    }
                                                })
                                                .catch(err => console.error(err))
                                                .finally(() => loading = false);
                                            "
                                            :class="following ? 'bg-slate-100 text-slate-600 hover:bg-slate-200' : 'bg-indigo-600 text-white hover:bg-indigo-500 shadow-sm'"
                                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition flex-shrink-0 flex items-center gap-1">
                                        <i class="fa" :class="following ? 'fa-check' : 'fa-plus'"></i>
                                        <span x-text="following ? 'Diikuti' : 'Ikuti'">Ikuti</span>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- SECTION 2: Saran Event Komunitas Terdekat (Priority 3) -->
                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                    <div class="flex items-center justify-between pb-1 border-b border-slate-100">
                        <h4 class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                            <i class="fa fa-calendar-alt text-indigo-600"></i>
                            <span>Saran Event Terdekat</span>
                        </h4>
                        <a href="{{ route('events.index') }}" class="text-[10px] font-bold text-indigo-600 hover:underline">Lainnya</a>
                    </div>

                    <div class="space-y-3">
                        @foreach($suggestedEvents as $event)
                            <div class="rounded-xl border border-slate-200 overflow-hidden bg-white hover:border-indigo-300 transition group">
                                <div class="h-20 bg-slate-100 relative overflow-hidden">
                                    <img src="{{ $event->banner_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute top-1.5 right-1.5 px-2 py-0.2 rounded-full text-[8px] font-black bg-slate-900/80 text-white">
                                        {{ $event->price == 0 ? 'Gratis' : 'Rp' . number_format($event->price, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="p-2.5 space-y-1">
                                    <span class="text-[8px] font-extrabold text-indigo-600 uppercase tracking-wider block">{{ $event->community->name }}</span>
                                    <h5 class="font-bold text-slate-800 text-xs line-clamp-1 hover:text-indigo-600 transition">
                                        <a href="{{ route('events.show', $event->slug) }}">{{ $event->title }}</a>
                                    </h5>
                                    <p class="text-[9px] text-slate-400 flex items-center gap-1">
                                        <i class="fa fa-calendar"></i>
                                        <span>{{ $event->event_date->format('d M Y, H:i') }}</span>
                                    </p>
                                    <a href="{{ route('events.show', $event->slug) }}" class="block w-full text-center py-1 rounded-lg bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white text-[10px] font-bold transition mt-1">
                                        Pesan Tiket &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- SECTION 3: Saran Komunitas Baru yang Belum Diikuti (Priority 3) -->
                @if($suggestedCommunities->isNotEmpty())
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-1 border-b border-slate-100">
                            <h4 class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                                <i class="fa fa-compass text-indigo-600"></i>
                                <span>Rekomendasi Komunitas</span>
                            </h4>
                            <a href="{{ route('communities.index') }}" class="text-[10px] font-bold text-indigo-600 hover:underline">Semua</a>
                        </div>

                        <div class="space-y-2.5">
                            @foreach($suggestedCommunities as $sugComm)
                                <div class="flex items-center justify-between gap-2 p-1.5 rounded-xl hover:bg-slate-50 transition">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <img src="{{ $sugComm->logo_url }}" alt="{{ $sugComm->name }}" class="w-8 h-8 rounded-lg object-cover border border-slate-100 flex-shrink-0">
                                        <div class="min-w-0">
                                            <h5 class="text-xs font-bold text-slate-800 truncate">
                                                <a href="{{ route('communities.show', $sugComm->slug) }}" class="hover:text-indigo-600">{{ $sugComm->name }}</a>
                                            </h5>
                                            <span class="text-[9px] text-slate-400">{{ $sugComm->members_count }} anggota</span>
                                        </div>
                                    </div>

                                    <form action="{{ route('communities.join', $sugComm->id) }}" method="POST" class="flex-shrink-0">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-[10px] font-bold shadow-sm transition">
                                            Gabung
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- SECTION 3.5: Postingan Populer & Relevan (Related Content Recommendation) -->
                @if(isset($relatedPosts) && $relatedPosts->isNotEmpty())
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-1 border-b border-slate-100">
                            <h4 class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                            <i class="fa fa-newspaper text-indigo-600"></i>
                            <span>Postingan Relevan</span>
                        </h4>
                            <span class="px-1.5 py-0.2 rounded text-[8px] font-black bg-amber-50 text-amber-700 uppercase">Rekomendasi</span>
                        </div>

                        <div class="space-y-2.5">
                            @foreach($relatedPosts as $relPost)
                                <a href="{{ route('communities.show', $relPost->community->slug) }}#post-{{ $relPost->id }}" class="block p-2 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/30 transition group">
                                    <div class="flex items-center justify-between text-[9px] text-slate-400 mb-1">
                                        <span class="font-bold text-indigo-600 truncate max-w-[130px]">{{ $relPost->community->name }}</span>
                                        <span class="flex items-center gap-1 text-slate-500 font-bold">
                                            <i class="fa fa-heart text-rose-500 text-[8px]"></i>
                                            {{ $relPost->likes_count }}
                                        </span>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-800 group-hover:text-indigo-600 line-clamp-2 leading-relaxed">
                                        {{ \Illuminate\Support\Str::limit($relPost->content, 90) }}
                                    </p>
                                    <div class="flex items-center justify-between text-[9px] text-slate-400 mt-1.5 pt-1 border-t border-slate-50">
                                        <span>Oleh {{ $relPost->author->name }}</span>
                                        <span class="text-indigo-600 font-bold group-hover:underline">Buka Diskusi &rarr;</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- SECTION 4: Donasi Amal Terpilih (0% Fee) -->
                @if($featuredDonations->isNotEmpty())
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-1 border-b border-slate-100">
                            <h4 class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                                <i class="fa fa-hand-holding-heart text-teal-600"></i>
                                <span>Donasi Amal Komunitas</span>
                            </h4>
                            <span class="px-1.5 py-0.2 rounded text-[8px] font-black bg-emerald-100 text-emerald-700 uppercase">0% Fee</span>
                        </div>

                        <div class="space-y-2">
                            @foreach($featuredDonations as $donasi)
                                <a href="{{ route('donations.show', $donasi->slug) }}" class="block p-2 rounded-xl border border-slate-100 hover:border-teal-300 transition group">
                                    <h5 class="text-xs font-bold text-slate-800 group-hover:text-teal-700 line-clamp-1">{{ $donasi->title }}</h5>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 my-1.5 overflow-hidden">
                                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $donasi->progressPercentage() }}%"></div>
                                    </div>
                                    <div class="flex justify-between text-[9px] text-slate-400">
                                        <span>Terkumpul: <strong>Rp{{ number_format($donasi->collected_amount, 0, ',', '.') }}</strong></span>
                                        <span>{{ $donasi->progressPercentage() }}%</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!-- MODAL 1: SHARE POP-UP TO EXTERNAL SOCIAL NETWORKS -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <div x-show="shareModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="shareModal = false" class="w-full max-w-sm bg-white rounded-3xl p-5 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-sm font-black text-slate-900 flex items-center gap-1.5">
                        <i class="fa fa-share-nodes text-indigo-600"></i>
                        <span>Bagikan ke Media Sosial</span>
                    </h3>
                    <button @click="shareModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <i class="fa fa-times text-sm"></i>
                    </button>
                </div>

                <p class="text-[11px] text-slate-500" x-text="shareTitle"></p>

                <!-- Official Vector Logos Share Buttons -->
                <div class="grid grid-cols-3 gap-2 text-center text-xs font-bold">
                    <!-- WhatsApp -->
                    <a :href="'https://wa.me/?text=' + encodeURIComponent(shareTitle + ' ' + shareUrl)" target="_blank" class="p-2.5 rounded-2xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition flex flex-col items-center gap-1.5 group">
                        <x-brand-logo name="whatsapp" class="w-7 h-7 shadow-sm group-hover:scale-105 transition-transform" />
                        <span class="text-[10px]">WhatsApp</span>
                    </a>

                    <!-- Instagram Direct / Copy -->
                    <button @click="navigator.clipboard.writeText(shareUrl); alert('Tautan berhasil disalin! Buka Instagram untuk membagikan cerita ini.')" class="p-2.5 rounded-2xl bg-pink-50 hover:bg-pink-100 text-pink-700 transition flex flex-col items-center gap-1.5 group">
                        <x-brand-logo name="instagram" class="w-7 h-7 shadow-sm group-hover:scale-105 transition-transform" />
                        <span class="text-[10px]">Instagram</span>
                    </button>

                    <!-- Twitter / X -->
                    <a :href="'https://twitter.com/intent/tweet?text=' + encodeURIComponent(shareTitle) + '&url=' + encodeURIComponent(shareUrl)" target="_blank" class="p-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 transition flex flex-col items-center gap-1.5 group">
                        <x-brand-logo name="twitter" class="w-7 h-7 shadow-sm group-hover:scale-105 transition-transform" />
                        <span class="text-[10px]">X / Twitter</span>
                    </a>

                    <!-- TikTok -->
                    <button @click="navigator.clipboard.writeText(shareUrl); alert('Tautan disalin! Buka TikTok untuk membagikan konten ini.')" class="p-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-900 transition flex flex-col items-center gap-1.5 group">
                        <x-brand-logo name="tiktok" class="w-7 h-7 shadow-sm group-hover:scale-105 transition-transform" />
                        <span class="text-[10px]">TikTok</span>
                    </button>

                    <!-- Facebook -->
                    <a :href="'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(shareUrl)" target="_blank" class="p-2.5 rounded-2xl bg-blue-50 hover:bg-blue-100 text-blue-700 transition flex flex-col items-center gap-1.5 group">
                        <x-brand-logo name="facebook" class="w-7 h-7 shadow-sm group-hover:scale-105 transition-transform" />
                        <span class="text-[10px]">Facebook</span>
                    </a>

                    <!-- Telegram -->
                    <a :href="'https://t.me/share/url?url=' + encodeURIComponent(shareUrl) + '&text=' + encodeURIComponent(shareTitle)" target="_blank" class="p-2.5 rounded-2xl bg-sky-50 hover:bg-sky-100 text-sky-700 transition flex flex-col items-center gap-1.5 group">
                        <x-brand-logo name="telegram" class="w-7 h-7 shadow-sm group-hover:scale-105 transition-transform" />
                        <span class="text-[10px]">Telegram</span>
                    </a>
                </div>

                <!-- Copy Link Bar -->
                <div class="pt-2 border-t border-slate-100">
                    <button @click="navigator.clipboard.writeText(shareUrl); alert('Tautan berhasil disalin ke clipboard!')" class="w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center justify-center gap-1.5">
                        <i class="fa fa-link text-xs"></i>
                        <span>Salin Tautan Postingan</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!-- MODAL 2: USERS WHO LIKED THIS POST -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <div x-show="likedUsersModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div @click.away="likedUsersModal = false" class="w-full max-w-sm bg-white rounded-3xl p-5 shadow-2xl space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-black text-slate-900 flex items-center gap-1.5">
                        <i class="fa fa-heart text-rose-500"></i>
                        <span>Disukai Oleh</span>
                    </h3>
                    <button @click="likedUsersModal = false" class="text-slate-400 hover:text-slate-600">
                        <i class="fa fa-times text-xs"></i>
                    </button>
                </div>
                <div class="max-h-72 overflow-y-auto divide-y divide-slate-100 space-y-1">
                    <template x-for="(user, idx) in currentLikedUsers" :key="idx">
                        <div class="flex items-center justify-between py-2 px-1">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <template x-if="user.avatar">
                                    <img :src="user.avatar" :alt="user.name" class="w-8 h-8 rounded-xl object-cover border border-slate-200 shadow-sm flex-shrink-0">
                                </template>
                                <template x-if="!user.avatar">
                                    <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                        <i class="fa fa-user"></i>
                                    </div>
                                </template>
                                <div class="min-w-0">
                                    <template x-if="user.username">
                                        <a :href="'/users/' + user.username" class="font-bold text-xs text-slate-800 hover:text-indigo-600 truncate block" x-text="user.name"></a>
                                    </template>
                                    <template x-if="!user.username">
                                        <p class="font-bold text-xs text-slate-800 truncate" x-text="user.name || user"></p>
                                    </template>
                                    <span class="text-[9px] text-slate-400">Anggota Komunitas</span>
                                </div>
                            </div>
                            <span class="text-rose-500 text-xs"><i class="fa fa-heart"></i></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
