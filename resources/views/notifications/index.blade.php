<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-6 space-y-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-200">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 flex items-center gap-2">
                    <i class="fa fa-bell text-indigo-600"></i>
                    <span>Pusat Notifikasi & Aktivitas</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Pantau interaksi suka, komentar, pendaftaran anggota, tiket, dan transaksi Anda.</p>
            </div>

            @if(auth()->user()->unreadNotificationsCount() > 0)
                <form action="{{ route('notifications.markAllRead') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs transition flex items-center gap-1.5">
                        <i class="fa fa-check-double text-[10px]"></i>
                        <span>Tandai Semua Dibaca</span>
                    </button>
                </form>
            @endif
        </div>

        <!-- Filter Tabs -->
        <div class="p-1 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center gap-1 text-xs max-w-xs">
            <a href="{{ route('notifications.index', ['filter' => 'all']) }}" class="flex-1 py-1 text-center rounded-lg font-bold transition {{ $filter === 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' }}">
                Semua
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="flex-1 py-1 text-center rounded-lg font-bold transition {{ $filter === 'unread' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' }}">
                Belum Dibaca ({{ auth()->user()->unreadNotificationsCount() }})
            </a>
        </div>

        <!-- Notifications List -->
        <div class="space-y-2.5">
            @forelse($notifications as $notif)
                <a href="{{ route('notifications.read', $notif->id) }}" class="block p-3.5 rounded-2xl border transition group {{ $notif->is_read ? 'bg-white border-slate-200 hover:border-indigo-200' : 'bg-indigo-50/50 border-indigo-200 shadow-sm hover:bg-indigo-50' }}">
                    <div class="flex items-start gap-3">
                        <!-- Sender Avatar / Icon -->
                        <div class="relative flex-shrink-0">
                            @if($notif->sender)
                                <img src="{{ $notif->sender->avatar_url }}" alt="{{ $notif->sender->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 shadow-sm">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-base shadow-sm">
                                    <i class="fa {{ $notif->icon }}"></i>
                                </div>
                            @endif
                            <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-white shadow flex items-center justify-center text-[9px] {{ $notif->icon_color }}">
                                <i class="fa {{ $notif->icon }}"></i>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-grow min-w-0 space-y-0.5">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="font-bold text-xs text-slate-900 group-hover:text-indigo-600 transition flex items-center gap-1.5 truncate">
                                    <span>{{ $notif->title }}</span>
                                    @if(!$notif->is_read)
                                        <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                                    @endif
                                </h4>
                                <span class="text-[10px] text-slate-400 whitespace-nowrap">{{ $notif->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">{{ $notif->message }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-8 rounded-2xl bg-white border border-slate-200 text-center text-slate-400 space-y-2">
                    <i class="fa fa-bell-slash text-3xl text-slate-300"></i>
                    <h3 class="text-sm font-bold text-slate-700">Tidak ada notifikasi baru</h3>
                    <p class="text-xs text-slate-400">Semua aktivitas dan interaksi terbaru Anda akan muncul di sini.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </div>
</x-app-layout>
