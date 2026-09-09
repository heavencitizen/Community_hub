<section>
    <header>
        <h2 class="text-lg font-bold text-slate-900">
            Informasi Profil & Foto Akun
        </h2>

        <p class="mt-1 text-xs text-slate-500">
            Perbarui foto profil, nama lengkap, dan alamat email akun Anda.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Banner Cover Upload -->
        <div class="space-y-2">
            <x-input-label for="banner" value="Foto Sampul / Banner Profil (Rasio Lebar)" />
            <div class="w-full h-32 rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 relative group shadow-inner">
                <img src="{{ $user->banner_url }}" alt="Banner {{ $user->name }}" class="w-full h-full object-cover">
            </div>
            <input id="banner" name="banner" type="file" accept="image/*" class="mt-1 block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100" />
            <p class="text-[10px] text-slate-400">Rekomendasi ukuran: 1200x400 px, format JPG, PNG, atau WEBP maks 3MB.</p>
            <x-input-error class="mt-1" :messages="$errors->get('banner')" />
        </div>

        <!-- Avatar Upload -->
        <div class="flex items-center gap-5">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-2xl object-cover border-2 border-indigo-500/30 shadow-md">
            <div>
                <x-input-label for="avatar" value="Foto Profil / Avatar Baru" />
                <input id="avatar" name="avatar" type="file" accept="image/*" class="mt-1 block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100" />
                <p class="text-[10px] text-slate-400">Format JPG, PNG, atau WEBP maks 2MB.</p>
                <x-input-error class="mt-1" :messages="$errors->get('avatar')" />
            </div>
        </div>

        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full text-xs py-2.5" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="username" value="Username (@handle)" />
            <div class="relative mt-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 font-mono text-xs">@</span>
                <input id="username" name="username" type="text" class="block w-full pl-8 text-xs py-2.5 rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 font-mono" value="{{ old('username', $user->username) }}" required placeholder="username" />
            </div>
            <p class="text-[10px] text-slate-400 mt-1">Tautan profil publik Anda: <span class="font-mono text-indigo-600">{{ url('/users/') }}/{{ $user->username }}</span></p>
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full text-xs py-2.5" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="bio" value="Bio / Tentang Saya" />
            <textarea id="bio" name="bio" rows="3" class="mt-1 block w-full rounded-xl border-slate-300 text-xs p-3 focus:border-indigo-500 focus:ring-indigo-500 placeholder-slate-400 resize-none" placeholder="Tulis deskripsi singkat tentang diri Anda, hobi, atau minat komunitas...">{{ old('bio', $user->bio) }}</textarea>
            <p class="text-[10px] text-slate-400 mt-1">Maksimum 500 karakter.</p>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition">
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated' || session('success'))
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-xs text-emerald-600 font-bold"
                >Tersimpan!</p>
            @endif
        </div>
    </form>
</section>
