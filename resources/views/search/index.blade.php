<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 min-h-[65vh]" x-data="{ activeTab: 'users' }">
        
        <!-- Header Pencarian -->
        <div class="mb-8 space-y-4">
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Hasil Pencarian</h1>
            
            <!-- Search Bar (Diperbaiki dengan Flexbox agar presisi) -->
            <form action="{{ route('search.index') }}" method="GET" class="flex items-center w-full bg-white border border-slate-200 rounded-2xl shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 transition-all p-1">
                <div class="pl-4 pr-2 flex items-center justify-center">
                    <i class="fa fa-search text-slate-400"></i>
                </div>
                <input type="text" name="q" value="{{ $query }}" placeholder="Cari nama pengguna atau komunitas..." class="w-full border-none focus:ring-0 text-sm font-medium text-slate-700 py-3 px-2 bg-transparent" autocomplete="off">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition whitespace-nowrap active:scale-95">
                    Cari
                </button>
            </form>
        </div>

        @if($query)
            <!-- Navigasi Tab -->
            <div class="flex items-center gap-2 mb-6 border-b border-slate-200 pb-2 overflow-x-auto no-scrollbar">
                <button @click="activeTab = 'users'" :class="activeTab === 'users' ? 'bg-indigo-600 text-white shadow-md border-indigo-600' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200'" class="px-5 py-2.5 rounded-xl border font-bold text-xs transition whitespace-nowrap flex items-center gap-2 active:scale-95">
                    <i class="fa fa-user"></i> Pengguna ({{ $users->count() }})
                </button>
                <button @click="activeTab = 'communities'" :class="activeTab === 'communities' ? 'bg-indigo-600 text-white shadow-md border-indigo-600' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200'" class="px-5 py-2.5 rounded-xl border font-bold text-xs transition whitespace-nowrap flex items-center gap-2 active:scale-95">
                    <i class="fa fa-users"></i> Komunitas ({{ $communities->count() }})
                </button>
            </div>

            <!-- Tab Konten: Pengguna -->
            <div x-show="activeTab === 'users'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @forelse($users as $user)
                    <a href="{{ route('users.show', $user->username) }}" class="flex items-center gap-3 p-4 bg-white rounded-2xl border border-slate-200 hover:border-indigo-300 hover:shadow-md transition group">
                        <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-full object-cover group-hover:scale-105 transition border border-slate-100 shrink-0">
                        <div class="min-w-0 flex-1">
                            <h4 class="font-bold text-slate-800 text-sm truncate group-hover:text-indigo-600 transition">
                                {{ $user->name }}
                                @if($user->role !== 'member')
                                    <span class="inline-block ml-1 text-[10px] text-slate-400 capitalize">({{ $user->role }})</span>
                                @endif
                            </h4>
                            <p class="text-xs text-slate-400 font-mono truncate">{{ '@' . $user->username }}</p>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-12 text-center text-slate-400 bg-white rounded-2xl border border-slate-200 border-dashed">
                        <i class="fa fa-user-xmark text-4xl mb-3 text-slate-300"></i>
                        <p class="text-sm font-medium">Pengguna dengan kata kunci "{{ $query }}" tidak ditemukan.</p>
                    </div>
                @endforelse
            </div>

            <!-- Tab Konten: Komunitas -->
            <div x-show="activeTab === 'communities'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @forelse($communities as $community)
                    <a href="{{ route('communities.show', $community->slug) }}" class="flex items-center gap-4 p-4 bg-white rounded-2xl border border-slate-200 hover:border-indigo-300 hover:shadow-md transition group">
                        <img src="{{ $community->logo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($community->name) }}" alt="{{ $community->name }}" class="w-14 h-14 rounded-2xl object-cover group-hover:scale-105 transition border border-slate-100 shadow-sm shrink-0">
                        <div class="min-w-0 flex-1">
                            <h4 class="font-bold text-slate-800 text-sm truncate group-hover:text-indigo-600 transition">{{ $community->name }}</h4>
                            <p class="text-[10px] font-black text-indigo-500 uppercase tracking-wider mt-0.5 truncate">{{ $community->category }}</p>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-1">{{ $community->description ?? 'Deskripsi belum ditambahkan.' }}</p>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-12 text-center text-slate-400 bg-white rounded-2xl border border-slate-200 border-dashed">
                        <i class="fa fa-users-slash text-4xl mb-3 text-slate-300"></i>
                        <p class="text-sm font-medium">Komunitas dengan kata kunci "{{ $query }}" tidak ditemukan.</p>
                    </div>
                @endforelse
            </div>
        @else
            <!-- State awal saat belum ada pencarian -->
            <div class="py-24 text-center text-slate-400 space-y-3 bg-white rounded-2xl border border-slate-200 border-dashed shadow-sm">
                <i class="fa fa-magnifying-glass text-5xl text-slate-200"></i>
                <p class="text-sm font-medium">Ketikkan kata kunci di atas untuk mulai mencari pengguna atau komunitas.</p>
            </div>
        @endif
    </div>
</x-app-layout>