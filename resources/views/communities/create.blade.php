<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
            <div class="pb-3 border-b border-slate-100">
                <h1 class="text-lg font-black text-slate-900">Bentuk Komunitas Baru</h1>
                <p class="text-xs text-slate-500">Mulai komunitas hobi Anda, buka forum diskusi, etalase jual beli, dan lelang.</p>
            </div>

            <form action="{{ route('communities.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Komunitas</label>
                        <input type="text" name="name" required placeholder="Contoh: Sumatra Runners Club..." class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kategori Hobi</label>
                        <select name="category" required class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500">
                            <option value="olahraga">🏃‍♂️ Olahraga</option>
                            <option value="otomotif">🛵 Otomotif & Vespa</option>
                            <option value="kesehatan">🤿 Kesehatan & Alam</option>
                            <option value="kuliner">☕ Kuliner & Kopi</option>
                            <option value="rumah_tangga">🏡 Rumah Tangga & Kebun</option>
                            <option value="seni">🎨 Seni & Kreatif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Deskripsi Komunitas</label>
                    <textarea name="description" rows="3" required placeholder="Ceritakan visi, aktivitas rutin, atau lokasi kumpul..." class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Aturan & Tata Tertib Komunitas</label>
                    <textarea name="rules" rows="3" placeholder="1. Saling menghormati sesama anggota...&#10;2. Dilarang spam atau penipuan..." class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500 font-mono"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Foto Logo / Profil Komunitas</label>
                        <input type="file" name="logo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-indigo-50 file:text-indigo-600">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Foto Banner Sampul</label>
                        <input type="file" name="banner" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-indigo-50 file:text-indigo-600">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <a href="{{ route('communities.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-sm transition">
                        Bentuk Komunitas
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
