<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6" x-data="{ selectedMethod: 'qris', methodCategory: 'qris' }">
        <!-- Compact Breadcrumbs & Status -->
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-200">
            <div class="flex items-center gap-2 text-xs">
                <a href="{{ route('home') }}" class="text-slate-500 hover:text-indigo-600">Beranda</a>
                <span class="text-slate-300">/</span>
                <span class="font-bold text-slate-800">Checkout Pembayaran</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    Menunggu Pembayaran
                </span>
                <span class="text-[11px] font-mono text-slate-400">#{{ $transaction->transaction_code }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Left 7 Columns: Standardized Payment Method Selector -->
            <div class="lg:col-span-7 space-y-4">
                <div class="space-y-4">
                    <input type="hidden" name="payment_method" :value="selectedMethod">

                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa fa-shield-halved text-indigo-600"></i>
                                <span>Pilih Metode Pembayaran Resmi</span>
                            </h2>
                            <span class="text-[10px] text-slate-400 font-semibold">Otomatis Terverifikasi</span>
                        </div>

                        <!-- 1. QRIS & E-Wallet -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">QRIS (Semua E-Wallet & Mobile Banking)</label>
                            <div @click="selectedMethod = 'qris'" :class="selectedMethod === 'qris' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-3 rounded-xl border cursor-pointer transition flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <x-brand-logo name="qris" class="h-6 w-auto" />
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-xs">QRIS Universal Instan</h4>
                                        <p class="text-[10px] text-slate-500">BCA, Mandiri, BRI, BNI, GoPay, OVO, DANA, ShopeePay</p>
                                    </div>
                                </div>
                                <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center" :class="selectedMethod === 'qris' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300'">
                                    <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="selectedMethod === 'qris'"></div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Virtual Account Bank Resmi -->
                        <div class="space-y-2 pt-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Virtual Account (Transfer Bebas Biaya Admin)</label>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <!-- BCA VA -->
                                <div @click="selectedMethod = 'bca_va'" :class="selectedMethod === 'bca_va' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-2.5 rounded-xl border cursor-pointer transition flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <x-brand-logo name="bca" class="h-5 w-auto" />
                                        <span class="font-bold text-xs text-slate-800">BCA VA</span>
                                    </div>
                                    <div class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center" :class="selectedMethod === 'bca_va' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300'">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="selectedMethod === 'bca_va'"></div>
                                    </div>
                                </div>

                                <!-- Mandiri VA -->
                                <div @click="selectedMethod = 'mandiri_va'" :class="selectedMethod === 'mandiri_va' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-2.5 rounded-xl border cursor-pointer transition flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <x-brand-logo name="mandiri" class="h-5 w-auto" />
                                        <span class="font-bold text-xs text-slate-800">Mandiri VA</span>
                                    </div>
                                    <div class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center" :class="selectedMethod === 'mandiri_va' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300'">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="selectedMethod === 'mandiri_va'"></div>
                                    </div>
                                </div>

                                <!-- BNI VA -->
                                <div @click="selectedMethod = 'bni_va'" :class="selectedMethod === 'bni_va' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-2.5 rounded-xl border cursor-pointer transition flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <x-brand-logo name="bni" class="h-5 w-auto" />
                                        <span class="font-bold text-xs text-slate-800">BNI VA</span>
                                    </div>
                                    <div class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center" :class="selectedMethod === 'bni_va' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300'">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="selectedMethod === 'bni_va'"></div>
                                    </div>
                                </div>

                                <!-- BRI VA -->
                                <div @click="selectedMethod = 'bri_va'" :class="selectedMethod === 'bri_va' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-2.5 rounded-xl border cursor-pointer transition flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <x-brand-logo name="bri" class="h-5 w-auto" />
                                        <span class="font-bold text-xs text-slate-800">BRI BRIVA</span>
                                    </div>
                                    <div class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center" :class="selectedMethod === 'bri_va' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300'">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="selectedMethod === 'bri_va'"></div>
                                    </div>
                                </div>

                                <!-- BTN VA -->
                                <div @click="selectedMethod = 'btn_va'" :class="selectedMethod === 'btn_va' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-2.5 rounded-xl border cursor-pointer transition flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <x-brand-logo name="btn" class="h-5 w-auto" />
                                        <span class="font-bold text-xs text-slate-800">Bank BTN VA</span>
                                    </div>
                                    <div class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center" :class="selectedMethod === 'btn_va' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300'">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="selectedMethod === 'btn_va'"></div>
                                    </div>
                                </div>

                                <!-- CIMB Niaga VA -->
                                <div @click="selectedMethod = 'cimb_va'" :class="selectedMethod === 'cimb_va' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-2.5 rounded-xl border cursor-pointer transition flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <x-brand-logo name="cimb" class="h-5 w-auto" />
                                        <span class="font-bold text-xs text-slate-800">CIMB Niaga VA</span>
                                    </div>
                                    <div class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center" :class="selectedMethod === 'cimb_va' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300'">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="selectedMethod === 'cimb_va'"></div>
                                    </div>
                                </div>

                                <!-- Bank Mega VA -->
                                <div @click="selectedMethod = 'mega_va'" :class="selectedMethod === 'mega_va' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-2.5 rounded-xl border cursor-pointer transition flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <x-brand-logo name="mega" class="h-5 w-auto" />
                                        <span class="font-bold text-xs text-slate-800">Bank Mega VA</span>
                                    </div>
                                    <div class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center" :class="selectedMethod === 'mega_va' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300'">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="selectedMethod === 'mega_va'"></div>
                                    </div>
                                </div>

                                <!-- Maybank VA -->
                                <div @click="selectedMethod = 'maybank_va'" :class="selectedMethod === 'maybank_va' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-2.5 rounded-xl border cursor-pointer transition flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <x-brand-logo name="maybank" class="h-5 w-auto" />
                                        <span class="font-bold text-xs text-slate-800">Maybank VA</span>
                                    </div>
                                    <div class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center" :class="selectedMethod === 'maybank_va' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300'">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="selectedMethod === 'maybank_va'"></div>
                                    </div>
                                </div>

                                <!-- SeaBank VA -->
                                <div @click="selectedMethod = 'seabank_va'" :class="selectedMethod === 'seabank_va' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-2.5 rounded-xl border cursor-pointer transition flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <x-brand-logo name="seabank" class="h-5 w-auto" />
                                        <span class="font-bold text-xs text-slate-800">SeaBank VA</span>
                                    </div>
                                    <div class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center" :class="selectedMethod === 'seabank_va' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300'">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="selectedMethod === 'seabank_va'"></div>
                                    </div>
                                </div>

                                <!-- Allo Bank VA -->
                                <div @click="selectedMethod = 'allobank_va'" :class="selectedMethod === 'allobank_va' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-2.5 rounded-xl border cursor-pointer transition flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <x-brand-logo name="allobank" class="h-5 w-auto" />
                                        <span class="font-bold text-xs text-slate-800">Allo Bank VA</span>
                                    </div>
                                    <div class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center" :class="selectedMethod === 'allobank_va' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300'">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="selectedMethod === 'allobank_va'"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. E-Wallets Langsung -->
                        <div class="space-y-2 pt-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">E-Wallet Langsung</label>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <!-- GoPay -->
                                <div @click="selectedMethod = 'gopay'" :class="selectedMethod === 'gopay' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-2 rounded-xl border cursor-pointer transition flex flex-col items-center gap-1 text-center">
                                    <x-brand-logo name="gopay" class="h-4 w-auto" />
                                </div>

                                <!-- OVO -->
                                <div @click="selectedMethod = 'ovo'" :class="selectedMethod === 'ovo' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-2 rounded-xl border cursor-pointer transition flex flex-col items-center gap-1 text-center">
                                    <x-brand-logo name="ovo" class="h-4 w-auto" />
                                </div>

                                <!-- DANA -->
                                <div @click="selectedMethod = 'dana'" :class="selectedMethod === 'dana' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-2 rounded-xl border cursor-pointer transition flex flex-col items-center gap-1 text-center">
                                    <x-brand-logo name="dana" class="h-4 w-auto" />
                                </div>

                                <!-- ShopeePay -->
                                <div @click="selectedMethod = 'shopeepay'" :class="selectedMethod === 'shopeepay' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-2 rounded-xl border cursor-pointer transition flex flex-col items-center gap-1 text-center">
                                    <x-brand-logo name="shopeepay" class="h-4 w-auto" />
                                </div>
                            </div>
                        </div>

                        <!-- 4. Kartu Kredit & Debit -->
                        <div class="space-y-2 pt-1">
                            <div @click="selectedMethod = 'credit_card'" :class="selectedMethod === 'credit_card' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-500' : 'border-slate-200 bg-white hover:bg-slate-50'" class="p-3 rounded-xl border cursor-pointer transition flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <x-brand-logo name="credit_card" class="h-5 w-auto" />
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-xs">Kartu Kredit / Debit Online</h4>
                                        <p class="text-[10px] text-slate-500">Visa, Mastercard, JCB (3D Secure)</p>
                                    </div>
                                </div>
                                <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center" :class="selectedMethod === 'credit_card' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300'">
                                    <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="selectedMethod === 'credit_card'"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4 pb-2">
                            <!-- Menggunakan atribut onclick memanggil fungsi JavaScript langsung -->
                            <button id="pay-button" onclick="payWithMidtrans()" type="button" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-black text-xs shadow-lg shadow-indigo-500/30 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                <i class="fa fa-lock text-xs opacity-80"></i>
                                <span class="tracking-wide">BAYAR SEKARANG &bull; Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                            </button>
                            <p class="text-center text-[10px] text-slate-400 mt-3 font-medium">Pembayaran akan diproses secara aman oleh <span class="font-bold text-indigo-500">Midtrans</span></p>
                        </div>
                    </div>
                </div>

                <!-- Compact Instructions Accordion -->
                <div class="bg-white rounded-2xl border border-slate-200 p-4 space-y-2 text-xs" x-data="{ openGuide: false }">
                    <button type="button" @click="openGuide = !openGuide" class="w-full flex items-center justify-between font-bold text-slate-700">
                        <span class="flex items-center gap-2">
                            <i class="fa fa-circle-question text-indigo-500"></i>
                            <span>Panduan Cara Pembayaran</span>
                        </span>
                        <i class="fa fa-chevron-down text-[10px] transition-transform" :class="openGuide ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="openGuide" x-cloak class="pt-2 text-[11px] text-slate-500 space-y-1.5 leading-relaxed border-t border-slate-100 mt-2">
                        <p>1. Pilih metode pembayaran di atas dan klik <strong>Bayar Sekarang</strong>.</p>
                        <p>2. Kode pembayaran / nomor VA / barcode QRIS akan terbit secara instan.</p>
                        <p>3. Selesaikan transfer dari aplikasi mobile banking / e-wallet pengguna.</p>
                        <p>4. Sistem akan otomatis memverifikasi dalam hitungan detik tanpa perlu konfirmasi manual.</p>
                    </div>
                </div>
            </div>

            <!-- Right 5 Columns: Compact Order Summary & Fee Breakdown -->
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4 text-xs">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider pb-2 border-b border-slate-100">
                        Rincian Pesanan
                    </h3>

                    <!-- Item Detail -->
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.2 rounded text-[9px] font-black uppercase {{ $transaction->type === 'donation' ? 'bg-emerald-100 text-emerald-700' : ($transaction->type === 'product' ? 'bg-indigo-100 text-indigo-700' : ($transaction->type === 'auction' ? 'bg-indigo-50 text-indigo-700' : 'bg-blue-100 text-blue-700')) }}">
                                {{ $transaction->type }}
                            </span>
                            <span class="text-[10px] text-slate-400">Kode: {{ $transaction->transaction_code }}</span>
                        </div>
                        <h4 class="font-bold text-slate-800 text-xs">
                            @if($item)
                                {{ $item->name ?? $item->title }}
                            @else
                                {{ $transaction->notes }}
                            @endif
                        </h4>
                        <p class="text-[11px] text-slate-500 line-clamp-1">{{ $transaction->notes }}</p>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="space-y-2 pt-1 text-[11px]">
                        <div class="flex justify-between text-slate-600">
                            <span>Harga Pokok:</span>
                            <span class="font-bold text-slate-800">Rp{{ number_format($transaction->amount, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between text-slate-600">
                            <span class="flex items-center gap-1">
                                <span>Fee Platform ({{ $transaction->platform_fee_percent }}%):</span>
                                @if($transaction->type === 'donation')
                                    <span class="text-[9px] text-emerald-600 font-bold">(0% Donasi)</span>
                                @endif
                            </span>
                            <span class="font-bold {{ $transaction->platform_fee_amount == 0 ? 'text-emerald-600' : 'text-slate-800' }}">
                                {{ $transaction->platform_fee_amount == 0 ? 'Rp0 (Bebas Biaya)' : 'Rp' . number_format($transaction->platform_fee_amount, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="border-t border-slate-100 pt-2 flex justify-between items-center text-xs font-black">
                            <span class="text-slate-900">Total Tagihan:</span>
                            <span class="text-indigo-600 text-sm font-black">Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Buyer info -->
                    <div class="pt-3 border-t border-slate-100 text-[10px] text-slate-400 space-y-0.5">
                        <p>Pembeli / Donatur: <strong class="text-slate-700">{{ $transaction->customer_name }}</strong></p>
                        @if($transaction->customer_email)
                            <p>Email: <strong class="text-slate-700">{{ $transaction->customer_email }}</strong></p>
                        @endif
                    </div>
                </div>

                <!-- Trust Badge -->
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-center space-y-1">
                    <p class="text-[10px] font-bold text-slate-600 flex items-center justify-center gap-1.5">
                        <i class="fa fa-lock text-emerald-500"></i>
                        <span>Transaksi Terenkripsi 256-Bit SSL</span>
                    </p>
                    <p class="text-[9px] text-slate-400">Pembayaran terhubung langsung dengan sistem kliring perbankan nasional.</p>
                </div>
            </div>
        </div>
    </div>

   <!-- Script Midtrans & SweetAlert2 -->
   <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        async function payWithMidtrans() {
            const payButton = document.getElementById('pay-button');
            const originalContent = payButton.innerHTML;
            
            payButton.innerHTML = '<i class="fa fa-spinner fa-spin text-xs opacity-80"></i><span class="tracking-wide">MEMPROSES...</span>';
            payButton.disabled = true;

            const selectedMethod = document.querySelector('input[name="payment_method"]').value;

            try {
                const response = await fetch("{{ route('payment.token', $transaction->transaction_code) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ payment_method: selectedMethod })
                });

                const data = await response.json();

                if (data.snap_token) {
                    snap.pay(data.snap_token, {
                        onSuccess: function(result){
                            window.location.href = "{{ route('payment.success', $transaction->transaction_code) }}";
                        },
                        onPending: function(result){
                            window.location.reload();
                        },
                        onError: function(result){
                            Swal.fire({
                                icon: 'error',
                                title: 'Transaksi Gagal',
                                text: 'Pembayaran gagal diproses oleh sistem.',
                                confirmButtonColor: '#4f46e5'
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        onClose: function(){
                            payButton.innerHTML = originalContent;
                            payButton.disabled = false;
                            
                            // Kirim sinyal pending ke backend
                            fetch("{{ route('payment.pendingNotification', $transaction->transaction_code) }}", {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                            });

                            // Notifikasi popup elegan pengganti alert() bawaan
                            Swal.fire({
                                icon: 'info',
                                title: 'Pembayaran Ditunda',
                                text: 'Silakan periksa ikon lonceng notifikasi pengguna untuk melanjutkan pembayaran nanti.',
                                confirmButtonColor: '#4f46e5',
                                confirmButtonText: 'Tutup'
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat',
                        text: data.error || "Kesalahan tidak dikenal saat menghubungi gerbang pembayaran.",
                        confirmButtonColor: '#4f46e5'
                    });
                    payButton.innerHTML = originalContent;
                    payButton.disabled = false;
                }
            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'warning',
                    title: 'Gangguan Koneksi',
                    text: 'Terjadi kesalahan saat terhubung ke server. Silakan coba lagi.',
                    confirmButtonColor: '#4f46e5'
                });
                payButton.innerHTML = originalContent;
                payButton.disabled = false;
            }
        }
    </script>
</x-app-layout>