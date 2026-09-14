<x-app-layout>
    <style>
        @keyframes marqueeSlow {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-33.333%); }
        }
        .animate-marquee-slow {
            display: flex;
            width: max-content;
            height: 100%;
            animation: marqueeSlow 180s linear infinite;
            will-change: transform;
            filter: blur(2px);
            transform: scale(1.02);
        }
        @keyframes marqueeLogos {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee-logos {
            display: flex;
            align-items: center;
            width: max-content;
            animation: marqueeLogos 35s linear infinite;
        }
        .animate-marquee-logos:hover {
            animation-play-state: paused;
        }
    </style>

    @php
    $marqueePhotos = [
        [
            'title' => 'Padang Runner & Marathon Club',
            'category' => 'Olahraga',
            'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSj3d_3cs8r25oa8kGauNquEgf3CIcntsYQA0UhpjAiel10mzoLmf-81ow&s=10',
        ],
        [
            'title' => 'Komunitas Fotografi Landscape Sumbar',
            'category' => 'Fotografi',
            'image' => 'https://www.mldspot.com/storage/generated/June2021/Foto-Prewedding-Foto-Pernikahan-Wedding-Photography-Jasa-Fotografi-Komunitas-fotografi-jakarta-4.jpg',
        ],
        [
            'title' => 'Kopi & Barista Khas Minang',
            'category' => 'Kuliner',
            'image' => 'https://jsni.solokkab.go.id/uploads/media_6881e36e40082.png',
        ],
        [
            'title' => 'Vespa & Motor Klasik Sunmori',
            'category' => 'Otomotif',
            'image' => 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=600&q=80',
        ],
        [
            'title' => 'Festival Musik & Live Concert Padang',
            'category' => 'Event Musik',
            'image' => 'https://pangansari.com/blog/wp-content/uploads/2025/03/keamanan-event.jpg',
        ],
        [
            'title' => 'Camping & Trekking Gunung Singgalang',
            'category' => 'Petualangan',
            'image' => 'https://www.jelajahsumbar.com/wp-content/uploads/2022/12/glamping_solok_radjo-1140x530.jpg',
        ],
        [
            'title' => 'Klub Esports & Gaming Sumbar',
            'category' => 'Esports',
            'image' => 'https://rricoid-assets.obs.ap-southeast-4.myhuaweicloud.com/berita/Bengkulu/o/1726412702191-Screenshot_(20)/4yhx22memnr5yzr.jpeg',
        ],
        [
            'title' => 'Bursa Jual Beli & Lelang Hobi',
            'category' => 'Lelang & Hobi',
            'image' => 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?auto=format&fit=crop&w=600&q=80',
        ],
        [
            'title' => 'Aksi Social & Donasi Peduli',
            'category' => 'Donasi Amal',
            'image' => 'https://ichef.bbci.co.uk/ace/ws/640/cpsprodpb/4ad1/live/c6c54030-d71b-11f0-aa87-6bb0a76c2b74.jpg.webp',
        ],
        [
            'title' => 'Workshop Painting & Galeri Seni',
            'category' => 'Seni Kreatif',
            'image' => 'https://kemenpar.go.id/_next/image?url=https%3A%2F%2Fapi.kemenpar.go.id%2Fstorage%2Fapp%2Fuploads%2Fpublic%2F6a8%2F58e%2Fac2%2F6a858eac28224211845725.jpeg&w=3840&q=75',
        ],
    ];
    @endphp

    <!-- 1. Hero Section - Moving Photos as Background with Enhanced Contrast -->
    <section class="relative overflow-hidden py-10 sm:py-20 min-h-[340px] sm:min-h-[460px] border-b border-slate-200 flex items-center justify-center">

        <!-- Full Background Photo Marquee -->
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none w-full h-full">
            <div class="animate-marquee-slow flex items-stretch h-full w-max">
                @foreach(array_merge($marqueePhotos, $marqueePhotos, $marqueePhotos) as $photo)
                    <img
                        src="{{ $photo['image'] }}"
                        alt="{{ $photo['title'] }}"
                        loading="eager"
                        class="h-full w-auto max-w-none block select-none opacity-80"
                    >
                @endforeach
            </div>
        </div>

        <!-- Dark Tint Overlay -->
        <div class="absolute inset-0 bg-slate-950/45 z-10"></div>

        <!-- Hero Content (Floating Glass Card with Sharp Contrast) -->
        <div class="relative z-20 max-w-3xl mx-auto px-4 sm:px-6 text-center w-full">
            <div class="bg-white/95 backdrop-blur-md rounded-3xl p-5 sm:p-8 lg:p-10 shadow-2xl border border-white/60 space-y-3 sm:space-y-4">

                <!-- Rotating Title -->
                <div
                    x-data="{
                        phrases: [
                            'Klub & Komunitas Sumbar!',
                            'Agenda Event & Meetup!',
                            'Arena Lelang Koleksi Hobi!',
                            'Etalase Jual Beli Komunitas!',
                            'Aksi Amal & Donasi Sosial!'
                        ],
                        currentIndex: 0,
                        show: true,
                        init() {
                            setInterval(() => {
                                this.show = false;
                                setTimeout(() => {
                                    this.currentIndex = (this.currentIndex + 1) % this.phrases.length;
                                    this.show = true;
                                }, 300);
                            }, 3200);
                        }
                    }"
                    class="min-h-[56px] sm:min-h-[90px] lg:min-h-[110px] flex flex-col justify-center items-center select-none"
                >
                    <h1 class="text-xl sm:text-3xl lg:text-4xl font-black tracking-tight leading-snug sm:leading-tight text-slate-900">
                        <span class="block text-slate-900">Temukan Teman Sehobi,</span>
                        <span class="block overflow-hidden pt-0.5 sm:pt-1">
                            <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 via-indigo-700 to-indigo-900 inline-block transition-all duration-300 ease-out transform"
                                  :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-4'"
                                  x-text="phrases[currentIndex]">
                                Klub & Komunitas Sumbar!
                            </span>
                        </span>
                    </h1>
                </div>

                <!-- Subtitle -->
                <p class="text-xs sm:text-sm text-slate-600 max-w-xl mx-auto leading-relaxed font-medium line-clamp-2 sm:line-clamp-none">
                    Temukan teman sehobi, ikuti lelang & jual beli, pesan tiket event resmi ber-QR Code,
                    serta salurkan donasi amal 100% bebas potongan.
                </p>

                <!-- Search with Dynamic Rotating Placeholder (Typewriter Effect) -->
                <div
                    x-data="{
                        phrases: [
                            'Cari barang hobi langka & koleksi antik...',
                            'Cari lelang diecast, vespa, kamera vintage...',
                            'Cari komunitas lari, diving mandeh, kopi...',
                            'Cari tiket meetup komunitas & konser amal...',
                            'Cari program donasi peduli & galang dana...'
                        ],
                        currentPhraseIndex: 0,
                        currentText: '',
                        isDeleting: false,
                        typingSpeed: 65,
                        pauseDuration: 2200,
                        init() {
                            this.typeEffect();
                        },
                        typeEffect() {
                            const fullText = this.phrases[this.currentPhraseIndex];
                            if (this.isDeleting) {
                                this.currentText = fullText.substring(0, this.currentText.length - 1);
                            } else {
                                this.currentText = fullText.substring(0, this.currentText.length + 1);
                            }

                            let delay = this.typingSpeed;
                            if (this.isDeleting) {
                                delay = this.typingSpeed / 2;
                            }

                            if (!this.isDeleting && this.currentText === fullText) {
                                delay = this.pauseDuration;
                                this.isDeleting = true;
                            } else if (this.isDeleting && this.currentText === '') {
                                this.isDeleting = false;
                                this.currentPhraseIndex = (this.currentPhraseIndex + 1) % this.phrases.length;
                                delay = 400;
                            }

                            setTimeout(() => this.typeEffect(), delay);
                        }
                    }"
                    class="max-w-md mx-auto pt-1 sm:pt-2"
                >
                    <!-- Form Diperbarui ke Route Pencarian Global -->
                    <form
                        action="{{ route('search.index') }}"
                        method="GET"
                        class="relative p-1 sm:p-1.5 rounded-xl bg-slate-50/95 border border-slate-200 shadow-inner flex items-center gap-1.5 sm:gap-2 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:bg-white focus-within:border-indigo-500 transition-all duration-200"
                    >
                        <i class="fa fa-search text-slate-400 pl-2.5 sm:pl-3 text-xs"></i>

                        <!-- Name diubah menjadi "q" -->
                        <input
                            type="text"
                            name="q"
                            :placeholder="currentText || 'Cari komunitas, lelang, tiket event, produk hobi...'"
                            placeholder="Cari komunitas, lelang, tiket event, produk hobi..."
                            class="flex-grow bg-transparent border-none text-slate-900 placeholder-slate-400 text-xs focus:ring-0 focus:outline-none py-1.5 px-1 font-medium transition-all"
                        >

                        <button
                            type="submit"
                            class="px-4 sm:px-5 py-1.5 sm:py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition-all duration-150 whitespace-nowrap active:scale-95"
                        >
                            Cari
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </section>

    <!-- 2. Clean Unified Category Filter Chips -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-4 sm:py-6">
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar -mx-4 px-4 sm:mx-auto sm:px-0 sm:grid sm:grid-cols-3 md:grid-cols-6 sm:gap-3 max-w-4xl">
            <!-- 1. Olahraga -->
            <a href="{{ route('communities.index', ['category' => 'olahraga']) }}" class="flex-shrink-0 inline-flex items-center gap-2 px-3 py-2 sm:p-3 rounded-xl bg-white border border-slate-200/90 hover:border-indigo-400 hover:shadow-sm transition group active:scale-95">
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-xs sm:text-sm group-hover:bg-indigo-600 group-hover:text-white transition flex-shrink-0">
                    <i class="fa-solid fa-person-running"></i>
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 whitespace-nowrap sm:truncate">Olahraga</h4>
                    <p class="hidden sm:block text-[9px] text-slate-400 truncate">Lari, Basket</p>
                </div>
            </a>

            <!-- 2. Otomotif -->
            <a href="{{ route('communities.index', ['category' => 'otomotif']) }}" class="flex-shrink-0 inline-flex items-center gap-2 px-3 py-2 sm:p-3 rounded-xl bg-white border border-slate-200/90 hover:border-indigo-400 hover:shadow-sm transition group active:scale-95">
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-xs sm:text-sm group-hover:bg-indigo-600 group-hover:text-white transition flex-shrink-0">
                    <i class="fa-solid fa-motorcycle"></i>
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 whitespace-nowrap sm:truncate">Otomotif</h4>
                    <p class="hidden sm:block text-[9px] text-slate-400 truncate">Vespa, Sunmori</p>
                </div>
            </a>

            <!-- 3. Kesehatan -->
            <a href="{{ route('communities.index', ['category' => 'kesehatan']) }}" class="flex-shrink-0 inline-flex items-center gap-2 px-3 py-2 sm:p-3 rounded-xl bg-white border border-slate-200/90 hover:border-indigo-400 hover:shadow-sm transition group active:scale-95">
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-xs sm:text-sm group-hover:bg-indigo-600 group-hover:text-white transition flex-shrink-0">
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 whitespace-nowrap sm:truncate">Kesehatan</h4>
                    <p class="hidden sm:block text-[9px] text-slate-400 truncate">Scuba, Yoga</p>
                </div>
            </a>

            <!-- 4. Kuliner -->
            <a href="{{ route('communities.index', ['category' => 'kuliner']) }}" class="flex-shrink-0 inline-flex items-center gap-2 px-3 py-2 sm:p-3 rounded-xl bg-white border border-slate-200/90 hover:border-indigo-400 hover:shadow-sm transition group active:scale-95">
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-xs sm:text-sm group-hover:bg-indigo-600 group-hover:text-white transition flex-shrink-0">
                    <i class="fa-solid fa-mug-hot"></i>
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 whitespace-nowrap sm:truncate">Kuliner</h4>
                    <p class="hidden sm:block text-[9px] text-slate-400 truncate">Kopi, Kuliner</p>
                </div>
            </a>

            <!-- 5. Rumah Tangga -->
            <a href="{{ route('communities.index', ['category' => 'rumah_tangga']) }}" class="flex-shrink-0 inline-flex items-center gap-2 px-3 py-2 sm:p-3 rounded-xl bg-white border border-slate-200/90 hover:border-indigo-400 hover:shadow-sm transition group active:scale-95">
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-xs sm:text-sm group-hover:bg-indigo-600 group-hover:text-white transition flex-shrink-0">
                    <i class="fa-solid fa-house-chimney-window"></i>
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 whitespace-nowrap sm:truncate">Rumah</h4>
                    <p class="hidden sm:block text-[9px] text-slate-400 truncate">Kebun, Dekor</p>
                </div>
            </a>

            <!-- 6. Seni Kreatif -->
            <a href="{{ route('communities.index', ['category' => 'seni']) }}" class="flex-shrink-0 inline-flex items-center gap-2 px-3 py-2 sm:p-3 rounded-xl bg-white border border-slate-200/90 hover:border-indigo-400 hover:shadow-sm transition group active:scale-95">
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-xs sm:text-sm group-hover:bg-indigo-600 group-hover:text-white transition flex-shrink-0">
                    <i class="fa-solid fa-palette"></i>
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 whitespace-nowrap sm:truncate">Seni</h4>
                    <p class="hidden sm:block text-[9px] text-slate-400 truncate">Musik, Foto</p>
                </div>
            </a>
        </div>
    </section>

    <!-- 3. Showcase Content: Horizontal Card Sliders for All Core Modules -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-4 space-y-10">

        <!-- ── 1. CAROUSEL: EVENT & MEETUP KOMUNITAS ── -->
        <div 
            x-data="{
                scroll(dir) {
                    const el = $refs.slider;
                    const amount = el.clientWidth * 0.75;
                    el.scrollBy({ left: dir === 'left' ? -amount : amount, behavior: 'smooth' });
                }
            }" 
            class="space-y-3"
        >
            <div class="flex items-center justify-between border-b border-slate-200/70 pb-2.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-xs flex-shrink-0">
                        <i class="fa fa-ticket"></i>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-black text-slate-900 leading-tight">Event & Meetup Komunitas</h2>
                        <p class="text-[11px] text-slate-500">Pesan E-Ticket ber-QR Code untuk meetup, touring, & workshop hobi.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-2.5">
                    <div class="hidden sm:flex items-center gap-1">
                        <button type="button" @click="scroll('left')" aria-label="Geser ke kiri" class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-indigo-300 text-slate-600 hover:text-indigo-600 flex items-center justify-center transition text-xs shadow-2xs active:scale-95">
                            <i class="fa fa-chevron-left text-[10px]"></i>
                        </button>
                        <button type="button" @click="scroll('right')" aria-label="Geser ke kanan" class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-indigo-300 text-slate-600 hover:text-indigo-600 flex items-center justify-center transition text-xs shadow-2xs active:scale-95">
                            <i class="fa fa-chevron-right text-[10px]"></i>
                        </button>
                    </div>
                    <a href="{{ route('events.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 flex-shrink-0">
                        <span>Semua Event ({{ $stats['total_events'] }})</span> <i class="fa fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Horizontal Slider -->
            <div x-ref="slider" class="flex items-stretch gap-3 sm:gap-4 overflow-x-auto no-scrollbar scroll-smooth pb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
                @forelse($upcomingEvents as $event)
                    <div class="w-[260px] sm:w-[285px] lg:w-[296px] flex-shrink-0 bg-white border border-slate-200 rounded-2xl overflow-hidden hover:border-indigo-300 hover:shadow-md transition flex flex-col justify-between">
                        <div class="h-32 bg-slate-100 relative overflow-hidden flex-shrink-0">
                            <img src="{{ $event->banner_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                            <div class="absolute top-2 right-2 px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-900/80 text-white backdrop-blur-xs shadow-sm">
                                {{ $event->price == 0 ? 'Gratis' : 'Rp' . number_format($event->price, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="p-3.5 flex-grow flex flex-col justify-between space-y-2.5">
                            <div class="space-y-1">
                                <span class="text-[9px] font-extrabold text-indigo-600 uppercase tracking-wider block truncate">{{ $event->community->name ?? 'Komunitas' }}</span>
                                <h3 class="font-bold text-slate-800 text-xs line-clamp-1">
                                    <a href="{{ route('events.show', $event->slug) }}" class="hover:text-indigo-600 transition">{{ $event->title }}</a>
                                </h3>
                                <p class="text-[10px] text-slate-400 flex items-center gap-1 pt-0.5">
                                    <i class="fa fa-calendar-alt text-[9px] text-slate-400"></i>
                                    <span>{{ $event->event_date->format('d M Y, H:i') }} WIB</span>
                                </p>
                            </div>
                            <a href="{{ route('events.show', $event->slug) }}" class="w-full text-center py-1.5 rounded-xl bg-slate-100 hover:bg-indigo-600 text-slate-700 hover:text-white text-xs font-bold transition active:scale-95">
                                Detail & Tiket &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="w-full p-6 text-center text-slate-400 text-xs bg-white rounded-2xl border border-slate-200">
                        Belum ada event terdaftar saat ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ── 2. CAROUSEL: KOMUNITAS TERPOPULER ── -->
        <div 
            x-data="{
                scroll(dir) {
                    const el = $refs.slider;
                    const amount = el.clientWidth * 0.75;
                    el.scrollBy({ left: dir === 'left' ? -amount : amount, behavior: 'smooth' });
                }
            }" 
            class="space-y-3"
        >
            <div class="flex items-center justify-between border-b border-slate-200/70 pb-2.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-xs flex-shrink-0">
                        <i class="fa fa-users"></i>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-black text-slate-900 leading-tight">Komunitas Populer</h2>
                        <p class="text-[11px] text-slate-500">Gabung forum diskusi, cari teman sehobi, dan bangun jejaring baru.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-2.5">
                    <div class="hidden sm:flex items-center gap-1">
                        <button type="button" @click="scroll('left')" aria-label="Geser ke kiri" class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-indigo-300 text-slate-600 hover:text-indigo-600 flex items-center justify-center transition text-xs shadow-2xs active:scale-95">
                            <i class="fa fa-chevron-left text-[10px]"></i>
                        </button>
                        <button type="button" @click="scroll('right')" aria-label="Geser ke kanan" class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-indigo-300 text-slate-600 hover:text-indigo-600 flex items-center justify-center transition text-xs shadow-2xs active:scale-95">
                            <i class="fa fa-chevron-right text-[10px]"></i>
                        </button>
                    </div>
                    <a href="{{ route('communities.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 flex-shrink-0">
                        <span>Semua ({{ $stats['total_communities'] }})</span> <i class="fa fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Horizontal Slider -->
            <div x-ref="slider" class="flex items-stretch gap-3 sm:gap-4 overflow-x-auto no-scrollbar scroll-smooth pb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
                @forelse($activeCommunities as $community)
                    <div class="w-[245px] sm:w-[275px] lg:w-[296px] flex-shrink-0 p-3.5 bg-white border border-slate-200 rounded-2xl hover:border-indigo-300 hover:shadow-md transition flex flex-col justify-between space-y-3">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2.5">
                                <img src="{{ $community->logo_url }}" alt="{{ $community->name }}" class="w-11 h-11 rounded-xl object-cover border border-slate-200 shadow-xs flex-shrink-0">
                                <div class="min-w-0">
                                    <h3 class="font-bold text-slate-800 text-xs truncate">
                                        <a href="{{ route('communities.show', $community->slug) }}" class="hover:text-indigo-600 transition">{{ $community->name }}</a>
                                    </h3>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-indigo-50 text-indigo-700 capitalize">{{ $community->category ?? 'Hobi' }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $community->members_count }} Anggota</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">{{ $community->description }}</p>
                        </div>
                        <a href="{{ route('communities.show', $community->slug) }}" class="w-full text-center py-1.5 rounded-xl bg-slate-100 hover:bg-indigo-600 text-slate-700 hover:text-white text-xs font-bold transition active:scale-95">
                            Kunjungi Komunitas &rarr;
                        </a>
                    </div>
                @empty
                    <div class="w-full p-6 text-center text-slate-400 text-xs bg-white rounded-2xl border border-slate-200">
                        Belum ada komunitas terdaftar saat ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ── 3. CAROUSEL: ARENA LELANG KOMUNITAS ── -->
        <div 
            x-data="{
                scroll(dir) {
                    const el = $refs.slider;
                    const amount = el.clientWidth * 0.75;
                    el.scrollBy({ left: dir === 'left' ? -amount : amount, behavior: 'smooth' });
                }
            }" 
            class="space-y-3"
        >
            <div class="flex items-center justify-between border-b border-slate-200/70 pb-2.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-xs flex-shrink-0">
                        <i class="fa-solid fa-gavel"></i>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-black text-slate-900 leading-tight">Arena Lelang Komunitas</h2>
                        <p class="text-[11px] text-slate-500">Lelang terbuka barang langka, koleksi hobi, & merchandise unik dengan anti-sniping.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-2.5">
                    <div class="hidden sm:flex items-center gap-1">
                        <button type="button" @click="scroll('left')" aria-label="Geser ke kiri" class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-indigo-300 text-slate-600 hover:text-indigo-600 flex items-center justify-center transition text-xs shadow-2xs active:scale-95">
                            <i class="fa fa-chevron-left text-[10px]"></i>
                        </button>
                        <button type="button" @click="scroll('right')" aria-label="Geser ke kanan" class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-indigo-300 text-slate-600 hover:text-indigo-600 flex items-center justify-center transition text-xs shadow-2xs active:scale-95">
                            <i class="fa fa-chevron-right text-[10px]"></i>
                        </button>
                    </div>
                    <a href="{{ route('auctions.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 flex-shrink-0">
                        <span>Semua Lelang ({{ $stats['total_live_auctions'] ?? 0 }})</span> <i class="fa fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Horizontal Slider -->
            <div x-ref="slider" class="flex items-stretch gap-3 sm:gap-4 overflow-x-auto no-scrollbar scroll-smooth pb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
                @forelse($featuredAuctions as $auction)
                    <div class="w-[260px] sm:w-[285px] lg:w-[296px] flex-shrink-0 bg-white border border-slate-200 rounded-2xl overflow-hidden hover:border-indigo-300 hover:shadow-md transition flex flex-col justify-between group">
                        <div class="h-32 bg-slate-100 relative overflow-hidden flex-shrink-0">
                            <img src="{{ $auction->image_url }}" alt="{{ $auction->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute top-2 left-2">
                                @if($auction->isActive())
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-500 text-white shadow flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        <span>LIVE LELANG</span>
                                    </span>
                                @elseif($auction->isScheduled())
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-sky-600 text-white shadow">
                                        TERJADWAL
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-slate-700 text-white shadow">
                                        SELESAI
                                    </span>
                                @endif
                            </div>
                            <div class="absolute bottom-2 left-2">
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-slate-900/80 text-white truncate max-w-[190px] block">
                                    {{ $auction->community->name ?? 'Komunitas' }}
                                </span>
                            </div>
                        </div>
                        <div class="p-3.5 flex-grow flex flex-col justify-between space-y-2.5">
                            <div class="space-y-1">
                                @if($auction->auction_code)
                                    <span class="text-[9px] font-mono text-indigo-600 font-bold block">{{ $auction->auction_code }}</span>
                                @endif
                                <h3 class="font-bold text-slate-800 text-xs line-clamp-1 group-hover:text-indigo-600 transition">
                                    {{ $auction->title }}
                                </h3>
                                <div class="flex items-baseline justify-between pt-1">
                                    <span class="text-[9px] text-slate-400">Tawaran:</span>
                                    <strong class="text-xs font-black text-slate-900">Rp{{ number_format($auction->current_price, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                            <a href="{{ route('communities.show', $auction->community->slug) }}?tab=auctions#auction-{{ $auction->id }}" class="w-full text-center py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition shadow-xs active:scale-95">
                                Ikuti Lelang &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="w-full p-6 text-center text-slate-400 text-xs bg-white rounded-2xl border border-slate-200">
                        Belum ada sesi lelang aktif saat ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ── 4. CAROUSEL: ETALASE JUAL BELI KOMUNITAS ── -->
        <div 
            x-data="{
                scroll(dir) {
                    const el = $refs.slider;
                    const amount = el.clientWidth * 0.75;
                    el.scrollBy({ left: dir === 'left' ? -amount : amount, behavior: 'smooth' });
                }
            }" 
            class="space-y-3"
        >
            <div class="flex items-center justify-between border-b border-slate-200/70 pb-2.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-xs flex-shrink-0">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-black text-slate-900 leading-tight">Etalase Jual Beli Komunitas</h2>
                        <p class="text-[11px] text-slate-500">Beli merchandise resmi, aksesoris, & produk kreatif antar-komunitas.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-2.5">
                    <div class="hidden sm:flex items-center gap-1">
                        <button type="button" @click="scroll('left')" aria-label="Geser ke kiri" class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-indigo-300 text-slate-600 hover:text-indigo-600 flex items-center justify-center transition text-xs shadow-2xs active:scale-95">
                            <i class="fa fa-chevron-left text-[10px]"></i>
                        </button>
                        <button type="button" @click="scroll('right')" aria-label="Geser ke kanan" class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-indigo-300 text-slate-600 hover:text-indigo-600 flex items-center justify-center transition text-xs shadow-2xs active:scale-95">
                            <i class="fa fa-chevron-right text-[10px]"></i>
                        </button>
                    </div>
                    <a href="{{ route('marketplace.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 flex-shrink-0">
                        <span>Semua Produk ({{ $stats['total_products'] }})</span> <i class="fa fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Horizontal Slider -->
            <div x-ref="slider" class="flex items-stretch gap-3 sm:gap-4 overflow-x-auto no-scrollbar scroll-smooth pb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
                @forelse($featuredProducts as $product)
                    <div class="w-[245px] sm:w-[275px] lg:w-[296px] flex-shrink-0 bg-white border border-slate-200 rounded-2xl overflow-hidden hover:border-indigo-300 hover:shadow-md transition flex flex-col justify-between group">
                        <div class="h-32 bg-slate-100 relative overflow-hidden flex-shrink-0">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute top-2 left-2">
                                @if($product->stock > 0)
                                    <span class="px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-wider bg-emerald-600 text-white shadow">
                                        Ready: {{ $product->stock }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-wider bg-slate-700 text-white shadow">
                                        Habis
                                    </span>
                                @endif
                            </div>
                            <div class="absolute bottom-2 left-2">
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-slate-900/80 text-white truncate max-w-[190px] block">
                                    {{ $product->community->name ?? 'Komunitas' }}
                                </span>
                            </div>
                        </div>
                        <div class="p-3.5 flex-grow flex flex-col justify-between space-y-2.5">
                            <div class="space-y-1">
                                <span class="text-[9px] font-bold text-indigo-600 uppercase tracking-wider block truncate capitalize">{{ $product->community->category ?? 'Hobi' }}</span>
                                <h3 class="font-bold text-slate-800 text-xs line-clamp-1 group-hover:text-indigo-600 transition">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-xs font-black text-slate-900 pt-0.5">
                                    Rp{{ number_format($product->price, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="grid grid-cols-2 gap-1.5 pt-1">
                                <a href="{{ route('communities.show', $product->community->slug) }}?tab=marketplace#product-{{ $product->id }}" class="text-center py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                                    Forum
                                </a>
                                <a href="{{ route('marketplace.index', ['search' => $product->name]) }}" class="text-center py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-bold transition shadow-xs">
                                    Beli
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="w-full p-6 text-center text-slate-400 text-xs bg-white rounded-2xl border border-slate-200">
                        Belum ada produk aktif yang dijual saat ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ── 5. CAROUSEL: SARAN TEMAN & PROFIL PENGGUNA ── -->
        <div 
            x-data="{
                scroll(dir) {
                    const el = $refs.slider;
                    const amount = el.clientWidth * 0.75;
                    el.scrollBy({ left: dir === 'left' ? -amount : amount, behavior: 'smooth' });
                }
            }" 
            class="space-y-3"
        >
            <div class="flex items-center justify-between border-b border-slate-200/70 pb-2.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-xs flex-shrink-0">
                        <i class="fa fa-user-group"></i>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-black text-slate-900 leading-tight">Kenalan & Teman Sehobi</h2>
                        <p class="text-[11px] text-slate-500">Jelajahi profil pengguna aktif dan temukan partner hobi di sekitarmu.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-2.5">
                    <div class="hidden sm:flex items-center gap-1">
                        <button type="button" @click="scroll('left')" aria-label="Geser ke kiri" class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-indigo-300 text-slate-600 hover:text-indigo-600 flex items-center justify-center transition text-xs shadow-2xs active:scale-95">
                            <i class="fa fa-chevron-left text-[10px]"></i>
                        </button>
                        <button type="button" @click="scroll('right')" aria-label="Geser ke kanan" class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-indigo-300 text-slate-600 hover:text-indigo-600 flex items-center justify-center transition text-xs shadow-2xs active:scale-95">
                            <i class="fa fa-chevron-right text-[10px]"></i>
                        </button>
                    </div>
                    <span class="text-xs font-mono font-bold text-slate-400 flex-shrink-0">{{ $stats['total_users'] }} Member</span>
                </div>
            </div>

            <!-- Horizontal Slider -->
            <div x-ref="slider" class="flex items-stretch gap-3 sm:gap-3.5 overflow-x-auto no-scrollbar scroll-smooth pb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
                @forelse($featuredUsers as $u)
                    <div class="w-[165px] sm:w-[185px] flex-shrink-0 p-3.5 bg-white border border-slate-200 rounded-2xl text-center hover:border-indigo-300 hover:shadow-md transition flex flex-col items-center justify-between space-y-2.5">
                        <a href="{{ route('users.show', $u->username) }}" class="block group">
                            <div class="relative mx-auto w-14 h-14 rounded-2xl overflow-hidden ring-2 ring-indigo-50 border border-slate-200 shadow-xs group-hover:scale-105 transition-transform duration-200">
                                <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="w-full h-full object-cover">
                            </div>
                        </a>
                        <div class="w-full">
                            <h4 class="font-bold text-xs text-slate-800 truncate">
                                <a href="{{ route('users.show', $u->username) }}" class="hover:text-indigo-600 transition">{{ $u->name }}</a>
                            </h4>
                            <span class="text-[10px] font-mono text-indigo-600 font-bold block truncate">{{ '@' . $u->username }}</span>
                            <p class="text-[9px] text-slate-400 mt-1">
                                <span>{{ $u->community_posts_count }} post</span> • <span>{{ $u->followers_count }} pengikut</span>
                            </p>
                        </div>
                        <a href="{{ route('users.show', $u->username) }}" class="w-full py-1.5 rounded-xl bg-slate-100 hover:bg-indigo-600 text-slate-700 hover:text-white text-[11px] font-bold transition active:scale-95">
                            Lihat Profil
                        </a>
                    </div>
                @empty
                    <div class="w-full p-6 text-center text-slate-400 text-xs bg-white rounded-2xl border border-slate-200">
                        Belum ada profil pengguna yang dipublikasikan.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ── 6. CAROUSEL: BERITA & ARTIKEL TERKINI ── -->
        <div 
            x-data="{
                scroll(dir) {
                    const el = $refs.slider;
                    const amount = el.clientWidth * 0.75;
                    el.scrollBy({ left: dir === 'left' ? -amount : amount, behavior: 'smooth' });
                }
            }" 
            class="space-y-3"
        >
            <div class="flex items-center justify-between border-b border-slate-200/70 pb-2.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-xs flex-shrink-0">
                        <i class="fa fa-newspaper"></i>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-black text-slate-900 leading-tight">Berita & Artikel Komunitas</h2>
                        <p class="text-[11px] text-slate-500">Liputan kegiatan, edukasi hobi, dan wawasan seputar Sumatera Barat.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-2.5">
                    <div class="hidden sm:flex items-center gap-1">
                        <button type="button" @click="scroll('left')" aria-label="Geser ke kiri" class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-indigo-300 text-slate-600 hover:text-indigo-600 flex items-center justify-center transition text-xs shadow-2xs active:scale-95">
                            <i class="fa fa-chevron-left text-[10px]"></i>
                        </button>
                        <button type="button" @click="scroll('right')" aria-label="Geser ke kanan" class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-indigo-300 text-slate-600 hover:text-indigo-600 flex items-center justify-center transition text-xs shadow-2xs active:scale-95">
                            <i class="fa fa-chevron-right text-[10px]"></i>
                        </button>
                    </div>
                    <a href="{{ route('articles.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 flex-shrink-0">
                        <span>Semua</span> <i class="fa fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Horizontal Slider -->
            <div x-ref="slider" class="flex items-stretch gap-3 sm:gap-4 overflow-x-auto no-scrollbar scroll-smooth pb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
                @forelse($featuredArticles as $article)
                    <div class="w-[260px] sm:w-[285px] lg:w-[296px] flex-shrink-0 bg-white border border-slate-200 rounded-2xl overflow-hidden hover:border-indigo-300 hover:shadow-md transition flex flex-col justify-between">
                        <div class="h-32 bg-slate-100 relative overflow-hidden flex-shrink-0">
                            <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                            <div class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[9px] font-bold bg-indigo-600 text-white backdrop-blur-xs shadow-sm capitalize">
                                {{ $article->category }}
                            </div>
                        </div>
                        <div class="p-3.5 flex-grow flex flex-col justify-between space-y-2.5">
                            <div class="space-y-1">
                                <p class="text-[10px] text-slate-400 flex items-center gap-1">
                                    <i class="fa fa-user text-[9px]"></i>
                                    <span class="truncate">{{ $article->author->name ?? 'Admin' }}</span>
                                    <span>•</span>
                                    <span>{{ $article->published_at ? $article->published_at->format('d M') : $article->created_at->format('d M') }}</span>
                                </p>
                                <h3 class="font-bold text-slate-800 text-xs line-clamp-2 leading-snug">
                                    <a href="{{ route('articles.show', $article->slug) }}" class="hover:text-indigo-600 transition">{{ $article->title }}</a>
                                </h3>
                            </div>
                            <a href="{{ route('articles.show', $article->slug) }}" class="text-[11px] font-bold text-indigo-600 hover:underline flex items-center gap-1 pt-1">
                                <span>Baca Selengkapnya</span> <i class="fa fa-arrow-right text-[9px]"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="w-full p-6 text-center text-slate-400 text-xs bg-white rounded-2xl border border-slate-200">
                        Belum ada artikel yang dipublikasikan saat ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ── 7. CAROUSEL: AKSI SOSIAL & DONASI AMAL ── -->
        <div 
            x-data="{
                scroll(dir) {
                    const el = $refs.slider;
                    const amount = el.clientWidth * 0.75;
                    el.scrollBy({ left: dir === 'left' ? -amount : amount, behavior: 'smooth' });
                }
            }" 
            class="space-y-3"
        >
            <div class="flex items-center justify-between border-b border-slate-200/70 pb-2.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm shadow-xs flex-shrink-0">
                        <i class="fa fa-hand-holding-heart"></i>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-black text-slate-900 leading-tight">Aksi Sosial & Donasi Amal</h2>
                        <p class="text-[11px] text-slate-500">Salurkan kepedulian 100% bebas biaya potongan platform.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-2.5">
                    <div class="hidden sm:flex items-center gap-1">
                        <button type="button" @click="scroll('left')" aria-label="Geser ke kiri" class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-emerald-300 text-slate-600 hover:text-emerald-600 flex items-center justify-center transition text-xs shadow-2xs active:scale-95">
                            <i class="fa fa-chevron-left text-[10px]"></i>
                        </button>
                        <button type="button" @click="scroll('right')" aria-label="Geser ke kanan" class="w-7 h-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-emerald-300 text-slate-600 hover:text-emerald-600 flex items-center justify-center transition text-xs shadow-2xs active:scale-95">
                            <i class="fa fa-chevron-right text-[10px]"></i>
                        </button>
                    </div>
                    <a href="{{ route('donations.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 flex-shrink-0">
                        <span>Semua</span> <i class="fa fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Horizontal Slider -->
            <div x-ref="slider" class="flex items-stretch gap-3 sm:gap-4 overflow-x-auto no-scrollbar scroll-smooth pb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
                @forelse($featuredDonations as $donation)
                    <div class="w-[250px] sm:w-[280px] lg:w-[296px] flex-shrink-0 p-3.5 bg-white border border-slate-200 rounded-2xl hover:border-emerald-300 hover:shadow-md transition flex flex-col justify-between space-y-2.5">
                        <div class="space-y-2">
                            <div class="h-24 rounded-xl overflow-hidden bg-slate-100 relative">
                                <img src="{{ $donation->banner_url }}" alt="{{ $donation->title }}" class="w-full h-full object-cover">
                                <div class="absolute bottom-1.5 left-1.5 px-2 py-0.5 rounded-md text-[8px] font-black bg-slate-900/80 text-white uppercase">
                                    0% Fee Platform
                                </div>
                            </div>
                            <div>
                                <span class="text-[9px] font-extrabold text-emerald-600 uppercase tracking-wider block truncate">{{ $donation->community->name ?? 'Komunitas' }}</span>
                                <h3 class="font-bold text-slate-800 text-xs line-clamp-1">
                                    <a href="{{ route('donations.show', $donation->slug) }}" class="hover:text-emerald-600 transition">{{ $donation->title }}</a>
                                </h3>
                            </div>
                            <!-- Progress Bar -->
                            <div class="space-y-1 pt-1">
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ min(100, $donation->progressPercentage()) }}%"></div>
                                </div>
                                <div class="flex items-center justify-between text-[10px] text-slate-500">
                                    <span>Terkumpul</span>
                                    <strong class="text-slate-800 font-bold">Rp {{ number_format($donation->collected_amount, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('donations.show', $donation->slug) }}" class="w-full text-center py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white text-xs font-bold transition active:scale-95">
                            Salurkan Donasi &rarr;
                        </a>
                    </div>
                @empty
                    <div class="w-full p-6 text-center text-slate-400 text-xs bg-white rounded-2xl border border-slate-200">
                        Belum ada program donasi aktif saat ini.
                    </div>
                @endforelse
            </div>
        </div>

    </section>

    <!-- 4. Compact Infinite Marquee (Moving PNG Logos) -->
    @php
    $marqueeLogos = [
        ['type' => 'community', 'file' => 'kompassumbar.jpg', 'name' => 'Kompas Sumbar'],
        ['type' => 'community', 'file' => 'padangtrailrunners.jpg', 'name' => 'Padang Trail Runners'],
        ['type' => 'community', 'file' => 'indonesianyouthdiplomacysumbar.jpg', 'name' => 'Indonesian Youth Diplomacy Sumbar'],
        ['type' => 'community', 'file' => 'komiteparalimpiadeindonesia.jpg', 'name' => 'Komite Paralimpiade Indonesia Sumbar'],
        ['type' => 'community', 'file' => 'sumaterawildadvanture.jpg', 'name' => 'Sumatera Wild Adventure'],
        ['type' => 'community', 'file' => 'genresumbar.jpg', 'name' => 'GenRe Sumatera Barat'],
        ['type' => 'community', 'file' => 'palitosumbar.png', 'name' => 'Palito Sumbar'],
        ['type' => 'community', 'file' => 'startupkito.jpg', 'name' => 'Startup Kito Minang'],
        ['type' => 'community', 'file' => 'sanaridiver.jpg', 'name' => 'Sanari Diver Sumbar'],
        ['type' => 'community', 'file' => 'govanaadvanture.jpg', 'name' => 'Govana Adventure'],
        ['type' => 'community', 'file' => 'esportsumbar.jpg', 'name' => 'Esports Sumbar'],
    ];
    @endphp

    <section class="border-y border-slate-200/80 bg-slate-50/70 py-4 overflow-hidden mt-8 relative">
        <!-- Soft Left Fade Mask -->
        <div class="pointer-events-none absolute inset-y-0 left-0 w-16 sm:w-28 bg-gradient-to-r from-slate-50 via-slate-50/80 to-transparent z-10"></div>
        
        <!-- Soft Right Fade Mask -->
        <div class="pointer-events-none absolute inset-y-0 right-0 w-16 sm:w-28 bg-gradient-to-l from-slate-50 via-slate-50/80 to-transparent z-10"></div>

        <div class="relative flex overflow-hidden">
            <div class="animate-marquee-logos flex items-center gap-5 sm:gap-7">
                @foreach(array_merge($marqueeLogos, $marqueeLogos) as $logo)
                    <div class="flex-shrink-0 flex items-center transition-transform hover:scale-105 select-none cursor-pointer" title="{{ $logo['name'] }}">
                        @if(isset($logo['type']) && $logo['type'] === 'brand')
                            <x-brand-logo :name="$logo['code']" class="h-6 sm:h-7 w-auto pointer-events-none" />
                        @else
                            <img
                                src="{{ asset('images/logos/' . $logo['file']) }}"
                                alt="{{ $logo['name'] }}"
                                class="h-7 sm:h-8 w-auto max-w-[120px] object-contain rounded-lg pointer-events-none"
                                loading="lazy"
                            >
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-app-layout>