<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
            <div class="pb-3 border-b border-slate-100">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-wider mb-1">
                    <i class="fa fa-hand-holding-heart"></i> Inisiatif Amal Komunitas (0% Fee)
                </div>
                <h1 class="text-lg font-black text-slate-900">Buka Program Donasi Amal</h1>
                <p class="text-xs text-slate-500">Buat program galang dana untuk bencana, kemanusiaan, atau pelestarian alam.</p>
            </div>

            <form action="{{ route('donations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Judul Program Donasi</label>
                    <input type="text" name="title" required placeholder="Contoh: Galang Dana Restorasi Terumbu Karang Mandeh..." class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-emerald-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Target Donasi (Rp)</label>
                        <input type="number" name="target_amount" required min="100000" placeholder="10000000" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Komunitas Penyelenggara (Opsional)</label>
                        <select name="community_id" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-emerald-500">
                            <option value="">-- Inisiatif Umum Hub --</option>
                            @foreach($communities as $comm)
                                <option value="{{ $comm->id }}">{{ $comm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Deskripsi Lengkap & Rencana Penyaluran</label>
                    <textarea name="description" rows="4" required placeholder="Jelaskan latar belakang bantuan, pihak penerima manfaat, dan rencana penyaluran..." class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-emerald-500"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Foto Banner Program Donasi</label>
                    <input type="file" name="banner" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-emerald-50 file:text-emerald-700">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <a href="{{ route('donations.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold shadow-sm transition">
                        Publikasikan Donasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
