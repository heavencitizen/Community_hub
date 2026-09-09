<x-app-layout>
    <div class="max-w-xl mx-auto px-4 py-8">
        <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-xl text-center space-y-5">
            <!-- Compact Success Icon -->
            <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-2xl shadow-inner">
                <i class="fa fa-circle-check"></i>
            </div>

            <div class="space-y-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Pembayaran Berhasil Diverifikasi
                </span>
                <h1 class="text-xl font-black text-slate-900">Transaksi Lunas</h1>
                <p class="text-xs text-slate-500">
                    Kode Transaksi: <strong class="font-mono text-slate-800">{{ $transaction->transaction_code }}</strong>
                </p>
            </div>

            <!-- Receipt Breakdown -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-left space-y-3 text-xs">
                <div class="flex justify-between border-b border-slate-200 pb-2">
                    <span class="text-slate-500">Keterangan:</span>
                    <strong class="text-slate-800 text-right">{{ $transaction->notes }}</strong>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Metode Bayar:</span>
                    <div class="flex items-center gap-1.5">
                        <x-brand-logo :name="$transaction->payment_method" class="h-4 w-auto" />
                        <strong class="text-slate-800 uppercase text-[11px]">{{ str_replace('_', ' ', $transaction->payment_method) }}</strong>
                    </div>
                </div>

                @if($transaction->payment_code)
                    <div class="flex items-center justify-between bg-white p-2 rounded-xl border border-slate-200">
                        <span class="text-slate-500 text-[11px]">No. Referensi / VA:</span>
                        <div class="flex items-center gap-1.5">
                            <strong class="font-mono text-indigo-600 text-xs">{{ $transaction->payment_code }}</strong>
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $transaction->payment_code }}'); alert('Nomor referensi disalin! 📋')" class="text-slate-400 hover:text-indigo-600 text-[10px]">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                    </div>
                @endif

                <div class="flex justify-between">
                    <span class="text-slate-500">Waktu Bayar:</span>
                    <strong class="text-slate-800">{{ $transaction->updated_at->format('d M Y, H:i') }} WIB</strong>
                </div>

                <div class="flex justify-between">
                    <span class="text-slate-500">Harga Pokok:</span>
                    <strong class="text-slate-800">Rp{{ number_format($transaction->amount, 0, ',', '.') }}</strong>
                </div>

                <div class="flex justify-between">
                    <span class="text-slate-500">Fee Platform ({{ $transaction->platform_fee_percent }}%):</span>
                    <strong class="{{ $transaction->platform_fee_amount == 0 ? 'text-emerald-600' : 'text-slate-800' }}">
                        {{ $transaction->platform_fee_amount == 0 ? 'Rp0 (Bebas Biaya)' : 'Rp' . number_format($transaction->platform_fee_amount, 0, ',', '.') }}
                    </strong>
                </div>

                <div class="flex justify-between text-xs font-black border-t border-slate-200 pt-2">
                    <span class="text-slate-900">Total Dibayar:</span>
                    <span class="text-emerald-600 text-sm font-black">Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-2 pt-1 text-xs">
                @if($transaction->type === 'ticket' && $transaction->reference_id)
                    <a href="{{ route('tickets.show', $transaction->reference_id) }}" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-sm transition">
                        Lihat E-Ticket QR Code &rarr;
                    </a>
                @elseif($transaction->type === 'donation')
                    <a href="{{ route('donations.index') }}" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold shadow-sm transition">
                        Kembali ke Donasi Amal &rarr;
                    </a>
                @else
                    <a href="{{ route('communities.index') }}" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-sm transition">
                        Kembali ke Komunitas &rarr;
                    </a>
                @endif
                <a href="{{ route('dashboard') }}" class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                    Ke Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
