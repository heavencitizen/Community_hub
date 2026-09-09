<!-- Slide-up Mobile Quick Post Bottom Sheet Modal -->
@auth
@php
    $myCommunities = auth()->user()->joinedCommunities ?? collect();
@endphp
<div x-data="{ 
        isOpen: false, 
        fileName: '',
        mediaPreview: null,
        mediaType: null,
        content: '',
        communityId: '{{ optional($myCommunities->first())->id ?? '' }}',
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
            if ($refs.mobileFileInput) {
                $refs.mobileFileInput.value = '';
            }
        }
     }"
     @open-mobile-composer.window="isOpen = true"
     @keydown.escape.window="isOpen = false"
     x-show="isOpen" 
     x-cloak 
     class="md:hidden fixed inset-0 z-50 flex items-end justify-center">

    <!-- Backdrop -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="isOpen = false" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

    <!-- Bottom Sheet Content -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="relative w-full max-w-lg bg-white rounded-t-3xl shadow-2xl p-5 space-y-4 max-h-[90vh] overflow-y-auto pb-safe">

        <!-- Drag Grab Handle -->
        <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto -mt-1 mb-2"></div>

        <!-- Header -->
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-xl object-cover border border-slate-200 shadow-sm">
                <div>
                    <h3 class="text-xs font-black text-slate-900">Buat Postingan Komunitas</h3>
                    <p class="text-[10px] text-slate-400 font-mono">{{ '@' . auth()->user()->username }}</p>
                </div>
            </div>
            <button type="button" @click="isOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-full hover:bg-slate-100">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        @if($myCommunities->isEmpty() && !auth()->user()->isSuperAdmin())
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs space-y-2">
                <p><strong>Belum Bergabung:</strong> Anda perlu bergabung dengan minimal 1 komunitas untuk berbagi cerita dan tips.</p>
                <a href="{{ route('communities.index') }}" @click="isOpen = false" class="inline-block px-3 py-1.5 rounded-lg bg-amber-600 text-white font-bold text-xs">
                    Cari & Gabung Komunitas &rarr;
                </a>
            </div>
        @else
            <form action="{{ route('posts.quickStore') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf

                <!-- Select Community -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pilih Komunitas</label>
                    <select name="community_id" required class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-xs py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500 font-bold">
                        @foreach($myCommunities as $comm)
                            <option value="{{ $comm->id }}">{{ $comm->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Textarea -->
                <div>
                    <textarea name="content" 
                              rows="4" 
                              required 
                              x-model="content"
                              placeholder="Apa cerita, tips hobi, atau agenda komunitas Anda hari ini?" 
                              class="w-full rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 text-xs p-3.5 focus:ring-indigo-500 focus:border-indigo-500 placeholder-slate-400 resize-none leading-relaxed"></textarea>
                </div>

                <!-- Rich Visual Media Preview (Photo / Video) -->
                <div x-show="mediaPreview" x-cloak class="relative rounded-2xl overflow-hidden border border-slate-200 bg-slate-900 shadow-md max-h-56 flex items-center justify-center">
                    <template x-if="mediaType === 'image'">
                        <img :src="mediaPreview" alt="Pratinjau Foto" class="w-full max-h-56 object-cover rounded-2xl">
                    </template>
                    <template x-if="mediaType === 'video'">
                        <video :src="mediaPreview" controls class="w-full max-h-56 rounded-2xl"></video>
                    </template>

                    <!-- Floating Badge & Cancel Button -->
                    <div class="absolute top-2.5 left-2.5 right-2.5 flex items-center justify-between pointer-events-none">
                        <span class="px-2.5 py-1 rounded-lg bg-slate-900/80 backdrop-blur text-white text-[10px] font-bold flex items-center gap-1.5 shadow">
                            <i class="fa-solid" :class="mediaType === 'video' ? 'fa-video text-indigo-400' : 'fa-image text-emerald-400'"></i>
                            <span x-text="fileName" class="max-w-[160px] truncate"></span>
                        </span>
                        <button type="button" @click="clearMedia()" class="pointer-events-auto p-1.5 rounded-full bg-slate-900/80 hover:bg-rose-600 text-white transition shadow" title="Batalkan berkas">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Action Controls -->
                <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100">
                    <label class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer transition">
                        <i class="fa-solid fa-image text-emerald-500 text-sm"></i>
                        <span>Foto / Video</span>
                        <input type="file" name="media" x-ref="mobileFileInput" accept="image/*,video/*" class="hidden" @change="handleFileSelect($event)">
                    </label>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="isOpen = false" class="px-3 py-2 rounded-xl text-slate-500 hover:bg-slate-100 text-xs font-bold transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-500/25 transition flex items-center gap-1.5 active:scale-95">
                            <i class="fa-solid fa-paper-plane text-[10px]"></i>
                            <span>Kirim Post</span>
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>
@endauth