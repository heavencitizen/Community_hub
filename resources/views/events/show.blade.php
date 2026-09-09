<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        <!-- Compact Event Banner / Header -->
        <div class="relative h-48 sm:h-72 rounded-2xl overflow-hidden bg-slate-900 border border-slate-200 shadow-sm">
            <img src="{{ $event->banner_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-transparent"></div>
            <div class="absolute bottom-4 left-4 right-4 text-white">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-600 shadow-sm">{{ $event->community->name }}</span>
                <h1 class="text-xl sm:text-3xl font-black mt-1.5 drop-shadow-md">{{ $event->title }}</h1>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Main Content: Description & Details -->
            <div class="lg:col-span-7 space-y-4">
                <div class="p-5 rounded-2xl bg-white border border-slate-200 space-y-4 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100">
                        <div>
                            <span class="text-[10px] font-extrabold text-indigo-600 uppercase tracking-wider block">Penyelenggara</span>
                            <a href="{{ route('communities.show', $event->community->slug) }}" class="text-sm font-bold text-slate-800 hover:text-indigo-600 transition flex items-center gap-1.5 mt-0.5">
                                <img src="{{ $event->community->logo_url }}" alt="{{ $event->community->name }}" class="w-5 h-5 rounded-md object-cover">
                                <span>{{ $event->community->name }}</span>
                            </a>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                <i class="fa fa-user-friends me-1 text-slate-400"></i>
                                Kuota: <strong>{{ $event->remainingQuota() }}</strong> / {{ $event->quota }}
                            </span>
                        </div>
                    </div>

                    <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa fa-circle-info text-indigo-600"></i>
                        <span>Deskripsi & Informasi Kegiatan</span>
                    </h2>
                    <div class="text-slate-600 text-xs leading-relaxed space-y-2 whitespace-pre-line">
                        {{ $event->description ?? 'Tidak ada deskripsi rinci untuk event ini.' }}
                    </div>
                </div>
            </div>

            <!-- Sidebar: RSVP / Beli Tiket dengan Payment Gateway -->
            <div class="lg:col-span-5 space-y-4 sticky top-20">
                <div class="p-5 rounded-2xl bg-white border border-slate-200 space-y-4 shadow-sm">
                    <div class="space-y-1 pb-3 border-b border-slate-100">
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Harga Tiket Masuk</span>
                        <div class="flex items-baseline justify-between">
                            <span class="text-2xl font-black text-slate-900">
                                {{ $event->price == 0 ? 'GRATIS' : 'Rp' . number_format($event->price, 0, ',', '.') }}
                            </span>
                            @if($event->price > 0)
                                @php
                                    $isMember = auth()->check() ? $event->community->hasActiveMember(auth()->id()) : false;
                                    $feePercent = $isMember ? 2 : 5;
                                @endphp
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $isMember ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                                    Fee: {{ $feePercent }}% ({{ $isMember ? 'Member' : 'Umum' }})
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-2.5 text-xs text-slate-600">
                        <div class="flex items-start gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0 text-xs">
                                <i class="fa fa-calendar-alt"></i>
                            </div>
                            <div>
                                <strong class="block text-slate-800 text-[11px]">Tanggal</strong>
                                <span class="text-slate-500 text-[11px]">{{ $event->event_date->format('l, d F Y') }}</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0 text-xs">
                                <i class="fa-regular fa-clock"></i>
                            </div>
                            <div>
                                <strong class="block text-slate-800 text-[11px]">Waktu</strong>
                                <span class="text-slate-500 text-[11px]">{{ $event->event_date->format('H:i') }} WIB s/d Selesai</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0 text-xs">
                                <i class="fa fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <strong class="block text-slate-800 text-[11px]">Lokasi</strong>
                                <span class="text-slate-500 text-[11px] leading-relaxed">{{ $event->location }}</span>
                            </div>
                        </div>
                    </div>

                    @auth
                        @if($userTicket)
                            <div class="p-3.5 rounded-xl {{ $userTicket->status === 'approved' ? 'bg-emerald-50 border border-emerald-200 text-emerald-700' : ($userTicket->status === 'pending' ? 'bg-amber-50 border border-amber-200 text-amber-700' : 'bg-rose-50 border border-rose-200 text-rose-700') }}">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] font-bold uppercase">Status Tiket Anda:</span>
                                    <span class="px-2 py-0.2 rounded text-[9px] font-extrabold uppercase {{ $userTicket->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $userTicket->status }}
                                    </span>
                                </div>
                                <p class="text-xs">Kode: <strong class="font-mono text-slate-800">{{ $userTicket->ticket_code }}</strong></p>
                                <a href="{{ route('tickets.show', $userTicket->id) }}" class="mt-2 block w-full text-center py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition">
                                    Buka E-Ticket & QR Code &rarr;
                                </a>
                            </div>
                        @elseif($event->remainingQuota() <= 0)
                            <button disabled class="w-full py-2.5 rounded-xl bg-slate-100 text-slate-400 text-xs font-bold cursor-not-allowed border border-slate-200">
                                Kuota Tiket Habis
                            </button>
                        @else
                            <!-- Form Beli / RSVP Tiket Otomatis via Payment Gateway -->
                            <form action="{{ route('tickets.store', $event->id) }}" method="POST" class="space-y-3">
                                @csrf

                                @if($event->price > 0)
                                    <!-- Payment Gateway Badges Preview -->
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                                        <div class="flex items-center justify-between text-[11px]">
                                            <span class="text-slate-500">Metode Pembayaran:</span>
                                            <span class="text-emerald-600 font-bold">Otomatis Terverifikasi</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <x-brand-logo name="qris" class="h-4 w-auto" />
                                            <x-brand-logo name="bca" class="h-4 w-auto" />
                                            <x-brand-logo name="mandiri" class="h-4 w-auto" />
                                            <x-brand-logo name="bri" class="h-4 w-auto" />
                                            <x-brand-logo name="gopay" class="h-4 w-auto" />
                                            <x-brand-logo name="dana" class="h-4 w-auto" />
                                        </div>
                                        <p class="text-[10px] text-slate-400">Pembayaran instan QRIS, VA Bank & E-Wallet dengan E-Ticket QR Code otomatis.</p>
                                    </div>
                                @endif

                                <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-500/20 transition flex items-center justify-center gap-1.5">
                                    <i class="fa fa-ticket"></i>
                                    <span>{{ $event->price == 0 ? 'Daftar Tiket Gratis (RSVP)' : 'Pesan Tiket & Bayar Sekarang' }}</span>
                                </button>
                            </form>
                        @endif
                    @else
                        <div class="p-3.5 rounded-xl bg-slate-50 text-center space-y-2 border border-slate-200">
                            <p class="text-xs text-slate-600">Silakan masuk untuk memesan tiket event ini.</p>
                            <a href="{{ route('login') }}" class="block w-full py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition">
                                Masuk / Registrasi
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
