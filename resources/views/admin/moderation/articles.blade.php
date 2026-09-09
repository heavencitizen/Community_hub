<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        <div class="pb-2 border-b border-slate-200">
            <h1 class="text-xl sm:text-2xl font-black text-slate-900">Moderasi Artikel & Publikasi</h1>
            <p class="text-xs text-slate-500">Super Admin: Tinjau dan publikasikan artikel yang diajukan oleh Admin Komunitas.</p>
        </div>

        <!-- Filter Status -->
        <div class="p-3 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-wrap gap-1.5">
            <a href="{{ route('admin.moderation.articles', ['status' => 'pending']) }}" class="px-3 py-1 rounded-lg text-xs font-bold transition {{ request('status', 'pending') === 'pending' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                Menunggu (Pending)
            </a>
            <a href="{{ route('admin.moderation.articles', ['status' => 'published']) }}" class="px-3 py-1 rounded-lg text-xs font-bold transition {{ request('status') === 'published' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                Terbit (Published)
            </a>
            <a href="{{ route('admin.moderation.articles', ['status' => 'draft']) }}" class="px-3 py-1 rounded-lg text-xs font-bold transition {{ request('status') === 'draft' ? 'bg-slate-700 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                Draf / Ditolak
            </a>
        </div>

        <!-- Articles Table -->
        <div class="overflow-x-auto rounded-2xl bg-white border border-slate-200 shadow-sm">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 uppercase font-bold text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Judul Artikel</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Penulis</th>
                        <th class="px-4 py-3">Tanggal Pengajuan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @if($articles->isEmpty())
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400">
                                Tidak ada artikel dalam antrean moderasi.
                            </td>
                        </tr>
                    @else
                        @foreach($articles as $art)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-3">
                                    <strong class="text-slate-800 text-xs block">{{ $art->title }}</strong>
                                    <span class="text-[10px] text-slate-400 line-clamp-1">{{ Str::limit(strip_tags($art->content), 70) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.2 rounded text-[9px] font-bold bg-indigo-600 text-white uppercase">
                                        {{ $art->category }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <strong class="text-slate-800 block text-xs">{{ $art->author->name ?? 'Admin' }}</strong>
                                    <span class="text-[10px] text-slate-400">{{ $art->community->name ?? 'Umum' }}</span>
                                </td>
                                <td class="px-4 py-3 text-slate-400 text-[10px]">{{ $art->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.2 rounded text-[9px] font-black uppercase {{ $art->status === 'published' ? 'bg-emerald-100 text-emerald-700' : ($art->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                                        {{ $art->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-1.5 whitespace-nowrap">
                                    <a href="{{ route('articles.show', $art->slug) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition border border-slate-200">
                                        Baca
                                    </a>

                                    @if($art->status !== 'published')
                                        <form action="{{ route('admin.moderation.articles.approve', $art->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] transition">
                                                Setujui
                                            </button>
                                        </form>
                                    @endif

                                    @if($art->status === 'pending')
                                        <form action="{{ route('admin.moderation.articles.reject', $art->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-bold text-[11px] transition">
                                                Tolak
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $articles->links() }}
        </div>
    </div>
</x-app-layout>
