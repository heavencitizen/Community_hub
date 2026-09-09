@props([
    'post',
    'showCommunity' => true,
])

<div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3" 
     x-data="{ 
         showComments: false, 
         heartPop: false,
         isLiked: {{ auth()->check() && $post->isLikedBy(auth()->id()) ? 'true' : 'false' }},
         likesCount: {{ $post->likes_count ?? 0 }},
         likedUsers: {{ json_encode($post->likes->map(fn($l) => [
             'name' => $l->user->name ?? 'Pengguna',
             'username' => $l->user->username ?? '',
             'avatar' => $l->user->avatar_url ?? null
         ])) }},
         isLiking: false,
         showLikesModal: false,
         showShareModal: false,
         async toggleLike() {
             @guest
                 if (typeof triggerGuestModal === 'function') {
                     triggerGuestModal('menyukai postingan ini');
                 } else {
                     window.location.href = '{{ route('login') }}';
                 }
                 return;
             @endguest

             if (this.isLiking) return;
             this.isLiking = true;
             this.heartPop = true;
             setTimeout(() => this.heartPop = false, 600);
             
             // Optimistic update
             const previousLiked = this.isLiked;
             const previousUsers = [...this.likedUsers];
             this.isLiked = !this.isLiked;
             this.likesCount = this.isLiked ? this.likesCount + 1 : Math.max(0, this.likesCount - 1);
             if (this.isLiked) {
                 this.likedUsers.unshift({ 
                     name: '{{ auth()->user()->name ?? 'Saya' }}', 
                     username: '{{ auth()->user()->username ?? '' }}', 
                     avatar: '{{ auth()->user()->avatar_url ?? '' }}' 
                 });
             } else {
                 this.likedUsers = this.likedUsers.filter(u => u.name !== '{{ auth()->user()->name ?? 'Saya' }}');
             }

             try {
                 const response = await fetch('{{ route('communities.posts.like', $post->id) }}', {
                     method: 'POST',
                     headers: {
                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                         'Accept': 'application/json',
                         'X-Requested-With': 'XMLHttpRequest'
                     }
                 });
                 const data = await response.json();
                 if (data.success) {
                     this.isLiked = data.liked;
                     this.likesCount = data.likes_count;
                     if (data.liked_users) {
                         this.likedUsers = data.liked_users;
                     }
                 }
             } catch (err) {
                 this.isLiked = previousLiked;
                 this.likedUsers = previousUsers;
                 this.likesCount = this.isLiked ? this.likesCount + 1 : Math.max(0, this.likesCount - 1);
             } finally {
                 this.isLiking = false;
             }
         },
         triggerShare() {
            if (navigator.share) {
                navigator.share({
                    title: 'CommunityHub Post',
                    text: '{{ Str::limit(addslashes($post->content), 60) }}',
                    url: '{{ route('communities.show', $post->community->slug) }}#post-{{ $post->id }}'
                }).catch(() => {});
            } else if (typeof openShare === 'function') {
                openShare('{{ route('communities.show', $post->community->slug) }}#post-{{ $post->id }}', '{{ Str::limit(addslashes($post->content), 60) }}');
            } else {
                this.showShareModal = true;
            }
        },
         triggerLikesList() {
             if (typeof openLikes === 'function') {
                 openLikes(this.likedUsers);
             } else {
                 this.showLikesModal = true;
             }
         }
     }" 
     id="post-{{ $post->id }}">
    
    <!-- Post Header -->
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5 min-w-0">
            <a href="{{ route('users.show', $post->author->username) }}" class="flex-shrink-0 group">
                <img src="{{ $post->author->avatar_url }}" alt="{{ $post->author->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 group-hover:ring-2 group-hover:ring-indigo-500 transition">
            </a>
            <div class="min-w-0">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <a href="{{ route('users.show', $post->author->username) }}" class="font-extrabold text-slate-800 text-xs hover:text-indigo-600 transition truncate">
                        {{ $post->author->name }}
                    </a>
                    <span class="text-[10px] text-slate-400 font-mono">{{ '@' . $post->author->username }}</span>
                    @if($post->community->user_id === $post->author->id)
                        <span class="px-1.5 py-0.2 rounded text-[8px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200/60 uppercase">Ketua</span>
                    @elseif($post->author->isSuperAdmin())
                        <span class="px-1.5 py-0.2 rounded text-[8px] font-black bg-slate-100 text-slate-700 border border-slate-200 uppercase">Admin</span>
                    @endif
                    @if(!empty($post->recommendation_reason))
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[8px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <i class="fa fa-sparkles text-[7px] text-emerald-500"></i>
                            <span>{{ $post->recommendation_reason }}</span>
                        </span>
                    @endif
                </div>
                <div class="flex items-center gap-1 text-[10px] text-slate-400 mt-0.5">
                    @if($showCommunity)
                        <a href="{{ route('communities.show', $post->community->slug) }}" class="font-bold text-indigo-600 hover:underline truncate">
                            {{ $post->community->name }}
                        </a>
                        <span>•</span>
                    @endif
                    <span>{{ $post->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        @auth
            @if($post->user_id === auth()->id() || $post->community->user_id === auth()->id() || auth()->user()->isSuperAdmin())
                <form action="{{ route('communities.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Hapus postingan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition" title="Hapus Postingan">
                        <i class="fa fa-trash-can text-xs"></i>
                    </button>
                </form>
            @endif
        @endauth
    </div>

    <!-- Post Body Content -->
    <div class="text-xs text-slate-700 leading-relaxed space-y-2 whitespace-pre-line">
        <p>{{ $post->content }}</p>
    </div>

    <!-- Post Media Attachment -->
    @if($post->media_url)
        <div class="rounded-xl overflow-hidden bg-slate-900 max-h-96 flex items-center justify-center border border-slate-100">
            @if(in_array(pathinfo($post->media_url, PATHINFO_EXTENSION), ['mp4', 'mov', 'webm']))
                <video src="{{ asset('storage/' . $post->media_url) }}" controls class="w-full max-h-96 object-contain"></video>
            @else
                <img src="{{ asset('storage/' . $post->media_url) }}" alt="Post Media" class="w-full max-h-96 object-cover">
            @endif
        </div>
    @endif

    <!-- Instagram-Style Likes Info Bar -->
    <div x-show="likesCount > 0" class="flex items-center gap-2 text-xs text-slate-600 pt-1">
        <div class="flex -space-x-1.5 overflow-hidden flex-shrink-0 cursor-pointer" @click="triggerLikesList()">
            <template x-for="(u, idx) in likedUsers.slice(0, 3)" :key="idx">
                <template x-if="u && u.avatar">
                    <img :src="u.avatar" :alt="u.name" class="inline-block h-5 w-5 rounded-full ring-2 ring-white object-cover">
                </template>
            </template>
        </div>

        <div class="text-[11px] text-slate-600 leading-tight">
            <span>Disukai oleh </span>
            <template x-if="likedUsers.length > 0">
                <button type="button" @click="triggerLikesList()" class="font-bold text-slate-800 hover:underline inline" x-text="likedUsers[0].name"></button>
            </template>
            <template x-if="likedUsers.length === 2">
                <span> dan <button type="button" @click="triggerLikesList()" class="font-bold text-slate-800 hover:underline inline" x-text="likedUsers[1].name"></button></span>
            </template>
            <template x-if="likedUsers.length > 2">
                <span> dan <button type="button" @click="triggerLikesList()" class="font-bold text-slate-800 hover:underline inline" x-text="(likesCount - 1) + ' lainnya'"></button></span>
            </template>
            <template x-if="likedUsers.length === 0">
                <button type="button" @click="triggerLikesList()" class="font-bold text-slate-800 hover:underline inline" x-text="likesCount + ' orang'"></button>
            </template>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="grid grid-cols-3 gap-1.5 pt-2 border-t border-slate-100 text-center text-xs">
        <!-- Asynchronous Like Button -->
        <button type="button" 
                @click="toggleLike()" 
                :class="isLiked ? 'bg-rose-50 text-rose-500' : 'text-slate-600 hover:bg-slate-100'"
                class="w-full py-2.5 sm:py-1.5 min-h-[44px] sm:min-h-0 rounded-lg font-bold transition flex items-center justify-center gap-1.5 active:scale-95">
            <i class="fa fa-heart transition-transform duration-200" :class="{ 'scale-125 text-rose-500 animate-bounce': heartPop, 'text-rose-500': isLiked }"></i>
            <span x-text="isLiked ? 'Disukai' : 'Suka'"></span>
        </button>

        <!-- Comment Button -->
        <button type="button" @click="showComments = !showComments" class="w-full py-2.5 sm:py-1.5 min-h-[44px] sm:min-h-0 rounded-lg font-bold text-slate-600 hover:bg-slate-100 transition flex items-center justify-center gap-1.5">
            <i class="fa fa-comment"></i>
            <span>Komentar ({{ $post->comments->count() }})</span>
        </button>

        <!-- Share Button -->
        <button type="button" @click="triggerShare()" class="w-full py-2.5 sm:py-1.5 min-h-[44px] sm:min-h-0 rounded-lg font-bold text-slate-600 hover:bg-slate-100 transition flex items-center justify-center gap-1.5">
            <i class="fa fa-share-nodes"></i>
            <span>Bagikan</span>
        </button>
    </div>

    <!-- Comments Stream & Input -->
    <div x-show="showComments" x-cloak class="pt-3 border-t border-slate-100 space-y-3">
        <div class="space-y-2">
            @foreach($post->comments as $c)
                <div class="flex items-start gap-2 text-xs">
                    <a href="{{ route('users.show', $c->user->username) }}" class="flex-shrink-0">
                        <img src="{{ $c->user->avatar_url }}" alt="{{ $c->user->name }}" class="w-6 h-6 rounded-full object-cover mt-0.5 border border-slate-200 hover:ring-2 hover:ring-indigo-400 transition">
                    </a>
                    <div class="flex-grow p-2.5 rounded-xl bg-slate-50 border border-slate-200 space-y-0.5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <a href="{{ route('users.show', $c->user->username) }}" class="text-slate-800 font-bold text-[11px] hover:text-indigo-600 transition">
                                    {{ $c->user->name }}
                                </a>
                                <span class="text-[9px] text-slate-400 font-mono">{{ '@' . $c->user->username }}</span>
                            </div>
                            <span class="text-[9px] text-slate-400">{{ $c->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-slate-600 text-[11px]">{{ $c->comment }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        @auth
            <form action="{{ route('communities.posts.comment', $post->id) }}" method="POST" class="flex gap-2 pt-1">
                @csrf
                <input type="text" name="comment" required placeholder="Tulis komentar balasan Anda..." class="flex-grow rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 text-[11px] focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-3">
                <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-[11px] font-bold transition">
                    Kirim
                </button>
            </form>
        @else
            <div class="p-2 rounded-lg bg-slate-50 text-center text-[11px] text-slate-500">
                <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:underline">Masuk</a> untuk ikut berkomentar.
            </div>
        @endauth
    </div>

    <!-- Self-contained Likes Modal fallback (Bottom sheet on mobile, dialog on desktop) -->
    <div x-show="showLikesModal" x-cloak class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/60 backdrop-blur-sm p-0 sm:p-4">
        <div @click.away="showLikesModal = false" class="w-full max-w-lg sm:max-w-sm bg-white rounded-t-3xl sm:rounded-3xl p-5 pb-8 sm:pb-5 shadow-2xl space-y-3 text-left max-h-[85vh] flex flex-col">
            <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto sm:hidden mb-2 -mt-1 flex-shrink-0"></div>
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 flex-shrink-0">
                <h3 class="text-xs font-black text-slate-900 flex items-center gap-1.5">
                    <i class="fa fa-heart text-rose-500"></i>
                    <span>Disukai Oleh</span>
                </h3>
                <button @click="showLikesModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa fa-times text-xs"></i>
                </button>
            </div>
            <div class="overflow-y-auto divide-y divide-slate-100 space-y-1">
                <template x-for="(user, idx) in likedUsers" :key="idx">
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

    <!-- Self-contained Share Modal fallback (Bottom sheet on mobile, dialog on desktop) -->
    <div x-show="showShareModal" x-cloak class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/60 backdrop-blur-sm p-0 sm:p-4">
        <div @click.away="showShareModal = false" class="w-full max-w-lg sm:max-w-sm bg-white rounded-t-3xl sm:rounded-3xl p-5 pb-8 sm:pb-5 shadow-2xl space-y-4 text-left">
            <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto sm:hidden mb-2 -mt-1"></div>
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h3 class="text-xs font-black text-slate-900 flex items-center gap-1.5">
                    <i class="fa fa-share-nodes text-indigo-600"></i>
                    <span>Bagikan Postingan Ini</span>
                </h3>
                <button @click="showShareModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa fa-times text-xs"></i>
                </button>
            </div>

            <div class="grid grid-cols-3 gap-2 text-center text-xs font-bold">
                <a :href="'https://wa.me/?text=' + encodeURIComponent('{{ Str::limit(addslashes($post->content), 60) }} {{ route('communities.show', $post->community->slug) }}#post-{{ $post->id }}')" target="_blank" class="p-2.5 rounded-2xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition flex flex-col items-center gap-1.5">
                    <x-brand-logo name="whatsapp" class="w-7 h-7" />
                    <span class="text-[10px]">WhatsApp</span>
                </a>
                <button @click="navigator.clipboard.writeText('{{ route('communities.show', $post->community->slug) }}#post-{{ $post->id }}'); alert('Tautan berhasil disalin!')" class="p-2.5 rounded-2xl bg-pink-50 hover:bg-pink-100 text-pink-700 transition flex flex-col items-center gap-1.5">
                    <x-brand-logo name="instagram" class="w-7 h-7" />
                    <span class="text-[10px]">Instagram</span>
                </button>
                <a :href="'https://twitter.com/intent/tweet?text=' + encodeURIComponent('{{ Str::limit(addslashes($post->content), 60) }}') + '&url=' + encodeURIComponent('{{ route('communities.show', $post->community->slug) }}#post-{{ $post->id }}')" target="_blank" class="p-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 transition flex flex-col items-center gap-1.5">
                    <x-brand-logo name="twitter" class="w-7 h-7" />
                    <span class="text-[10px]">X / Twitter</span>
                </a>
            </div>

            <div class="pt-2 border-t border-slate-100">
                <button @click="navigator.clipboard.writeText('{{ route('communities.show', $post->community->slug) }}#post-{{ $post->id }}'); alert('Tautan disalin ke clipboard!')" class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center justify-center gap-1.5">
                    <i class="fa fa-link text-xs"></i>
                    <span>Salin Tautan Postingan</span>
                </button>
            </div>
        </div>
    </div>
</div>
