<x-app-layout>
    @php
        // Filter khusus memanggil transaksi yang pembayarannya sudah sukses/lunas
        $donators = $donation->transactions()
            ->where('payment_status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();
    @endphp

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 space-y-6" x-data="{ donationAmount: 50000, customAmount: 50000 }">
        <div class="rounded-2xl overflow-hidden bg-white border border-slate-200 shadow-sm">
            <div class="h-48 sm:h-60 bg-slate-900 relative">
                <img src="{{ $donation->banner_url }}" alt="{{ $donation->title }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent"></div>
                
                <div class="absolute bottom-4 left-4 right-4 text-white space-y-1">
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500 text-white uppercase tracking-wider">
                        0% Potongan Biaya Platform
                    </span>
                    <h1 class="text-lg sm:text-2xl font-black">{{ $donation->title }}</h1>
                    <p class="text-[11px] text-slate-300">Inisiator: <strong>{{ $donation->user->name }}</strong> • {{ $donation->community->name ?? 'Gerakan Sosial' }}</p>
                </div>
            </div>

            <div class="p-4 sm:p-6 grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left: Info & Description -->
                <div class="lg:col-span-7 space-y-4">
                    <div>
                        <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider mb-2">Tentang Program Donasi</h2>
                        <div class="prose prose-sm text-slate-600 leading-relaxed whitespace-pre-line text-xs">
                            {{ $donation->description }}
                        </div>
                    </div>

                    <!-- Riwayat Donatur -->
                    <div class="pt-4 border-t border-slate-100 space-y-2">
                        <div class="flex items-center justify-between">
                            <h3 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Donatur Terkini</h3>
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">{{ $donators->count() }} Orang</span>
                        </div>
                        
                        <div class="space-y-2">
                            @forelse($donators as $trx)
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0">
                                                <i class="fa fa-hand-holding-heart text-[10px]"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 text-[11px]">{{ $trx->customer_name ?? 'Hamba Allah' }}</p>
                                                <p class="text-[9px] text-slate-400">{{ $trx->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <span class="font-black text-emerald-600 text-xs shrink-0">Rp{{ number_format($trx->amount, 0, ',', '.') }}</span>
                                    </div>
                                    
                                    <!-- Logika Pemanggil Doa / Pesan Kebaikan -->
                                    @if($trx->notes)
                                        <div class="mt-2 text-[11px] text-slate-500 italic bg-white p-2.5 rounded-lg border border-slate-100 relative">
                                            <i class="fa fa-quote-left text-slate-300 absolute top-2.5 left-2.5 text-[8px]"></i>
                                            <p class="pl-4 relative z-10 leading-relaxed">{{ $trx->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-6 border border-dashed border-slate-200 rounded-xl bg-slate-50">
                                    <i class="fa fa-heart text-2xl text-slate-300 mb-1"></i>
                                    <p class="text-xs text-slate-400">Jadilah donatur pertama untuk program kebaikan ini!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right: Donation Form & Progress -->
                <div class="lg:col-span-5 space-y-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                        <!-- Progress Box -->
                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500 font-semibold text-[11px]">Terkumpul:</span>
                                <span class="text-emerald-600 font-black text-xs">Rp{{ number_format($donation->collected_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $donation->progressPercentage() }}%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] text-slate-400">
                                <span>{{ $donation->progressPercentage() }}% Tercapai</span>
                                <span>Target: Rp{{ number_format($donation->target_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Donation Form -->
                        <form action="{{ route('donations.donate', $donation->id) }}" method="POST" class="space-y-3 text-xs">
                            @csrf
                            <div>
                                <label class="block font-bold text-slate-800 mb-1.5 text-[11px]">Pilih Nominal Donasi</label>
                                <div class="grid grid-cols-2 gap-1.5">
                                    <button type="button" @click="donationAmount = 25000; customAmount = 25000" :class="donationAmount === 25000 ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-700 border-slate-200'" class="p-2 rounded-lg border font-bold text-xs transition">
                                        Rp25.000
                                    </button>
                                    <button type="button" @click="donationAmount = 50000; customAmount = 50000" :class="donationAmount === 50000 ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-700 border-slate-200'" class="p-2 rounded-lg border font-bold text-xs transition">
                                        Rp50.000
                                    </button>
                                    <button type="button" @click="donationAmount = 100000; customAmount = 100000" :class="donationAmount === 100000 ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-700 border-slate-200'" class="p-2 rounded-lg border font-bold text-xs transition">
                                        Rp100.000
                                    </button>
                                    <button type="button" @click="donationAmount = 250000; customAmount = 250000" :class="donationAmount === 250000 ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-700 border-slate-200'" class="p-2 rounded-lg border font-bold text-xs transition">
                                        Rp250.000
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-0.5 text-[11px]">Nominal Lainnya (Rp)</label>
                                <input type="number" name="amount" x-model="customAmount" @input="donationAmount = customAmount" min="5000" required placeholder="Minimal Rp5.000" class="w-full rounded-xl bg-white border border-slate-200 text-xs py-2 px-3">
                            </div>

                            @guest
                                <div>
                                    <label class="block font-bold text-slate-700 mb-0.5 text-[11px]">Nama Donatur</label>
                                    <input type="text" name="guest_name" required placeholder="Hamba Allah / Nama Anda" class="w-full rounded-xl bg-white border border-slate-200 text-xs py-1.5 px-3">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 mb-0.5 text-[11px]">Email</label>
                                    <input type="email" name="guest_email" required placeholder="email@domain.com" class="w-full rounded-xl bg-white border border-slate-200 text-xs py-1.5 px-3">
                                </div>
                            @endguest

                            <div>
                                <label class="block font-bold text-slate-700 mb-0.5 text-[11px]">Doa / Pesan Kebaikan</label>
                                <textarea name="notes" rows="2" placeholder="Tuliskan doa atau pesan..." class="w-full rounded-xl bg-white border border-slate-200 text-xs py-1.5 px-3 resize-none"></textarea>
                            </div>

                            <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition flex items-center justify-center gap-1.5">
                                <i class="fa fa-heart text-[10px]"></i>
                                <span>Lanjutkan Pembayaran &rarr;</span>
                            </button>

                            <p class="text-[9px] text-center text-slate-400">
                                🔒 100% donasi diteruskan tanpa potongan fee. QRIS & Bank VA tersedia.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>