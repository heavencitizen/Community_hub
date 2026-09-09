<!-- Modern Unified Footer (Harmonis, Senada dengan Header & Seimbang Proporsional) -->
<footer class="bg-white text-slate-600 border-t border-slate-200 mt-16 relative overflow-hidden">

    <!-- 1. Top CTA Banner (Senada dengan Aksen Pastel Indigo Header) -->
    <div class="relative border-b border-slate-200/80 bg-gradient-to-r from-slate-50 via-indigo-50/40 to-slate-50 py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-1.5 text-center md:text-left">
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    Punya Komunitas atau Rencana Event di Sumbar?
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 max-w-xl">
                    Bangun ruang komunitas Anda sendiri, distribusikan tiket QR Code resmi, atau galang donasi amal secara transparan tanpa potongan.
                </p>
            </div>
            <div class="flex flex-wrap items-center justify-center md:justify-end gap-3 shrink-0">
                @auth
                    <a href="{{ route('communities.create') }}" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm shadow-md shadow-indigo-600/20 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                        <i class="fa-solid fa-plus-circle"></i>
                        Buat Komunitas Baru
                    </a>
                @else
                    <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm shadow-md shadow-indigo-600/20 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                        <i class="fa-solid fa-user-plus"></i>
                        Daftar Akun Gratis
                    </a>
                @endauth
                <a href="{{ route('events.index') }}" class="px-5 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 hover:text-indigo-600 font-bold text-xs sm:text-sm border border-slate-200 shadow-xs transition flex items-center gap-2">
                    <i class="fa-solid fa-ticket text-indigo-500"></i>
                    Jelajahi Event & Meetup
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Main Footer Grid (Sempurna & Seimbang 4 Kolom: 1 : 1 : 1 : 1) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-10">

            <!-- Kolom 1: Brand & Deskripsi -->
            <div class="space-y-4">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logos/communityhub.jpeg') }}" alt="CommunityHub" class="w-8 h-8 rounded-lg object-cover shadow-sm">
                    <div>
                        <span class="font-extrabold text-base text-slate-900 tracking-tight">Community<span class="text-indigo-600">Hub</span></span>
                        <span class="block text-[9px] uppercase font-bold tracking-widest text-slate-400">Sumatera Barat</span>
                    </div>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">
                    Platform ekosistem komunitas dan sosial media hobi nomor 1 di Ranah Minang. Menghubungkan ribuan penggiat hobi, jual beli & lelang aman, tiket resmi ber-QR Code, serta aksi peduli sesama.
                </p>
            </div>

            <!-- Kolom 2: Eksplorasi Komunitas & Hobi -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900">Eksplorasi Komunitas</h4>
                <ul class="space-y-2 text-xs text-slate-600 font-medium">
                    <li>
                        <a href="{{ route('communities.index') }}" class="hover:text-indigo-600 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-angle-right text-[10px] text-slate-400"></i>
                            Katalog Semua Komunitas
                        </a>
                    </li>
                    <li>
                        <a href="{{ auth()->check() ? route('communities.create') : route('login') }}" class="hover:text-indigo-600 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-angle-right text-[10px] text-slate-400"></i>
                            Buat Komunitas Baru
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('communities.index') }}" class="hover:text-indigo-600 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-angle-right text-[10px] text-slate-400"></i>
                            Jual Beli Perlengkapan Hobi
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('communities.index') }}" class="hover:text-indigo-600 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-angle-right text-[10px] text-slate-400"></i>
                            Sesi Lelang Terverifikasi
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('articles.index') }}" class="hover:text-indigo-600 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-angle-right text-[10px] text-slate-400"></i>
                            Kabar & Cerita Hobi
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}" class="hover:text-indigo-600 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-angle-right text-[10px] text-slate-400"></i>
                            Ruang Diskusi & Feed
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Kolom 3: Event & Donasi Amal -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900">Event & Aksi Sosial</h4>
                <ul class="space-y-2 text-xs text-slate-600 font-medium">
                    <li>
                        <a href="{{ route('events.index') }}" class="hover:text-indigo-600 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-angle-right text-[10px] text-slate-400"></i>
                            Jadwal Meetup & Gathering
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('events.index') }}" class="hover:text-indigo-600 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-angle-right text-[10px] text-slate-400"></i>
                            Tiket Resmi E-Ticket QR Code
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('donations.index') }}" class="hover:text-indigo-600 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-angle-right text-[10px] text-slate-400"></i>
                            Galang Donasi & Aksi Peduli
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('donations.index') }}" class="hover:text-indigo-600 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-angle-right text-[10px] text-slate-400"></i>
                            Donasi 100% Bebas Potongan
                        </a>
                    </li>
                    <li>
                        <a href="{{ auth()->check() ? route('profile.tickets') : route('login') }}" class="hover:text-indigo-600 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-angle-right text-[10px] text-slate-400"></i>
                            Tiket Saya (E-Ticket)
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('events.index') }}" class="hover:text-indigo-600 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-angle-right text-[10px] text-slate-400"></i>
                            Verifikasi Tiket Penyelenggara
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Kolom 4: Bantuan, Kontak & Keamanan -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900">Bantuan & Keamanan</h4>
                <div class="space-y-2.5 text-xs text-slate-600">
                    <div class="flex items-start gap-2">
                        <i class="fa-solid fa-location-dot text-indigo-500 mt-0.5 w-3.5 shrink-0"></i>
                        <span>Kota Padang, Sumatera Barat, Indonesia</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-envelope text-indigo-500 w-3.5 shrink-0"></i>
                        <span>bantuan@communityhub.id</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-headset text-indigo-500 w-3.5 shrink-0"></i>
                        <span>Layanan CS: 08.00 - 22.00 WIB</span>
                    </div>
                </div>

                <!-- Terhubung Bersama Kami (Media Sosial) -->
                <div class="pt-2 border-t border-slate-100">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-900 mb-2">Terhubung Bersama Kami</p>
                    <div class="flex items-center gap-2">
                        <a href="https://instagram.com" target="_blank" rel="noopener" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-pink-50 text-slate-600 hover:text-pink-600 border border-slate-200/80 flex items-center justify-center text-xs transition shadow-2xs hover:scale-105" title="Instagram">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="https://whatsapp.com" target="_blank" rel="noopener" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-600 border border-slate-200/80 flex items-center justify-center text-xs transition shadow-2xs hover:scale-105" title="WhatsApp">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                        <a href="https://tiktok.com" target="_blank" rel="noopener" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-900 text-slate-600 hover:text-white border border-slate-200/80 flex items-center justify-center text-xs transition shadow-2xs hover:scale-105" title="TikTok">
                            <i class="fa-brands fa-tiktok"></i>
                        </a>
                        <a href="https://youtube.com" target="_blank" rel="noopener" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-red-50 text-slate-600 hover:text-red-600 border border-slate-200/80 flex items-center justify-center text-xs transition shadow-2xs hover:scale-105" title="YouTube">
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                        <a href="https://telegram.org" target="_blank" rel="noopener" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-sky-50 text-slate-600 hover:text-sky-600 border border-slate-200/80 flex items-center justify-center text-xs transition shadow-2xs hover:scale-105" title="Telegram">
                            <i class="fa-brands fa-telegram"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Official Payment Methods Strip (Tersusun Rapi, Proporsional & Bersih) -->
        <div class="border-t border-slate-200/80 mt-10 pt-8">
            <div class="p-5 sm:p-6 rounded-2xl bg-slate-50/70 border border-slate-200/70 shadow-xs space-y-4">
                <!-- Header: Judul + Tagline + Badge Keamanan -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200/60">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <h5 class="text-xs font-black uppercase tracking-wider text-slate-900">
                                Metode Pembayaran Resmi & Terverifikasi
                            </h5>
                        </div>
                        <p class="text-[11px] text-slate-500">
                            Didukung Virtual Account Bank Nasional, QRIS Standar BI, dan E-Wallet Resmi
                        </p>
                    </div>
                </div>

                <!-- Uniform Badges Grid (Symmetrical Height & Balanced Padding) -->
                @php
                $paymentChannels = [
                    ['code' => 'bca', 'name' => 'Bank Central Asia (BCA)'],
                    ['code' => 'mandiri', 'name' => 'Bank Mandiri'],
                    ['code' => 'bni', 'name' => 'Bank BNI 46'],
                    ['code' => 'bri', 'name' => 'Bank BRI'],
                    ['code' => 'btn', 'name' => 'Bank BTN'],
                    ['code' => 'cimb', 'name' => 'CIMB Niaga'],
                    ['code' => 'mega', 'name' => 'Bank Mega'],
                    ['code' => 'maybank', 'name' => 'Maybank Indonesia'],
                    ['code' => 'seabank', 'name' => 'SeaBank'],
                    ['code' => 'allobank', 'name' => 'Allo Bank'],
                    ['code' => 'qris', 'name' => 'QRIS Standar Nasional'],
                    ['code' => 'gopay', 'name' => 'GoPay Indonesia'],
                    ['code' => 'ovo', 'name' => 'OVO Payment'],
                    ['code' => 'dana', 'name' => 'DANA Dompet Digital'],
                    ['code' => 'shopeepay', 'name' => 'ShopeePay'],
                ];
                @endphp
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 sm:gap-2.5">
                    @foreach($paymentChannels as $pay)
                            <x-brand-logo :name="$pay['code']" class="max-h-5 sm:max-h-6 max-w-[50px] sm:max-w-[58px] w-auto h-auto object-contain" />
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 4. Bottom Copyright & Legal Links (Clean Balance) -->
        <div class="border-t border-slate-200/80 mt-8 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-500">
            <p class="text-center sm:text-left">
                &copy; {{ date('Y') }} <span class="text-slate-900 font-bold">CommunityHub</span>. Platform Komunitas & Aksi Amal Sumatera Barat.
            </p>
            <div class="flex items-center gap-4 text-slate-500">
                <a href="#" class="hover:text-indigo-600 transition">Kebijakan Privasi</a>
                <span>&bull;</span>
                <a href="#" class="hover:text-indigo-600 transition">Syarat & Ketentuan</a>
                <span>&bull;</span>
                <a href="#" class="hover:text-indigo-600 transition">Panduan Keamanan</a>
            </div>
        </div>

    </div>
</footer>
