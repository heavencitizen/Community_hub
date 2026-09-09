<x-guest-layout>
    <div class="space-y-1 text-center">
        <h1 class="text-xl font-black text-slate-900">Lupa Kata Sandi?</h1>
        <p class="text-xs text-slate-500 leading-relaxed">
            Masukkan alamat email yang terdaftar. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4 text-xs">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-bold text-slate-700 mb-1">Alamat Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-500/20 transition flex items-center justify-center gap-1.5">
            <i class="fa fa-envelope text-[10px]"></i>
            <span>Kirim Tautan Reset Sandi</span>
        </button>

        <div class="pt-3 border-t border-slate-100 text-center">
            <a href="{{ route('login') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                &larr; Kembali ke Halaman Masuk
            </a>
        </div>
    </form>
</x-guest-layout>
