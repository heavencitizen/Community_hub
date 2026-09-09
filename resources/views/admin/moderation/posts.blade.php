<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        <div class="pb-2 border-b border-slate-200">
            <h1 class="text-xl sm:text-2xl font-black text-slate-900">Moderasi Feed Forum Komunitas</h1>
            <p class="text-xs text-slate-500">Super Admin: Pantau semua aktivitas postingan dan bersihkan konten spam / tidak pantas.</p>
        </div>

        <div class="overflow-x-auto rounded-2xl bg-white border border-slate-200 shadow-sm">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 uppercase font-bold text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Komunitas</th>
                        <th class="px-4 py-3">Penulis</th>
                        <th class="px-4 py-3">Isi Konten Postingan</th>
                        <th class="px-4 py-3">Media</th>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @if($posts->isEmpty())
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400">
                                Belum ada postingan forum.
                            </td>
                        </tr>
                    @else
                        @foreach($posts as $post)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-3 font-bold text-indigo-600">{{ $post->community->name }}</td>
                                <td class="px-4 py-3 font-bold text-slate-800">{{ $post->author->name }}</td>
                                <td class="px-4 py-3 max-w-md">
                                    <p class="line-clamp-2 text-slate-600">{{ $post->content }}</p>
                                </td>
                                <td class="px-4 py-3 uppercase font-semibold text-slate-500 text-[10px]">
                                    {{ $post->type }}
                                    @if($post->media_url)
                                        <a href="{{ asset('storage/' . $post->media_url) }}" target="_blank" class="text-indigo-600 font-bold block hover:underline">Lihat Media</a>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-400 text-[10px]">{{ $post->created_at->diffForHumans() }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <form action="{{ route('communities.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Hapus postingan ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white font-bold text-[11px] transition">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $posts->links() }}
        </div>
    </div>
</x-app-layout>
