<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        <div class="pb-2 border-b border-slate-200">
            <h1 class="text-xl sm:text-2xl font-black text-slate-900">Pengaturan Akun & Informasi Sistem</h1>
            <p class="text-xs text-slate-500">Kelola keamanan akun dan tinjau struktur platform terpadu.</p>
        </div>

        <!-- Section 1: Ubah Kata Sandi -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
            <div>
                <h2 class="text-sm font-black text-slate-800">Perbarui Kata Sandi</h2>
                <p class="text-[11px] text-slate-500 mt-0.5">Pastikan akun Anda menggunakan kata sandi yang aman dan tidak dibagikan ke pihak lain.</p>
            </div>

            <form action="{{ route('settings.password') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kata Sandi Saat Ini</label>
                    <input type="password" name="current_password" required placeholder="••••••••" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-xs py-2 px-3 focus:ring-indigo-500">
                    @error('current_password') <span class="text-[10px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kata Sandi Baru</label>
                        <input type="password" name="password" required placeholder="Minimal 8 karakter" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-xs py-2 px-3 focus:ring-indigo-500">
                        @error('password') <span class="text-[10px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi baru" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-xs py-2 px-3 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-sm transition">
                        Simpan Sandi Baru
                    </button>
                </div>
            </form>
        </div>

        <!-- Section 2: Struktur Fee Platform Resmi -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
            <h2 class="text-sm font-black text-slate-800">Struktur Komisi & Biaya Platform Resmi</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                    <span class="text-[10px] text-slate-400 font-bold uppercase">Jual Beli & Lelang</span>
                    <p class="text-indigo-600 font-black text-sm">1% (Member) • 2% (Umum)</p>
                    <p class="text-[10px] text-slate-500">Penambahan produk/lelang eksklusif oleh Ketua Komunitas.</p>
                </div>
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                    <span class="text-[10px] text-slate-400 font-bold uppercase">Tiket Event Resmi</span>
                    <p class="text-indigo-600 font-black text-sm">2% (Member) • 5% (Umum)</p>
                    <p class="text-[10px] text-slate-500">Tiket gratis 0% fee (RSVP gratis).</p>
                </div>
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                    <span class="text-[10px] text-slate-400 font-bold uppercase">Donasi Kemanusiaan</span>
                    <p class="text-emerald-600 font-black text-sm">0% (Bebas Biaya)</p>
                    <p class="text-[10px] text-slate-500">100% donasi disalurkan tanpa potongan apapun.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
