<x-guest-layout>
    <div class="space-y-1 text-center">
        <h1 class="text-xl font-black text-slate-900">Masuk ke Akun</h1>
        <p class="text-xs text-slate-500">Silakan masukkan email dan kata sandi Anda.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4 text-xs">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-bold text-slate-700 mb-1">Alamat Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@email.com" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="block font-bold text-slate-700">Kata Sandi</label>
                @if (Route::has('password.request'))
                    <a class="text-[11px] font-bold text-indigo-600 hover:underline" href="{{ route('password.request') }}">
                        Lupa Sandi?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" class="w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="text-xs text-slate-600">Ingat saya di perangkat ini</span>
            </label>
        </div>

        <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-500/20 transition flex items-center justify-center gap-1.5">
            <span>Masuk Sekarang</span>
            <i class="fa fa-arrow-right text-[10px]"></i>
        </button>

        <div class="pt-3 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:underline">Daftar Akun Baru (Gratis)</a>
            </p>
        </div>
    </form>
</x-guest-layout>
