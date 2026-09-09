<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 space-y-6">
        
        <!-- Navigation & Action Bar (Hidden during Print) -->
        <div class="flex items-center justify-between gap-3 print:hidden">
            <a href="{{ route('communities.show', $auction->community->slug) }}?tab=auctions" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                <i class="fa fa-arrow-left text-[11px]"></i>
                <span>Kembali ke Komunitas</span>
            </a>

            <div class="flex items-center gap-2">
                <a href="{{ route('auctions.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa fa-gavel text-[11px]"></i>
                    <span>Katalog Lelang</span>
                </a>
                <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-200 transition flex items-center gap-2">
                    <i class="fa fa-print text-xs"></i>
                    <span>Cetak Dokumen (PDF)</span>
                </button>
            </div>
        </div>

        <!-- Official Certificate Container -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden print:shadow-none print:border-slate-400 print:rounded-none">
            
            <!-- Document Header Banner -->
            <div class="bg-gradient-to-r from-slate-950 via-indigo-950 to-slate-900 text-white p-6 sm:p-8 relative overflow-hidden print:bg-white print:text-slate-900 print:border-b-2 print:border-slate-800">
                <div class="absolute -right-10 -bottom-10 opacity-10 text-white text-9xl pointer-events-none print:hidden">
                    <i class="fa fa-gavel"></i>
                </div>

                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-widest bg-indigo-500/30 text-indigo-200 border border-indigo-400/30 print:border-slate-400 print:text-slate-800 print:bg-slate-100">
                                DOKUMEN BUKTI TRANSAKSI RESMI
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white print:text-slate-900">
                            KUTIPAN HASIL LELANG COMMUNITYHUB
                        </h1>
                        <p class="text-xs text-indigo-200/80 print:text-slate-600">
                            Penetapan Pemenang & Akta Serah Terima Transaksi Lelang Komunitas
                        </p>
                    </div>

                    <div class="sm:text-right space-y-1 flex-shrink-0">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-indigo-300 print:text-slate-500 block">
                            Nomor Registrasi Lelang
                        </span>
                        <p class="text-base sm:text-lg font-black font-mono text-indigo-100 print:text-slate-900 bg-white/10 print:bg-slate-100 px-3 py-1 rounded-xl inline-block border border-white/10 print:border-slate-300">
                            {{ $auction->auction_code ?: 'CH-AUC-'.$auction->id }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Status & Meta Ribbon -->
            <div class="px-6 sm:px-8 py-3.5 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2">
                    <span class="text-slate-500">Status Transaksi:</span>
                    @if($auction->isPaid())
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1">
                            <i class="fa fa-check-circle"></i>
                            <span>Lunas & Selesai</span>
                        </span>
                    @elseif($auction->isWanprestasi())
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-200 flex items-center gap-1">
                            <i class="fa fa-triangle-exclamation"></i>
                            <span>Wanprestasi (Gugur)</span>
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200 flex items-center gap-1">
                            <i class="fa fa-hourglass-half"></i>
                            <span>Menunggu Pelunasan</span>
                        </span>
                    @endif
                </div>

                <div class="text-slate-500 text-[11px]">
                    Waktu Dokumen Diterbitkan: <strong class="text-slate-700">{{ now()->format('d F Y, H:i') }} WIB</strong>
                </div>
            </div>

            <!-- Document Body Content -->
            <div class="p-6 sm:p-8 space-y-6 text-xs text-slate-700">
                
                <!-- Section 1: Item & Community Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-6 border-b border-slate-200">
                    <div class="md:col-span-2 space-y-3">
                        <h2 class="text-xs font-black uppercase tracking-wider text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                            <span>1. Objek Barang & Komunitas Penyelenggara</span>
                        </h2>

                        <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-200 space-y-2.5">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Judul Barang Lelang</span>
                                <h3 class="text-base font-black text-slate-900">{{ $auction->title }}</h3>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Komunitas Penyelenggara</span>
                                    <p class="font-bold text-slate-800 text-xs">{{ $auction->community->name }}</p>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Pelelang / Penjual</span>
                                    <p class="font-bold text-slate-800 text-xs">{{ $auction->creator->name }} <span class="text-[10px] text-slate-500 font-mono">(@ {{ $auction->creator->username }})</span></p>
                                </div>
                            </div>

                            @if($auction->description)
                                <div class="pt-2 border-t border-slate-200">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Keterangan Barang</span>
                                    <p class="text-slate-600 text-[11px] leading-relaxed">{{ $auction->description }}</p>
                                </div>
                            @endif

                            @if($auction->notes)
                                <div class="pt-2 border-t border-slate-200">
                                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Ketentuan / Catatan Serah Terima</span>
                                    <p class="text-slate-600 text-[11px] leading-relaxed">{{ $auction->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Item Photo Thumbnail -->
                    <div class="space-y-2">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Dokumentasi Visual Objek</span>
                        <div class="h-44 rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 flex items-center justify-center">
                            <img src="{{ $auction->image_url }}" alt="{{ $auction->title }}" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Winning Bidder & Fair Process -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-slate-200">
                    <div class="space-y-3">
                        <h2 class="text-xs font-black uppercase tracking-wider text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                            <span>2. Pemenang Lelang Sah (Highest Bidder)</span>
                        </h2>

                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $auction->winner ? $auction->winner->avatar_url : asset('default-avatar.png') }}" alt="Avatar" class="w-12 h-12 rounded-xl object-cover border border-slate-200">
                                <div>
                                    <h4 class="font-black text-sm text-slate-900">{{ $auction->winner->name ?? '-' }}</h4>
                                    <p class="text-[11px] text-slate-500 font-mono">{{ $auction->winner ? '@'.$auction->winner->username : '-' }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[9px] font-extrabold {{ $feeData['is_member'] ? 'bg-indigo-50 text-indigo-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $feeData['is_member'] ? 'Anggota Komunitas Resmi (Diskon Fee)' : 'Penawar Umum' }}
                                    </span>
                                </div>
                            </div>

                            <div class="text-[11px] text-slate-600 pt-2 border-t border-slate-100 space-y-1">
                                <div>Kontak Email: <strong>{{ $auction->winner->email ?? '-' }}</strong></div>
                                <div>Total Riwayat Tawaran: <strong>{{ $auction->totalBids() }} tawaran sah tercatat</strong></div>
                            </div>
                        </div>
                    </div>

                    <!-- Fair Play & Runner-up Verification -->
                    <div class="space-y-3">
                        <h2 class="text-xs font-black uppercase tracking-wider text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                            <span>3. Ketentuan Integritas & Pemenang Cadangan</span>
                        </h2>

                        <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-200 space-y-2.5 text-[11px]">
                            <div class="flex items-center justify-between pb-1.5 border-b border-slate-200">
                                <span class="text-slate-500">Mekanisme Anti-Sniping:</span>
                                <strong class="text-emerald-700">{{ $auction->anti_sniping ? 'Aktif (+2 Menit Perpanjangan)' : 'Non-aktif' }}</strong>
                            </div>

                            <div class="flex items-center justify-between pb-1.5 border-b border-slate-200">
                                <span class="text-slate-500">Pemenang Cadangan (Runner-up):</span>
                                <strong class="text-slate-800">
                                    @if($auction->runnerUp)
                                        {{ $auction->runnerUp->name }} (Rp{{ number_format($auction->runner_up_bid, 0, ',', '.') }})
                                    @else
                                        -
                                    @endif
                                </strong>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Tenggat Pelunasan Pemenang:</span>
                                <strong class="text-slate-800">{{ $auction->payment_deadline ? $auction->payment_deadline->format('d M Y, H:i') . ' WIB' : '48 Jam Pasca Penetapan' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Financial Settlement Matrix -->
                <div class="space-y-3 pb-6 border-b border-slate-200">
                    <h2 class="text-xs font-black uppercase tracking-wider text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                        <span>4. Rincian Finansial Transaksi Lelang</span>
                    </h2>

                    <div class="bg-slate-50 rounded-2xl border border-slate-200 overflow-hidden divide-y divide-slate-200">
                        <div class="p-3.5 flex items-center justify-between">
                            <span class="text-slate-600">Nilai Pembukaan (*Starting Price*)</span>
                            <span class="font-bold text-slate-800">Rp{{ number_format($auction->starting_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="p-3.5 flex items-center justify-between bg-slate-50">
                            <div>
                                <span class="font-bold text-slate-900">Pokok Lelang Terbentuk (*Winning Bid*)</span>
                                <p class="text-[10px] text-indigo-700">Tawaran tertinggi yang dinyatakan menang secara sah</p>
                            </div>
                            <span class="text-base font-black text-slate-900">Rp{{ number_format($auction->current_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="p-3.5 flex items-center justify-between">
                            <div>
                                <span class="text-slate-600">Biaya Layanan Komunitas ({{ $feeData['fee_percent'] }}%)</span>
                                <p class="text-[10px] text-slate-400">{{ $feeData['is_member'] ? 'Tarif khusus anggota komunitas terdaftar' : 'Tarif partisipan non-komunitas' }}</p>
                            </div>
                            <span class="font-bold text-slate-700">Rp{{ number_format($feeData['fee_amount'], 0, ',', '.') }}</span>
                        </div>
                        <div class="p-4 flex items-center justify-between bg-slate-900 text-white print:bg-slate-900">
                            <div>
                                <span class="text-xs font-black uppercase tracking-wider">TOTAL PELUNASAN TRANSAKSI</span>
                                <p class="text-[10px] text-indigo-200 print:text-slate-400">Total kewajiban yang dibayarkan pemenang lelang</p>
                            </div>
                            <span class="text-xl font-black tracking-tight">Rp{{ number_format($feeData['total_amount'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Integrity QR Code & Verification Signature -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 items-center pt-2">
                    
                    <!-- QR Code Digital Verification -->
                    <div class="flex flex-col items-center sm:items-start space-y-2">
                        <span class="text-[10px] text-slate-400 uppercase font-black tracking-wider">QR Code Verifikasi Sistem</span>
                        <div class="p-3 bg-white rounded-2xl border-2 border-slate-200 shadow-sm inline-block print:border-slate-800">
                            {!! $qrCode !!}
                        </div>
                        <p class="text-[9px] text-slate-400 text-center sm:text-left max-w-[180px]">
                            Pindai untuk memvalidasi keaslian dokumen lelang ini secara online di CommunityHub.
                        </p>
                    </div>

                    <!-- Platform Seal & Legal Statement -->
                    <div class="sm:col-span-2 space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        <div class="flex items-center gap-2">
                            <i class="fa fa-shield-halved text-indigo-600 text-sm"></i>
                            <span class="font-bold text-slate-800 text-xs">Jaminan Transparansi & Integritas CommunityHub</span>
                        </div>
                        <p class="text-[10px] text-slate-500 leading-relaxed">
                            Dokumen digital ini diterbitkan secara resmi oleh sistem <strong>CommunityHub</strong> sebagai bukti penetapan pemenang transaksi lelang komunitas yang sah dan mengikat antara penjual dan penawar. 
                        </p>
                        <p class="text-[9px] text-slate-400 italic leading-relaxed border-t border-slate-200 pt-2">
                            *Pernyataan Hukum: Dokumen ini merupakan bukti transaksi internal platform komunitas dan tidak terafiliasi dengan lelang eksekusi peradilan, DJKN, maupun lembaga lelang pemerintah RI.
                        </p>
                    </div>
                </div>

            </div>

            <!-- Footer Stamp -->
            <div class="bg-slate-100 p-4 text-center text-[10px] text-slate-400 border-t border-slate-200 font-mono">
                CommunityHub Ecosystem © {{ date('Y') }} • Dokumen Sah Digital Transaksi Komunitas
            </div>

        </div>

    </div>
</x-app-layout>
