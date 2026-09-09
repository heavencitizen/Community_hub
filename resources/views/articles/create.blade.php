<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
            <div class="pb-3 border-b border-slate-100">
                <h1 class="text-lg font-black text-slate-900">Tulis Artikel Baru</h1>
                <p class="text-xs text-slate-500">Buat draf artikel komunitas atau ajukan untuk moderasi Super Admin.</p>
            </div>

            <form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Judul Artikel</label>
                    <input type="text" name="title" required placeholder="Judul artikel menarik..." class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kategori</label>
                        <select name="category" required class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500">
                            <option value="news">Berita</option>
                            <option value="lifestyle">Lifestyle</option>
                            <option value="trend">Trend</option>
                            <option value="donation">Penggalangan Dana</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Hubungkan Komunitas (Opsional)</label>
                        <select name="community_id" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500">
                            <option value="">-- Umum / Tanpa Komunitas --</option>
                            @foreach($communities as $comm)
                                <option value="{{ $comm->id }}">{{ $comm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Isi Konten Artikel</label>
                    <textarea name="content" rows="7" required placeholder="Tuliskan isi artikel Anda di sini..." class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Foto Sampul Artikel</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-indigo-50 file:text-indigo-600">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="submit" name="action" value="draft" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                        Simpan Draf
                    </button>
                    <button type="submit" name="action" value="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-sm transition">
                        Ajukan Moderasi &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
