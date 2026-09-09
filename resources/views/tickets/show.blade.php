<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 py-6">
        <!-- Compact E-Ticket Card -->
        <div class="rounded-3xl bg-white border border-slate-200 shadow-xl overflow-hidden">
            <!-- Ticket Header -->
            <div class="p-6 bg-gradient-to-r from-slate-900 via-indigo-900 to-slate-900 border-b border-indigo-500 text-white space-y-2">
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black tracking-wider uppercase bg-white/20 text-white backdrop-blur-sm">
                        E-TICKET RESMI
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $ticket->status === 'approved' ? 'bg-emerald-500 text-white' : ($ticket->status === 'pending' ? 'bg-amber-500 text-white' : 'bg-rose-500 text-white') }}">
                        {{ $ticket->status === 'approved' ? 'LUNAS' : $ticket->status }}
                    </span>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black">{{ $ticket->event->title }}</h1>
                    <p class="text-xs text-indigo-100 flex items-center gap-1.5 mt-0.5">
                        <img src="{{ $ticket->event->community->logo_url }}" alt="{{ $ticket->event->community->name }}" class="w-4 h-4 rounded object-cover">
                        <span>{{ $ticket->event->community->name }}</span>
                    </p>
                </div>
            </div>

            <!-- Ticket Body -->
            <div class="p-6 space-y-5">
                <!-- QR Code Display -->
                <div class="flex flex-col items-center justify-center p-5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 space-y-3">
                    @if($ticket->status === 'approved')
                        <div class="p-3 bg-white rounded-2xl shadow-sm border border-slate-200">
                            {!! $qrCode !!}
                        </div>
                        <div class="text-center">
                            <span class="text-[10px] text-slate-400 uppercase font-bold">Kode Tiket Masuk</span>
                            <p class="text-lg font-black font-mono tracking-widest text-indigo-900">{{ $ticket->ticket_code }}</p>
                        </div>
                        @if($ticket->is_scanned)
                            <div class="px-3 py-1 rounded-full bg-emerald-600 text-white text-[10px] font-bold flex items-center gap-1">
                                <i class="fa fa-check-circle"></i>
                                <span>SUDAH DI-SCAN (CHECK-IN VALID)</span>
                            </div>
                        @else
                            <p class="text-[11px] text-slate-500 text-center max-w-xs">Tunjukkan QR Code ini kepada panitia event di lokasi untuk verifikasi masuk.</p>
                        @endif
                    @elseif($ticket->status === 'pending')
                        <div class="py-4 text-center space-y-1.5">
                            <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto text-base animate-spin">
                                <i class="fa fa-spinner"></i>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800">Menunggu Pembayaran</h3>
                            <p class="text-xs text-slate-500 max-w-xs">QR Code akan aktif otomatis setelah pembayaran lunas.</p>
                        </div>
                    @else
                        <div class="py-4 text-center space-y-1.5">
                            <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto text-base">
                                <i class="fa fa-times-circle"></i>
                            </div>
                            <h3 class="text-sm font-bold text-rose-700">Tiket Dibatalkan</h3>
                        </div>
                    @endif
                </div>

                <!-- Event & Attendee Info Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-0.5">
                        <span class="text-slate-400 font-bold uppercase text-[10px]">Nama Pemegang Tiket</span>
                        <p class="text-slate-800 font-bold text-xs">{{ $ticket->user->name }}</p>
                        <p class="text-slate-500 text-[10px]">{{ $ticket->user->email }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-0.5">
                        <span class="text-slate-400 font-bold uppercase text-[10px]">Waktu & Lokasi</span>
                        <p class="text-slate-800 font-bold text-xs">{{ $ticket->event->event_date->format('d M Y, H:i') }} WIB</p>
                        <p class="text-slate-500 text-[10px] line-clamp-1">{{ $ticket->event->location }}</p>
                    </div>
                </div>

                <!-- EO / Panitia Action: Scan In Gate -->
                @if(auth()->user()->isSuperAdmin() || $ticket->event->community->user_id === auth()->id())
                    <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                        <span class="font-bold text-indigo-600 text-xs">Panel Panitia EO:</span>
                        @if(!$ticket->is_scanned && $ticket->status === 'approved')
                            <form action="{{ route('admin.tickets.scan', $ticket->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition shadow-sm">
                                    ✓ Tandai Hadir (Scan)
                                </button>
                            </form>
                        @endif
                    </div>
                @endif

                <div class="flex justify-between items-center pt-3 border-t border-slate-100 text-xs">
                    <a href="{{ route('profile.tickets') }}" class="font-bold text-slate-500 hover:text-slate-800 transition">
                        &larr; Riwayat Tiket
                    </a>
                    <button onclick="window.print()" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition border border-slate-200">
                        🖨 Cetak Tiket
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
