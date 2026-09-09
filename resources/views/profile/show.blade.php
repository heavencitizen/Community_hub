<x-app-layout>
    <div class="max-w-5xl mx-auto px-3 sm:px-6 py-4 sm:py-6 space-y-4 sm:space-y-6" 
         x-data="{ 
             activeTab: '{{ request()->query('tab', 'posts') }}',
             following: {{ $isFollowing ? 'true' : 'false' }},
             followersCount: {{ $followersCount }},
             loadingFollow: false,
             async toggleFollow() {
                 @guest
                     window.location.href = '{{ route('login') }}';
                     return;
                 @endguest

                 if (this.loadingFollow) return;
                 this.loadingFollow = true;

                 try {
                     const response = await fetch('{{ route('users.follow', $user->id) }}', {
                         method: 'POST',
                         headers: {
                             'X-CSRF-TOKEN': '{{ csrf_token() }}',
                             'Accept': 'application/json',
                             'X-Requested-With': 'XMLHttpRequest'
                         }
                     });
                     const data = await response.json();
                     if (data.success) {
                         this.following = data.following;
                         this.followersCount = data.followers_count;
                     }
                 } catch (err) {
                     console.error(err);
                 } finally {
                     this.loadingFollow = false;
                 }
             }
         }">

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!-- 1. PROFILE HEADER: Cover Banner, Avatar & Social Meta -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <div class="bg-white border border-slate-200 shadow-sm rounded-3xl overflow-hidden">
            <!-- Cover Banner -->
            <div class="w-full h-36 sm:h-60 bg-slate-900 relative overflow-hidden">
                <img src="{{ $user->banner_url }}" alt="Cover {{ $user->name }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
            </div>

            <!-- Profile Info Body -->
            <div class="px-4 sm:px-8 pb-5 sm:pb-6 pt-2 sm:pt-3">
                <!-- Top Row: Avatar overlapping cover + Desktop Actions -->
                <div class="flex items-end justify-between -mt-14 sm:-mt-20 mb-3 sm:mb-4">
                    <!-- Avatar -->
                    <div class="relative z-10 flex-shrink-0">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-24 h-24 sm:w-32 sm:h-32 rounded-2xl sm:rounded-3xl object-cover ring-4 ring-white shadow-xl bg-white border border-slate-100">
                    </div>

                    <!-- Desktop Action Buttons (hidden on mobile, visible on sm:) -->
                    <div class="hidden sm:flex items-center gap-2 pb-1">
                        @if($isOwn)
                            <a href="{{ route('profile.edit') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-200 shadow-sm transition flex items-center gap-1.5 active:scale-95">
                                <i class="fa fa-pen text-[10px]"></i>
                                <span>Edit Profil</span>
                            </a>
                            <a href="{{ route('profile.tickets') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-sm transition flex items-center gap-1.5 active:scale-95">
                                <i class="fa fa-ticket text-[10px]"></i>
                                <span>Tiket Saya</span>
                            </a>
                        @else
                            <button type="button" 
                                    @click="toggleFollow()"
                                    :disabled="loadingFollow"
                                    :class="following ? 'bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 border border-slate-200' : 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-100'"
                                    class="px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 active:scale-95">
                                <i class="fa text-[11px]" :class="following ? 'fa-user-check text-emerald-500' : 'fa-user-plus'"></i>
                                <span x-text="following ? 'Mengikuti' : 'Ikuti Akun Ini'"></span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- User Identity: Name, Badge, Handle, Join Date -->
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $user->name }}</h1>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $user->isSuperAdmin() ? 'bg-amber-100 text-amber-700' : ($user->isCommunityAdmin() ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-700') }}">
                            {{ str_replace('_', ' ', $user->role) }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 text-xs flex-wrap">
                        <span class="font-mono text-indigo-600 font-bold">{{ '@' . $user->username }}</span>
                        <span class="text-slate-300">•</span>
                        <span class="text-[11px] text-slate-400 flex items-center gap-1">
                            <i class="fa fa-calendar-alt text-[10px]"></i>
                            <span>Bergabung sejak {{ $user->created_at->format('F Y') }}</span>
                        </span>
                    </div>
                </div>

                <!-- Bio Content -->
                <div class="mt-3 text-xs sm:text-sm text-slate-600 leading-relaxed max-w-3xl">
                    @if($user->bio)
                        <p class="whitespace-pre-line">{{ $user->bio }}</p>
                    @else
                        <p class="text-slate-400 italic">Pengguna belum menambahkan bio informasi pribadi.</p>
                    @endif
                </div>

                <!-- Mobile Action Buttons (visible on mobile, hidden on sm:) -->
                <div class="mt-3.5 sm:hidden">
                    @if($isOwn)
                        <div class="grid grid-cols-2 gap-2 w-full">
                            <a href="{{ route('profile.edit') }}" class="justify-center py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-200 shadow-sm transition flex items-center gap-1.5 active:scale-95 text-center">
                                <i class="fa fa-pen text-[10px]"></i>
                                <span>Edit Profil</span>
                            </a>
                            <a href="{{ route('profile.tickets') }}" class="justify-center py-2.5 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-sm transition flex items-center gap-1.5 active:scale-95 text-center">
                                <i class="fa fa-ticket text-[10px]"></i>
                                <span>Tiket Saya</span>
                            </a>
                        </div>
                    @else
                        <button type="button" 
                                @click="toggleFollow()"
                                :disabled="loadingFollow"
                                :class="following ? 'bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 border border-slate-200' : 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-100'"
                                class="w-full justify-center py-2.5 px-4 rounded-xl font-bold text-xs transition flex items-center gap-2 active:scale-95">
                            <i class="fa text-[11px]" :class="following ? 'fa-user-check text-emerald-500' : 'fa-user-plus'"></i>
                            <span x-text="following ? 'Mengikuti' : 'Ikuti Akun Ini'"></span>
                        </button>
                    @endif
                </div>

                <!-- Social Stats Counter Strip (Compact single row) -->
                <div class="grid grid-cols-4 divide-x divide-slate-100 py-2.5 sm:py-3 bg-slate-50/80 rounded-2xl border border-slate-100 text-center mt-4 sm:mt-5">
                    <button type="button" @click="activeTab = 'posts'" class="px-1.5 group transition hover:text-indigo-600 focus:outline-none">
                        <span class="block text-base sm:text-lg font-black text-slate-900 group-hover:text-indigo-600 transition">{{ $postsCount }}</span>
                        <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">Post</span>
                    </button>
                    <button type="button" @click="activeTab = 'communities'" class="px-1.5 group transition hover:text-indigo-600 focus:outline-none">
                        <span class="block text-base sm:text-lg font-black text-slate-900 group-hover:text-indigo-600 transition">{{ $communitiesCount }}</span>
                        <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider truncate">Komunitas</span>
                    </button>
                    <button type="button" @click="activeTab = 'followers'" class="px-1.5 group transition hover:text-indigo-600 focus:outline-none">
                        <span class="block text-base sm:text-lg font-black text-indigo-600 group-hover:text-indigo-700 transition" x-text="followersCount">{{ $followersCount }}</span>
                        <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider truncate">Pengikut</span>
                    </button>
                    <button type="button" @click="activeTab = 'following'" class="px-1.5 group transition hover:text-indigo-600 focus:outline-none">
                        <span class="block text-base sm:text-lg font-black text-slate-900 group-hover:text-indigo-600 transition">{{ $followingCount }}</span>
                        <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider truncate">Mengikuti</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!-- 2. SOCIAL MEDIA TABS NAVIGATION -->
        <!-- ════════════════════════════════════════════════════════════════ -->
        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl overflow-hidden">
            <div class="flex items-center px-2 sm:px-4 overflow-x-auto no-scrollbar whitespace-nowrap border-b border-slate-100">
                <!-- 1. Postingan -->
                <button type="button" 
                        @click="activeTab = 'posts'" 
                        :class="activeTab === 'posts' ? 'text-indigo-600 border-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-500 hover:text-slate-800 border-transparent font-medium hover:bg-slate-50/80'"
                        class="py-3 px-3.5 sm:px-5 border-b-2 text-xs sm:text-sm transition-all flex items-center gap-2 flex-shrink-0 active:scale-95">
                    <i class="fa fa-newspaper text-xs" :class="activeTab === 'posts' ? 'text-indigo-600' : 'text-slate-400'"></i>
                    <span>Postingan</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'posts' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500'">{{ $postsCount }}</span>
                </button>

                <!-- 2. Komunitas -->
                <button type="button" 
                        @click="activeTab = 'communities'" 
                        :class="activeTab === 'communities' ? 'text-indigo-600 border-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-500 hover:text-slate-800 border-transparent font-medium hover:bg-slate-50/80'"
                        class="py-3 px-3.5 sm:px-5 border-b-2 text-xs sm:text-sm transition-all flex items-center gap-2 flex-shrink-0 active:scale-95">
                    <i class="fa fa-users text-xs" :class="activeTab === 'communities' ? 'text-indigo-600' : 'text-slate-400'"></i>
                    <span>Komunitas</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'communities' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500'">{{ $communitiesCount }}</span>
                </button>

                <!-- 3. Media -->
                <button type="button" 
                        @click="activeTab = 'media'" 
                        :class="activeTab === 'media' ? 'text-indigo-600 border-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-500 hover:text-slate-800 border-transparent font-medium hover:bg-slate-50/80'"
                        class="py-3 px-3.5 sm:px-5 border-b-2 text-xs sm:text-sm transition-all flex items-center gap-2 flex-shrink-0 active:scale-95">
                    <i class="fa fa-image text-xs" :class="activeTab === 'media' ? 'text-indigo-600' : 'text-slate-400'"></i>
                    <span>Media</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'media' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500'">{{ $mediaPosts->count() }}</span>
                </button>

                <!-- 4. Pengikut -->
                <button type="button" 
                        @click="activeTab = 'followers'" 
                        :class="activeTab === 'followers' ? 'text-indigo-600 border-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-500 hover:text-slate-800 border-transparent font-medium hover:bg-slate-50/80'"
                        class="py-3 px-3.5 sm:px-5 border-b-2 text-xs sm:text-sm transition-all flex items-center gap-2 flex-shrink-0 active:scale-95">
                    <i class="fa fa-user-group text-xs" :class="activeTab === 'followers' ? 'text-indigo-600' : 'text-slate-400'"></i>
                    <span>Pengikut</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'followers' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500'" x-text="followersCount">{{ $followersCount }}</span>
                </button>

                <!-- 5. Mengikuti -->
                <button type="button" 
                        @click="activeTab = 'following'" 
                        :class="activeTab === 'following' ? 'text-indigo-600 border-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-500 hover:text-slate-800 border-transparent font-medium hover:bg-slate-50/80'"
                        class="py-3 px-3.5 sm:px-5 border-b-2 text-xs sm:text-sm transition-all flex items-center gap-2 flex-shrink-0 active:scale-95">
                    <i class="fa fa-user-check text-xs" :class="activeTab === 'following' ? 'text-indigo-600' : 'text-slate-400'"></i>
                    <span>Mengikuti</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'following' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500'">{{ $followingCount }}</span>
                </button>

                <!-- 6. Khusus Akun Sendiri: Tiket & Pesanan -->
                @if($isOwn)
                    <button type="button" 
                            @click="activeTab = 'tickets'" 
                            :class="activeTab === 'tickets' ? 'text-indigo-600 border-indigo-600 font-bold bg-indigo-50/50' : 'text-slate-500 hover:text-slate-800 border-transparent font-medium hover:bg-slate-50/80'"
                            class="py-3 px-3.5 sm:px-5 border-b-2 text-xs sm:text-sm transition-all flex items-center gap-2 flex-shrink-0 active:scale-95">
                        <i class="fa fa-ticket text-xs" :class="activeTab === 'tickets' ? 'text-indigo-600' : 'text-slate-400'"></i>
                        <span>Tiket Saya</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold" :class="activeTab === 'tickets' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500'">{{ $tickets->count() }}</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════════ -->
        <!-- 3. TAB PANELS CONTENT -->
        <!-- ════════════════════════════════════════════════════════════════ -->

        <!-- TAB 1: POSTINGAN USER -->
        <div x-show="activeTab === 'posts'" class="space-y-4">
            @forelse($posts as $post)
                <x-post-card :post="$post" />
            @empty
                <div class="p-12 rounded-3xl bg-white border border-slate-200 text-center text-slate-500 space-y-3 shadow-sm">
                    <i class="fa fa-comments text-4xl text-slate-300"></i>
                    <h3 class="text-sm font-bold text-slate-800">Belum ada postingan yang dipublikasikan</h3>
                    <p class="text-xs text-slate-400 max-w-sm mx-auto">
                        @if($isOwn)
                            Mulai berdiskusi, bagikan tips, atau cerita agenda komunitas Anda di beranda atau forum!
                        @else
                            Pengguna ini belum pernah mengunggah postingan di komunitas manapun.
                        @endif
                    </p>
                    @if($isOwn)
                        <a href="{{ route('communities.index') }}" class="inline-block mt-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm transition">
                            Jelajahi Komunitas &rarr;
                        </a>
                    @endif
                </div>
            @endforelse

            <div class="mt-4">
                {{ $posts->links() }}
            </div>
        </div>

        <!-- TAB 2: KOMUNITAS YANG DIIKUTI / DIPIMPIN -->
        <div x-show="activeTab === 'communities'" x-cloak class="space-y-4">
            @if($memberships->isEmpty())
                <div class="p-12 rounded-3xl bg-white border border-slate-200 text-center text-slate-500 space-y-3 shadow-sm">
                    <i class="fa fa-users text-4xl text-slate-300"></i>
                    <h3 class="text-sm font-bold text-slate-800">Belum bergabung dengan komunitas manapun</h3>
                    <p class="text-xs text-slate-400">Temukan komunitas hobi lokal di Sumatera Barat dan bangun jejaring baru.</p>
                    <a href="{{ route('communities.index') }}" class="inline-block mt-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm transition">
                        Jelajahi Direktori Komunitas &rarr;
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($memberships as $m)
                        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-indigo-300 transition flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="{{ $m->community->logo_url }}" alt="{{ $m->community->name }}" class="w-12 h-12 rounded-2xl object-cover border border-slate-200 flex-shrink-0 shadow-sm">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <h4 class="font-bold text-xs sm:text-sm text-slate-800 truncate">
                                            <a href="{{ route('communities.show', $m->community->slug) }}" class="hover:text-indigo-600 transition">{{ $m->community->name }}</a>
                                        </h4>
                                        @if($m->community->user_id === $user->id)
                                            <span class="px-1.5 py-0.2 rounded text-[8px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200/60 uppercase">Ketua</span>
                                        @elseif($m->role === 'moderator')
                                            <span class="px-1.5 py-0.2 rounded text-[8px] font-black bg-indigo-100 text-indigo-700 uppercase">Moderator</span>
                                        @endif
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1">
                                        <i class="fa fa-tag text-[9px] text-indigo-500"></i>
                                        <span class="capitalize">{{ $m->community->category }}</span>
                                        <span>•</span>
                                        <span>Bergabung sejak {{ $m->joined_at ? $m->joined_at->format('d M Y') : $m->created_at->format('d M Y') }}</span>
                                    </p>
                                </div>
                            </div>

                            <a href="{{ route('communities.show', $m->community->slug) }}" class="px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-indigo-600 text-slate-700 hover:text-white border border-slate-200 text-xs font-bold transition flex-shrink-0">
                                Buka
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- TAB 3: MEDIA (FOTO & VIDEO POSTINGAN) -->
        <div x-show="activeTab === 'media'" x-cloak class="space-y-4">
            @if($mediaPosts->isEmpty())
                <div class="p-12 rounded-3xl bg-white border border-slate-200 text-center text-slate-500 space-y-2 shadow-sm">
                    <i class="fa fa-photo-film text-4xl text-slate-300"></i>
                    <h3 class="text-sm font-bold text-slate-800">Belum ada foto atau video</h3>
                    <p class="text-xs text-slate-400">Foto dan video yang diunggah dalam postingan komunitas akan muncul di galeri ini.</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($mediaPosts as $mediaPost)
                        <a href="{{ route('communities.show', $mediaPost->community->slug) }}#post-{{ $mediaPost->id }}" class="group block relative rounded-2xl overflow-hidden bg-slate-900 aspect-square shadow-sm border border-slate-100">
                            @if(in_array(pathinfo($mediaPost->media_url, PATHINFO_EXTENSION), ['mp4', 'mov', 'webm']))
                                <video src="{{ asset('storage/' . $mediaPost->media_url) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"></video>
                                <div class="absolute top-2 right-2 w-6 h-6 rounded-full bg-black/60 text-white flex items-center justify-center text-[10px]">
                                    <i class="fa fa-play"></i>
                                </div>
                            @else
                                <img src="{{ asset('storage/' . $mediaPost->media_url) }}" alt="Media" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @endif

                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-3 flex flex-col justify-end text-white text-xs">
                                <p class="text-[11px] font-bold line-clamp-2 leading-tight">{{ $mediaPost->content }}</p>
                                <div class="flex items-center justify-between text-[9px] text-slate-300 mt-1 pt-1 border-t border-white/20">
                                    <span>{{ $mediaPost->community->name }}</span>
                                    <span><i class="fa fa-heart text-rose-400 mr-0.5"></i> {{ $mediaPost->likes_count }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- TAB 4: PENGIKUT (FOLLOWERS) -->
        <div x-show="activeTab === 'followers'" x-cloak class="space-y-4">
            @if($followers->isEmpty())
                <div class="p-12 rounded-3xl bg-white border border-slate-200 text-center text-slate-500 space-y-2 shadow-sm">
                    <i class="fa fa-user-group text-4xl text-slate-300"></i>
                    <h3 class="text-sm font-bold text-slate-800">Belum memiliki pengikut</h3>
                    <p class="text-xs text-slate-400">Aktif berinteraksi dan membuat postingan menarik untuk mendapatkan teman dan pengikut baru.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($followers as $f)
                        <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between gap-3 hover:border-indigo-200 transition"
                             x-data="{ 
                                 following: {{ auth()->check() && auth()->user()->isFollowing($f->id) ? 'true' : 'false' }},
                                 loading: false,
                                 async toggle() {
                                     @guest
                                         window.location.href = '{{ route('login') }}';
                                         return;
                                     @endguest
                                     if (this.loading) return;
                                     this.loading = true;
                                     try {
                                         const res = await fetch('{{ route('users.follow', $f->id) }}', {
                                             method: 'POST',
                                             headers: {
                                                 'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                 'Accept': 'application/json',
                                                 'X-Requested-With': 'XMLHttpRequest'
                                             }
                                         });
                                         const d = await res.json();
                                         if (d.success) {
                                             this.following = d.following;
                                         }
                                     } catch(e) { console.error(e); }
                                     finally { this.loading = false; }
                                 }
                             }">
                            <div class="flex items-center gap-3 min-w-0">
                                <a href="{{ route('users.show', $f->username) }}" class="flex-shrink-0 group">
                                    <img src="{{ $f->avatar_url }}" alt="{{ $f->name }}" class="w-11 h-11 rounded-xl object-cover border border-slate-200 group-hover:ring-2 group-hover:ring-indigo-400 transition">
                                </a>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-xs text-slate-800 truncate">
                                        <a href="{{ route('users.show', $f->username) }}" class="hover:text-indigo-600 transition">{{ $f->name }}</a>
                                    </h4>
                                    <span class="text-[10px] text-slate-400 font-mono block">{{ '@' . $f->username }}</span>
                                    <p class="text-[9px] text-slate-400 mt-0.5">
                                        <span>{{ $f->community_posts_count }} post</span> • 
                                        <span>{{ $f->followers_count }} pengikut</span>
                                    </p>
                                </div>
                            </div>

                            @if(auth()->check() && auth()->id() !== $f->id)
                                <button type="button" 
                                        @click="toggle()"
                                        :disabled="loading"
                                        :class="following ? 'bg-slate-100 text-slate-600 hover:bg-rose-50 hover:text-rose-600' : 'bg-indigo-600 text-white hover:bg-indigo-500 shadow-sm'"
                                        class="px-3 py-1.5 rounded-xl font-bold text-xs transition flex-shrink-0 flex items-center gap-1">
                                    <i class="fa text-[10px]" :class="following ? 'fa-check' : 'fa-plus'"></i>
                                    <span x-text="following ? 'Mengikuti' : 'Ikuti Balik'"></span>
                                </button>
                            @else
                                <a href="{{ route('users.show', $f->username) }}" class="px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold transition flex-shrink-0">
                                    Lihat
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- TAB 5: MENGIKUTI (FOLLOWING) -->
        <div x-show="activeTab === 'following'" x-cloak class="space-y-4">
            @if($following->isEmpty())
                <div class="p-12 rounded-3xl bg-white border border-slate-200 text-center text-slate-500 space-y-2 shadow-sm">
                    <i class="fa fa-user-check text-4xl text-slate-300"></i>
                    <h3 class="text-sm font-bold text-slate-800">Belum mengikuti siapapun</h3>
                    <p class="text-xs text-slate-400">Ikuti teman atau anggota komunitas lainnya untuk melihat pembaruan aktivitas mereka di feed Anda.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($following as $fol)
                        <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between gap-3 hover:border-indigo-200 transition"
                             x-data="{ 
                                 following: {{ auth()->check() && auth()->user()->isFollowing($fol->id) ? 'true' : 'false' }},
                                 loading: false,
                                 async toggle() {
                                     @guest
                                         window.location.href = '{{ route('login') }}';
                                         return;
                                     @endguest
                                     if (this.loading) return;
                                     this.loading = true;
                                     try {
                                         const res = await fetch('{{ route('users.follow', $fol->id) }}', {
                                             method: 'POST',
                                             headers: {
                                                 'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                 'Accept': 'application/json',
                                                 'X-Requested-With': 'XMLHttpRequest'
                                             }
                                         });
                                         const d = await res.json();
                                         if (d.success) {
                                             this.following = d.following;
                                         }
                                     } catch(e) { console.error(e); }
                                     finally { this.loading = false; }
                                 }
                             }">
                            <div class="flex items-center gap-3 min-w-0">
                                <a href="{{ route('users.show', $fol->username) }}" class="flex-shrink-0 group">
                                    <img src="{{ $fol->avatar_url }}" alt="{{ $fol->name }}" class="w-11 h-11 rounded-xl object-cover border border-slate-200 group-hover:ring-2 group-hover:ring-indigo-400 transition">
                                </a>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-xs text-slate-800 truncate">
                                        <a href="{{ route('users.show', $fol->username) }}" class="hover:text-indigo-600 transition">{{ $fol->name }}</a>
                                    </h4>
                                    <span class="text-[10px] text-slate-400 font-mono block">{{ '@' . $fol->username }}</span>
                                    <p class="text-[9px] text-slate-400 mt-0.5">
                                        <span>{{ $fol->community_posts_count }} post</span> • 
                                        <span>{{ $fol->followers_count }} pengikut</span>
                                    </p>
                                </div>
                            </div>

                            @if(auth()->check() && auth()->id() !== $fol->id)
                                <button type="button" 
                                        @click="toggle()"
                                        :disabled="loading"
                                        :class="following ? 'bg-slate-100 text-slate-600 hover:bg-rose-50 hover:text-rose-600' : 'bg-indigo-600 text-white hover:bg-indigo-500 shadow-sm'"
                                        class="px-3 py-1.5 rounded-xl font-bold text-xs transition flex-shrink-0 flex items-center gap-1">
                                    <i class="fa text-[10px]" :class="following ? 'fa-check' : 'fa-plus'"></i>
                                    <span x-text="following ? 'Mengikuti' : 'Ikuti'"></span>
                                </button>
                            @else
                                <a href="{{ route('users.show', $fol->username) }}" class="px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold transition flex-shrink-0">
                                    Lihat
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- TAB 6: KHUSUS AKUN SENDIRI (TIKET & TRANSAKSI) -->
        @if($isOwn)
            <div x-show="activeTab === 'tickets'" x-cloak class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- E-Ticket Saya -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <h3 class="text-xs font-black text-slate-800 flex items-center gap-1.5 uppercase tracking-wider">
                                <i class="fa fa-ticket text-indigo-600"></i>
                                <span>E-Ticket Event Anda</span>
                            </h3>
                            <a href="{{ route('profile.tickets') }}" class="text-[11px] text-indigo-600 font-bold hover:underline">Semua Tiket &rarr;</a>
                        </div>

                        @if($tickets->isEmpty())
                            <div class="py-8 text-center text-slate-400 text-xs space-y-2">
                                <i class="fa fa-ticket text-3xl text-slate-200"></i>
                                <p>Belum ada tiket event yang Anda miliki.</p>
                                <a href="{{ route('events.index') }}" class="inline-block px-3 py-1.5 rounded-xl bg-indigo-600 text-white font-bold text-xs">Jelajahi Event</a>
                            </div>
                        @else
                            <div class="space-y-2.5">
                                @foreach($tickets->take(5) as $ticket)
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <span class="font-mono text-[9px] font-bold text-slate-400">#{{ $ticket->ticket_code }}</span>
                                            <span class="px-1.5 py-0.2 rounded text-[8px] font-black uppercase {{ $ticket->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                                {{ $ticket->status }}
                                            </span>
                                        </div>
                                        <h4 class="font-bold text-slate-800 text-xs line-clamp-1">{{ $ticket->event->title }}</h4>
                                        <div class="flex items-center justify-between pt-1 border-t border-slate-100 text-[10px]">
                                            <span class="text-slate-400">{{ $ticket->event->event_date->format('d M Y, H:i') }}</span>
                                            <a href="{{ route('tickets.show', $ticket->id) }}" class="font-bold text-indigo-600 hover:underline flex items-center gap-1">
                                                <i class="fa fa-qrcode"></i>
                                                <span>QR E-Ticket</span>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Riwayat Transaksi Terakhir -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <h3 class="text-xs font-black text-slate-800 flex items-center gap-1.5 uppercase tracking-wider">
                                <i class="fa fa-receipt text-emerald-600"></i>
                                <span>Transaksi Terakhir</span>
                            </h3>
                        </div>

                        @if($transactions->isEmpty())
                            <div class="py-8 text-center text-slate-400 text-xs space-y-2">
                                <i class="fa fa-receipt text-3xl text-slate-200"></i>
                                <p>Belum ada riwayat transaksi pembayaran.</p>
                            </div>
                        @else
                            <div class="space-y-2.5">
                                @foreach($transactions as $trx)
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-mono text-[9px] font-bold text-indigo-600">#{{ $trx->transaction_code }}</span>
                                                <span class="px-1.5 py-0.2 rounded text-[8px] font-black uppercase {{ $trx->payment_status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                                    {{ $trx->payment_status }}
                                                </span>
                                            </div>
                                            <p class="text-[10px] text-slate-400 mt-0.5 capitalize">{{ str_replace('_', ' ', $trx->type) }} • {{ $trx->created_at->format('d M Y') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs font-black text-slate-900 block">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</span>
                                            <span class="text-[9px] font-mono text-slate-400 uppercase">{{ $trx->payment_method }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
