<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        <!-- Header & Stats -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900">Verifikasi Tiket & Transaksi</h1>
                <p class="text-xs text-slate-500">Tinjau pembayaran tiket event, setujui status, dan kelola QR Code pengunjung.</p>
            </div>

            @if(auth()->user()->isSuperAdmin())
                <div class="flex gap-2">
                    <div class="px-3.5 py-1.5 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Total Komisi Platform</span>
                        <strong class="text-sm font-black text-indigo-700">Rp{{ number_format($platformFeeTotal, 0, ',', '.') }}</strong>
                    </div>
                </div>
            @endif
        </div>

        <!-- Filter Status -->
        <div class="p-3 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-wrap gap-1.5">
            <a href="{{ route('admin.tickets.index') }}" class="px-3 py-1 rounded-lg text-xs font-bold transition {{ !request('status') ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                Semua Status
            </a>
            <a href="{{ route('admin.tickets.index', ['status' => 'pending']) }}" class="px-3 py-1 rounded-lg text-xs font-bold transition {{ request('status') === 'pending' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                Menunggu (Pending)
            </a>
            <a href="{{ route('admin.tickets.index', ['status' => 'approved']) }}" class="px-3 py-1 rounded-lg text-xs font-bold transition {{ request('status') === 'approved' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                Disetujui (Approved)
            </a>
            <a href="{{ route('admin.tickets.index', ['status' => 'rejected']) }}" class="px-3 py-1 rounded-lg text-xs font-bold transition {{ request('status') === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                Ditolak (Rejected)
            </a>
        </div>

        <!-- Tickets Table -->
        <div class="overflow-x-auto rounded-2xl bg-white border border-slate-200 shadow-sm">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 uppercase font-bold text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Kode Tiket</th>
                        <th class="px-4 py-3">Event & Komunitas</th>
                        <th class="px-4 py-3">Nama Pembeli</th>
                        <th class="px-4 py-3">Total Bayar</th>
                        <th class="px-4 py-3">Status Masuk</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @if($tickets->isEmpty())
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400">
                                Tidak ada data tiket dalam antrean.
                            </td>
                        </tr>
                    @else
                        @foreach($tickets as $ticket)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-3 font-mono font-bold text-slate-800">#{{ $ticket->ticket_code }}</td>
                                <td class="px-4 py-3">
                                    <strong class="text-slate-800 block text-xs">{{ $ticket->event->title }}</strong>
                                    <span class="text-[10px] text-slate-400">{{ $ticket->event->community->name }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <strong class="text-slate-800 block text-xs">{{ $ticket->user->name }}</strong>
                                    <span class="text-[10px] text-slate-400">{{ $ticket->user->email }}</span>
                                </td>
                                <td class="px-4 py-3 font-bold text-emerald-600">
                                    Rp{{ number_format($ticket->total_price, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 space-y-0.5">
                                    <span class="inline-block px-2 py-0.2 rounded text-[9px] font-black uppercase {{ $ticket->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($ticket->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                        {{ $ticket->status }}
                                    </span>
                                    @if($ticket->is_scanned)
                                        <span class="block text-[10px] text-emerald-600 font-bold">✓ Sudah Masuk</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right space-x-1.5 whitespace-nowrap">
                                    <a href="{{ route('tickets.show', $ticket->id) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition border border-slate-200">
                                        Lihat
                                    </a>

                                    @if($ticket->status === 'pending')
                                        <form action="{{ route('admin.tickets.approve', $ticket->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] transition">
                                                Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.tickets.reject', $ticket->id) }}" method="POST" class="inline" onsubmit="return confirm('Tolak tiket ini?')">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-bold text-[11px] transition">
                                                Tolak
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    </div>
</x-app-layout>
