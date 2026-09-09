<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
            <div class="pb-3 border-b border-slate-100">
                <h1 class="text-lg font-black text-slate-900">Buat Event Komunitas</h1>
                <p class="text-xs text-slate-500">Publikasikan agenda meetup, fun run, turnamen, atau gathering berbayar/gratis.</p>
            </div>

            <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Pilih Komunitas Penyelenggara</label>
                    <select name="community_id" required class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500">
                        @foreach($communities as $comm)
                            <option value="{{ $comm->id }}">{{ $comm->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Judul Event</label>
                    <input type="text" name="title" required placeholder="Contoh: Fun Run 5K Pantai Padang..." class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tanggal & Waktu Pelaksanaan</label>
                        <input type="datetime-local" name="event_date" required class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Lokasi / Titik Kumpul</label>
                        <input type="text" name="location" required placeholder="Contoh: Tugu Merpati Perdamaian..." class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Harga Tiket Pokok (Rp) — Isi 0 jika Gratis</label>
                        <input type="number" name="price" min="0" value="0" required class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500">
                        <span class="text-[9px] text-slate-400 mt-0.5 block">Fee otomatis: 2% (Member) / 5% (Umum) saat checkout.</span>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kuota Tiket</label>
                        <input type="number" name="quota" min="1" value="100" required class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Deskripsi Lengkap & Fasilitas Peserta</label>
                    <textarea name="description" rows="3" placeholder="Tuliskan deskripsi lengkap, fasilitas tiket (BIB number/medali/snack), dan rundown..." class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Foto Banner Sampul Event (Opsional)</label>
                    <input type="file" name="banner" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-indigo-50 file:text-indigo-600">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <a href="{{ route('events.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-sm transition">
                        Publikasikan Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
