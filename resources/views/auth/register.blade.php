<x-guest-layout>
    <div class="space-y-1 text-center">
        <h1 class="text-xl font-black text-slate-900">Pendaftaran Akun Baru</h1>
        <p class="text-xs text-slate-500">Bergabunglah dengan ribuan anggota komunitas di Sumatera Barat.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-3.5 text-xs">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block font-bold text-slate-700 mb-1">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama Anda" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-bold text-slate-700 mb-1">Alamat Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="nama@email.com" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block font-bold text-slate-700 mb-1">Kata Sandi</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block font-bold text-slate-700 mb-1">Ulangi Kata Sandi</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ketik ulang kata sandi" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-500/20 transition flex items-center justify-center gap-1.5 mt-2">
            <span>Daftar Sekarang</span>
            <i class="fa fa-user-plus text-[10px]"></i>
        </button>

        <div class="pt-3 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:underline">Masuk ke Akun</a>
            </p>
        </div>
    </form>
</x-guest-layout>
