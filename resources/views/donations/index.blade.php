<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        <!-- Modern High-Impact Hero Banner Donasi -->
        <div class="relative rounded-3xl overflow-hidden bg-slate-900 text-white shadow-2xl border border-emerald-500/20">
            <!-- Ambient Background Image with Dark Emerald Overlay -->
            <div class="absolute inset-0 z-0">
                <img 
                    src="https://adcolaw.com/wp-content/uploads/2022/12/Foto-Cianjur-Press-Release-2.webp" 
                    alt="Galang Donasi Amal" 
                    class="w-full h-full object-cover opacity-25 filter blur-[1px] scale-105"
                >
                <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-emerald-950/90 to-teal-950/80"></div>
            </div>

            <!-- Decorative Light Glow Effects -->
            <div class="pointer-events-none absolute -top-24 -left-24 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -right-24 w-96 h-96 bg-teal-500/20 rounded-full blur-3xl"></div>

            <!-- Banner Content -->
            <div class="relative z-10 p-6 sm:p-10 lg:p-12 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8">
                <div class="max-w-2xl space-y-4">
                    <!-- Main Title -->
                    <h1 class="text-2xl sm:text-4xl font-black tracking-tight leading-tight">
                        Salurkan Kepedulian,
                        <br class="hidden sm:inline">
                            Wujudkan Kebaikan Bersama.
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-xl font-medium">
                        Platform galang dana transparan komunitas Sumatera Barat. Donasi Anda disalurkan 100% utuh tanpa potongan biaya platform apapun.
                    </p>

                    <!-- Quick Action & Admin Create Button -->
                    <div class="pt-2 flex flex-wrap items-center gap-3">
                        @if(auth()->check() && (auth()->user()->isCommunityAdmin() || auth()->user()->isSuperAdmin()))
                            <a href="{{ route('donations.create') }}" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs sm:text-sm shadow-lg shadow-emerald-500/25 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                                <i class="fa-solid fa-plus-circle"></i>
                                Buka Program Donasi Baru
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Right Side: Key Metric Glass Cards -->
                <div class="w-full lg:w-auto grid grid-cols-1 sm:grid-cols-3 lg:flex lg:flex-col gap-3 shrink-0">
                    <!-- Metric Card 1: Total Terkumpul -->
                    <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 shadow-lg space-y-1 sm:min-w-[220px]">
                        <div class="flex items-center justify-between text-emerald-300 text-[11px] font-bold">
                            <span>Total Donasi Terkumpul</span>
                            <i class="fa-solid fa-vault text-xs"></i>
                        </div>
                        <p class="text-lg sm:text-xl font-black text-white font-mono">
                            Rp{{ number_format($totalCollected, 0, ',', '.') }}
                        </p>
                    </div>

                    <!-- Metric Card 2: 0% Potongan Biaya -->
                    <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 shadow-lg space-y-1 sm:min-w-[220px]">
                        <div class="flex items-center justify-between text-teal-300 text-[11px] font-bold">
                            <span>Biaya Potongan Platform</span>
                            <i class="fa-solid fa-percent text-xs"></i>
                        </div>
                        <p class="text-lg sm:text-xl font-black text-emerald-400">
                            0% (Bebas Potongan)
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donations Grid -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-black text-slate-900">Program Donasi Aktif</h2>
                    <p class="text-[11px] text-slate-500">Pilih program donasi sosial dan kemanusiaan dari komunitas kami.</p>
                </div>
            </div>

            @if($donations->isEmpty())
                <div class="p-8 rounded-2xl bg-white border border-slate-200 text-center text-slate-500">
                    <i class="fa fa-hand-holding-heart text-3xl text-slate-300 mb-1"></i>
                    <p class="text-xs font-bold text-slate-800">Belum ada program donasi aktif saat ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($donations as $donation)
                        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:border-emerald-300 hover:shadow-md transition flex flex-col justify-between">
                            <div class="h-36 bg-slate-100 relative overflow-hidden">
                                <img src="{{ $donation->banner_url }}" alt="{{ $donation->title }}" class="w-full h-full object-cover">
                                <div class="absolute top-2 right-2 px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-600 text-white shadow-sm">
                                    0% Potongan Biaya
                                </div>
                            </div>

                            <div class="p-4 flex-grow flex flex-col justify-between space-y-3">
                                <div class="space-y-2">
                                    <span class="text-[9px] font-extrabold text-emerald-600 uppercase tracking-wider">
                                        {{ $donation->community->name ?? 'Inisiatif Sosial Hub' }}
                                    </span>
                                    <h3 class="font-extrabold text-slate-800 text-xs sm:text-sm line-clamp-1 hover:text-emerald-600 transition">
                                        <a href="{{ route('donations.show', $donation->slug) }}">{{ $donation->title }}</a>
                                    </h3>
                                    <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">{{ $donation->description }}</p>

                                    <!-- Progress Bar -->
                                    <div class="space-y-1 pt-1">
                                        <div class="flex justify-between text-[11px] font-bold">
                                            <span class="text-emerald-700">Rp{{ number_format($donation->collected_amount, 0, ',', '.') }}</span>
                                            <span class="text-slate-400">Target: Rp{{ number_format($donation->target_amount, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                            <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $donation->progressPercentage() }}%"></div>
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ route('donations.show', $donation->slug) }}" class="w-full text-center py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-sm transition">
                                    Salurkan Donasi &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $donations->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
