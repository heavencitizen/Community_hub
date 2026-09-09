<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900">Riwayat E-Ticket Saya</h1>
                <p class="text-xs text-slate-500">Daftar tiket event, status verifikasi pembayaran, dan QR Code masuk.</p>
            </div>
            <a href="{{ route('events.index') }}" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm transition">
                + Pesan Tiket Baru
            </a>
        </div>

        @if($tickets->isEmpty())
            <div class="p-8 rounded-2xl bg-white border border-slate-200 text-center text-slate-500">
                <i class="fa fa-ticket text-3xl text-slate-300 mb-1"></i>
                <p class="text-sm font-bold text-slate-800">Belum ada riwayat pembelian tiket.</p>
                <a href="{{ route('events.index') }}" class="inline-block mt-2 text-xs font-bold text-indigo-600 hover:underline">
                    Jelajahi event seru sekarang &rarr;
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($tickets as $ticket)
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-indigo-200 hover:shadow-md transition flex flex-col justify-between space-y-3">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-0.2 rounded text-[9px] font-extrabold uppercase {{ $ticket->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($ticket->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                    {{ $ticket->status === 'approved' ? 'LUNAS (SIAP DIGUNAKAN)' : $ticket->status }}
                                </span>
                                <span class="font-mono text-[10px] text-slate-400 font-bold">#{{ $ticket->ticket_code }}</span>
                            </div>

                            <h3 class="font-bold text-slate-800 text-xs sm:text-sm line-clamp-1">{{ $ticket->event->title }}</h3>

                            <div class="space-y-1 text-[11px] text-slate-500 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                <p><i class="fa fa-calendar-day text-indigo-500 mr-1 text-[10px]"></i> {{ $ticket->event->event_date->format('d M Y, H:i') }} WIB</p>
                                <p class="line-clamp-1"><i class="fa fa-map-marker-alt text-slate-400 mr-1 text-[10px]"></i> {{ $ticket->event->location }}</p>
                                <p><i class="fa fa-receipt text-emerald-500 mr-1 text-[10px]"></i> Total: <strong class="text-emerald-600 font-bold">Rp{{ number_format($ticket->total_price, 0, ',', '.') }}</strong></p>
                            </div>
                        </div>

                        <a href="{{ route('tickets.show', $ticket->id) }}" class="w-full text-center py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-sm transition">
                            Lihat E-Ticket & QR Code &rarr;
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
