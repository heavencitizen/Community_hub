<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-slate-200">
            <div>
                <h1 class="text-xl font-black text-slate-900">Pengaturan Akun & Profil</h1>
                <p class="text-xs text-slate-500">Kelola informasi pribadi, foto profil akun, dan keamanan kata sandi.</p>
            </div>
            <a href="{{ route('profile.show') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                &larr; Lihat Profil Publik
            </a>
        </div>

        <div class="p-5 bg-white border border-slate-200 shadow-sm rounded-2xl">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-5 bg-white border border-slate-200 shadow-sm rounded-2xl">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-5 bg-white border border-slate-200 shadow-sm rounded-2xl">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
