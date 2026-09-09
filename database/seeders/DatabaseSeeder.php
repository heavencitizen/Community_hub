<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\AuctionBid;
use App\Models\Community;
use App\Models\CommunityAuction;
use App\Models\CommunityMember;
use App\Models\CommunityPost;
use App\Models\CommunityPostComment;
use App\Models\CommunityPostLike;
use App\Models\CommunityProduct;
use App\Models\Donation;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. USERS MULTI-ROLE DENGAN AVATAR ──────────────────────────────
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'username' => 'superadmin',
            'email' => 'superadmin@hubmedia.id',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'bio' => 'Administrator utama CommunityHub. Menjaga ketertiban ekosistem forum, verifikasi event, dan transparansi donasi.',
            'email_verified_at' => now(),
        ]);

        $communityAdmin = User::create([
            'name' => 'Rian Maulana (EO & Captain)',
            'username' => 'rianmaulana',
            'email' => 'eo@community.id',
            'password' => Hash::make('password'),
            'role' => 'community_admin',
            'bio' => 'Event Organizer & Captain komunitas lari Padang Running Club & Vespa vintage. Hobi marathon pantai dan eksplorasi alam Minang.',
            'email_verified_at' => now(),
        ]);

        $generalUser = User::create([
            'name' => 'Siti Nurhaliza (Member)',
            'username' => 'sitinurhaliza',
            'email' => 'user@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'bio' => 'Penggemar manual brew specialty coffee Solok Radjo, penikmat lari santai sore di Pantai Padang, dan relawan konservasi laut.',
            'email_verified_at' => now(),
        ]);

        $member2 = User::create([
            'name' => 'Dimas Pratama',
            'username' => 'dimaspratama',
            'email' => 'dimas@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'bio' => 'Streetballer Padang, pecinta otomotif retro Vespa 2-tak, dan penjelajah spot hidden gem Sumatera Barat.',
            'email_verified_at' => now(),
        ]);

        $member3 = User::create([
            'name' => 'Aisyah Putri',
            'username' => 'aisyahputri',
            'email' => 'aisyah@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'bio' => 'Home baker & pecinta kuliner artisan. Suka berbagi resep pastry dan hunting coffee shop lokal.',
            'email_verified_at' => now(),
        ]);

        // ── 2. KOMUNITAS HOBI LOKAL LENGKAP KATEGORI & RULES ─────────────────
        $rulesDefault = "1. Wajib menjaga etika dan saling menghargai sesama anggota.\n2. Dilarang memposting konten berbau SARA, pornografi, hoax, atau ujaran kebencian.\n3. Transaksi jual beli dan lelang wajib transparan, barang sesuai deskripsi, dan anti-penipuan.\n4. Pelanggaran aturan akan dikenakan sanksi penonaktifan akun keanggotaan oleh Ketua Komunitas.";

        $commRunners = Community::create([
            'user_id' => $communityAdmin->id,
            'name' => 'Sumatra Runners / Padang Running Club',
            'slug' => 'sumatra-runners-padang-running-club',
            'category' => 'olahraga',
            'description' => 'Komunitas lari santai, maraton, dan hidup bugar di pesisir Sumatera Barat. Rutin lari pagi & sore di Pantai Padang, GOR Agus Salim, dan Jembatan Siti Nurbaya. 🏃‍♂️💨',
            'rules' => $rulesDefault,
            'status' => 'active',
        ]);

        $commVespa = Community::create([
            'user_id' => $communityAdmin->id,
            'name' => 'Padang Night Ride & Classic Vespa',
            'slug' => 'padang-night-ride-classic-vespa',
            'category' => 'otomotif',
            'description' => 'Ruang kumpul pecinta motor klasik Vespa 2-tak, Vespa matic, dan motor vintage. Suka sunmori ke Sitinjau Lauik, ngopi santai di Teluk Bayur, dan berbagi tips restorasi mesin! 🛵✨',
            'rules' => $rulesDefault,
            'status' => 'active',
        ]);

        $commScuba = Community::create([
            'user_id' => $superAdmin->id,
            'name' => 'West Sumatra Scuba & Diving Club',
            'slug' => 'west-sumatra-scuba-diving-club',
            'category' => 'kesehatan',
            'description' => 'Pencinta laut biru, selam scuba, freediving, dan aksi konservasi terumbu karang di Kepulauan Mandeh, Pasumpahan, dan Mentawai. 🤿🐠🌊',
            'rules' => $rulesDefault,
            'status' => 'active',
        ]);

        $commCoffee = Community::create([
            'user_id' => $communityAdmin->id,
            'name' => 'Padang Bakers & Coffee Enthusiast',
            'slug' => 'padang-bakers-coffee-enthusiast',
            'category' => 'kuliner',
            'description' => 'Komunitas home-baker, pecinta manual brew kopi lokal (Solok Radjo, Kayu Aro), pastry artisan, dan barista hunting hidden gem coffee shop di Padang. ☕🥐🍰',
            'rules' => $rulesDefault,
            'status' => 'active',
        ]);

        $commBasket = Community::create([
            'user_id' => $member2->id,
            'name' => 'Padang Streetball Basket',
            'slug' => 'padang-streetball-basket',
            'category' => 'olahraga',
            'description' => 'Wadah pebasket jalanan, komunitas 3x3, sparing basket mingguan, dan adu skill slam dunk santai di lapangan terbuka se-Sumatera Barat. 🏀🔥',
            'rules' => $rulesDefault,
            'status' => 'active',
        ]);

        // ── 3. MEMBERSHIP LENGKAP STATUS & TANGGAL BERGABUNG ────────────────
        $allCommunities = [$commRunners, $commVespa, $commScuba, $commCoffee, $commBasket];
        $allUsers = [$superAdmin, $communityAdmin, $generalUser, $member2, $member3];

        foreach ($allCommunities as $comm) {
            foreach ($allUsers as $index => $u) {
                $isOwner = ($comm->user_id === $u->id);
                CommunityMember::create([
                    'community_id' => $comm->id,
                    'user_id' => $u->id,
                    'role' => $isOwner ? 'captain' : ($index === 1 ? 'moderator' : 'member'),
                    'status' => 'active',
                    'joined_at' => now()->subDays(rand(10, 180)),
                ]);
            }
        }

        // ── 4. JUAL BELI PRODUK KOMUNITAS (MARKETPLACE) ─────────────────────
        CommunityProduct::create([
            'community_id' => $commRunners->id,
            'user_id' => $communityAdmin->id,
            'name' => 'Jersey Lari Resmi Sumatra Runners 2026 (QuickDry)',
            'slug' => 'jersey-lari-resmi-sumatra-runners-2026',
            'description' => 'Jersey dry-fit polyester anti bakteri dan sejuk dipakai saat lari siang/pagi. Tersedia ukuran S, M, L, XL.',
            'price' => 135000,
            'stock' => 25,
            'status' => 'available',
        ]);

        CommunityProduct::create([
            'community_id' => $commRunners->id,
            'user_id' => $communityAdmin->id,
            'name' => 'Running Belt & Soft Flask 500ml Ergonomis',
            'slug' => 'running-belt-soft-flask-500ml',
            'description' => 'Tas pinggang lari elastis muat HP 6.7 inch dan botol minum lembut anti goyang saat sprint.',
            'price' => 85000,
            'stock' => 15,
            'status' => 'available',
        ]);

        CommunityProduct::create([
            'community_id' => $commVespa->id,
            'user_id' => $communityAdmin->id,
            'name' => 'Spion Baret Bulat Retro Vespa PX / Super / Sprint (Chrome)',
            'slug' => 'spion-baret-bulat-retro-vespa-chrome',
            'description' => 'Sepasang spion batang chrome tebal anti karat, kaca cembung sudut pandang lebar cocok untuk sunmori.',
            'price' => 120000,
            'stock' => 8,
            'status' => 'available',
        ]);

        CommunityProduct::create([
            'community_id' => $commCoffee->id,
            'user_id' => $communityAdmin->id,
            'name' => 'Specialty Coffee Beans Solok Radjo Natural 200g',
            'slug' => 'specialty-coffee-beans-solok-radjo-natural',
            'description' => 'Biji kopi arabika pilihan dari lereng Gunung Talang. Tasting notes: Plum, honey sweetness, and dragon fruit.',
            'price' => 95000,
            'stock' => 30,
            'status' => 'available',
        ]);

        // ── 5. LELANG KOMUNITAS DENGAN RIWAYAT BIDDING ─────────────────────
        $auction1 = CommunityAuction::create([
            'community_id' => $commVespa->id,
            'user_id' => $communityAdmin->id,
            'title' => 'Helm Bogo Vintage Classic Motif Minang Edition (NOS)',
            'slug' => 'helm-bogo-vintage-classic-motif-minang',
            'description' => 'Helm retro limited edition koleksi komunitas Vespa Padang dengan kaca bogo cembung original dan lis kulit asli.',
            'starting_price' => 200000,
            'bid_increment' => 25000,
            'current_price' => 275000,
            'start_time' => now()->subDays(2),
            'end_time' => now()->addDays(3),
            'status' => 'active',
            'winner_id' => $generalUser->id,
        ]);

        AuctionBid::create([
            'auction_id' => $auction1->id,
            'user_id' => $member2->id,
            'bid_amount' => 225000,
            'created_at' => now()->subHours(20),
        ]);
        AuctionBid::create([
            'auction_id' => $auction1->id,
            'user_id' => $member3->id,
            'bid_amount' => 250000,
            'created_at' => now()->subHours(10),
        ]);
        AuctionBid::create([
            'auction_id' => $auction1->id,
            'user_id' => $generalUser->id,
            'bid_amount' => 275000,
            'created_at' => now()->subHours(2),
        ]);

        $auction2 = CommunityAuction::create([
            'community_id' => $commScuba->id,
            'user_id' => $superAdmin->id,
            'title' => 'Diving Mask Cressi F1 Frameless Limited Matte Blue',
            'slug' => 'diving-mask-cressi-f1-frameless-matte-blue',
            'description' => 'Kacamata selam frameless dengan silikon ultra lembut dan tempered glass jernih, hasil sumbangan konservasi laut Mandeh.',
            'starting_price' => 450000,
            'bid_increment' => 50000,
            'current_price' => 550000,
            'start_time' => now()->subDays(1),
            'end_time' => now()->addDays(5),
            'status' => 'active',
            'winner_id' => $member2->id,
        ]);

        AuctionBid::create([
            'auction_id' => $auction2->id,
            'user_id' => $generalUser->id,
            'bid_amount' => 500000,
            'created_at' => now()->subHours(8),
        ]);
        AuctionBid::create([
            'auction_id' => $auction2->id,
            'user_id' => $member2->id,
            'bid_amount' => 550000,
            'created_at' => now()->subHours(1),
        ]);

        // ── 6. PROGRAM DONASI AMAL (0% FEE POTONGAN) ────────────────────────
        $donation1 = Donation::create([
            'community_id' => $commScuba->id,
            'user_id' => $superAdmin->id,
            'title' => 'Gerakan Adopsi & Restorasi Terumbu Karang Mandeh 2026',
            'slug' => 'gerakan-adopsi-restorasi-terumbu-karang-mandeh',
            'description' => "Program penanaman 2.000 bibit fragmen karang meja metode spider web di kawasan perairan Teluk Mandeh dan Pasumpahan.\n\nSetiap donasi Rp50.000 setara dengan 1 struktur bibit karang yang dirawat dan dimonitor oleh penyelam bersertifikasi.",
            'target_amount' => 25000000,
            'collected_amount' => 8750000,
            'status' => 'active',
        ]);

        $donation2 = Donation::create([
            'community_id' => $commRunners->id,
            'user_id' => $communityAdmin->id,
            'title' => 'Bantuan Perlengkapan Olahraga & Sepatu Anak Pesisir Pantai Padang',
            'slug' => 'bantuan-sepatu-olahraga-anak-pesisir-padang',
            'description' => 'Penggalangan dana untuk membelikan 100 pasang sepatu lari dan bola basket bagi anak-anak di perkampungan nelayan Muaro Padang.',
            'target_amount' => 15000000,
            'collected_amount' => 6200000,
            'status' => 'active',
        ]);

        // ── 7. TRANSAKSI DUMMY LENGKAP METODE PAYMENT ────────────────────────
        Transaction::create([
            'transaction_code' => 'DON-RESTO-001',
            'user_id' => $generalUser->id,
            'type' => 'donation',
            'reference_id' => $donation1->id,
            'amount' => 250000,
            'platform_fee_percent' => 0.0,
            'platform_fee_amount' => 0.0,
            'total_amount' => 250000,
            'payment_method' => 'qris',
            'payment_status' => 'completed',
            'guest_name' => 'Siti Nurhaliza',
            'guest_email' => 'user@gmail.com',
            'notes' => 'Donasi untuk Restorasi Terumbu Karang',
        ]);

        Transaction::create([
            'transaction_code' => 'DON-ANON-002',
            'user_id' => null,
            'type' => 'donation',
            'reference_id' => $donation1->id,
            'amount' => 500000,
            'platform_fee_percent' => 0.0,
            'platform_fee_amount' => 0.0,
            'total_amount' => 500000,
            'payment_method' => 'bca_va',
            'payment_status' => 'completed',
            'guest_name' => 'Hamba Allah (Masyarakat Minang)',
            'guest_email' => 'donatur@gmail.com',
            'notes' => 'Semoga laut kita lestari selalu',
        ]);

        // ── 8. EVENT DENGAN TIKET & KALKULASI FEE ────────────────────────────
        $event1 = Event::create([
            'community_id' => $commRunners->id,
            'title' => 'Sunday Morning Fun Run 5K: Pesisir Pantai & Siti Nurbaya',
            'slug' => 'sunday-morning-fun-run-5k-pantai-padang',
            'description' => "Lari santai pagi bersama Sumatra Runners menikmati angin laut Pantai Padang dan Jembatan Siti Nurbaya.\n\nFasilitas Tiket:\n- BIB Number & Refreshment Water Station\n- Medali Finisher Lucu & Stiker Komunitas\n- Sarapan bareng di finish line!",
            'location' => 'Titik Kumpul: Tugu Merpati Perdamaian, Pantai Muaro Padang',
            'event_date' => now()->addDays(6)->setHour(6)->setMinute(0),
            'price' => 35000,
            'admin_fee' => 1750, // 5% fee umum
            'quota' => 150,
        ]);

        $event2 = Event::create([
            'community_id' => $commScuba->id,
            'title' => 'Mini Concert & Acoustic Jamming: Galang Dana Bencana Pesisir',
            'slug' => 'mini-concert-acoustic-jamming-galang-dana-bencana',
            'description' => 'Konser amal akustik intim diselenggarakan bersama Dinas Pariwisata dan musisi lokal. 100% donasi disalurkan untuk posko bencana.',
            'location' => 'Amphitheater Taman Budaya Sumatera Barat, Padang',
            'event_date' => now()->addDays(10)->setHour(19)->setMinute(0),
            'price' => 25000,
            'admin_fee' => 1250,
            'quota' => 250,
        ]);

        $event3 = Event::create([
            'community_id' => $commVespa->id,
            'title' => 'Sunmori & Gathering Vespa Classic: Jalur Panorama Sitinjau Lauik',
            'slug' => 'sunmori-gathering-vespa-classic-sitinjau-lauik',
            'description' => 'Rolling thunder santai keliling kota Padang dan sarapan bareng di Rest Area Panorama. Terbuka untuk semua jenis Vespa. Gratis!',
            'location' => 'Meeting Point: Lapangan Imam Bonjol, Padang',
            'event_date' => now()->addDays(3)->setHour(7)->setMinute(0),
            'price' => 0,
            'admin_fee' => 0,
            'quota' => 100,
        ]);

        $event4 = Event::create([
            'community_id' => $commCoffee->id,
            'title' => 'Workshop Manual Brew V60 & Cupping Biji Kopi Solok Radjo',
            'slug' => 'workshop-manual-brew-v60-cupping-solok-radjo',
            'description' => "Sesi workshop edukasi teknik pour over V60, kalibrasi rasio ekstraksi, dan cupping session 4 varietas kopi arabika unggulan Minang bersama master barista bersertifikasi Q-Grader.\n\nFasilitas Peserta:\n- Biji Kopi Solok Radjo Natural 100g dibawa pulang\n- Sertifikat Workshop & Handout Panduan Rasio Seduh\n- Tasting & Cupping Session 4 Beans Specialty\n- Snack Artisan Pastry Pairing",
            'location' => 'Kopi Rumah Tua Roastery, Jl. Batang Arau No. 42, Muaro Padang',
            'event_date' => now()->addDays(8)->setHour(14)->setMinute(0),
            'price' => 65000,
            'admin_fee' => 3250,
            'quota' => 40,
        ]);

        $event5 = Event::create([
            'community_id' => $commBasket->id,
            'title' => 'Padang 3x3 Streetball Championship & Dunk Contest 2026',
            'slug' => 'padang-3x3-streetball-championship-dunk-contest-2026',
            'description' => "Turnamen bola basket 3-on-3 terbuka se-Sumatera Barat, diramaikan kompetisi slam dunk, 3-point shootout contest, dan penampilan beatbox hip-hop lokal. Mari unjuk skill dan junjung tinggi sportivitas jalanan!\n\nBenefit Peserta & Penonton:\n- Trophy & Total Uang Pembinaan Rp15.000.000\n- Official Match Jersey untuk setiap tim terdaftar\n- Tiket akses all-day area festival & games booth",
            'location' => 'Lapangan Basket Outdoor GOR H. Agus Salim, Padang',
            'event_date' => now()->addDays(14)->setHour(15)->setMinute(30),
            'price' => 20000,
            'admin_fee' => 1000,
            'quota' => 300,
        ]);

        $event6 = Event::create([
            'community_id' => $commScuba->id,
            'title' => 'Underwater Coral Cleanup & Dive Clinic: Pulau Pasumpahan',
            'slug' => 'underwater-coral-cleanup-dive-clinic-pulau-pasumpahan',
            'description' => "Aksi konservasi penyelaman bersih-bersih sampah dasar laut dan monitoring bibit terumbu karang di spot Pasumpahan Island. Terbuka bagi penyelam berlisensi (Open Water / Advanced) maupun relawan snorkel bibir pantai.\n\nFasilitas Kegiatan:\n- Penyeberangan kapal boat PP Teluk Bungus - Pasumpahan\n- Tabung selam & pemberat (khusus scuba diver)\n- Makan siang prasmanan ikan bakar & kelapa muda segar\n- Jaring pengumpul sampah & briefing konservasi laut",
            'location' => 'Dermaga Wisata Bahari Teluk Bungus, Padang',
            'event_date' => now()->addDays(12)->setHour(7)->setMinute(30),
            'price' => 150000,
            'admin_fee' => 7500,
            'quota' => 50,
        ]);

        $ticket1 = Ticket::create([
            'user_id' => $generalUser->id,
            'event_id' => $event1->id,
            'ticket_code' => 'RUN-PADANG-5K01',
            'total_price' => 35700,
            'status' => 'approved',
            'is_scanned' => false,
        ]);

        $ticket2 = Ticket::create([
            'user_id' => $member3->id,
            'event_id' => $event4->id,
            'ticket_code' => 'BREW-SOLOK-001',
            'total_price' => 66300,
            'status' => 'approved',
            'is_scanned' => false,
        ]);

        $ticket3 = Ticket::create([
            'user_id' => $member2->id,
            'event_id' => $event5->id,
            'ticket_code' => '3X3-PADANG-001',
            'total_price' => 20400,
            'status' => 'approved',
            'is_scanned' => false,
        ]);

        // ── 9. FEED FORUM POSTS DENGAN LIKES & KOMENTAR ─────────────────────
        $post1 = CommunityPost::create([
            'community_id' => $commRunners->id,
            'user_id' => $communityAdmin->id,
            'content' => 'Selamat pagi runners! 🌅 Tadi pagi selesai easy run 7K keliling Pantai Padang. Udaranya segar banget dan pemandangan sunrise-nya juara. Jangan lupa besok Minggu kita ada latihan interval di GOR Agus Salim jam 06.00 WIB ya! Siapa yang mau ikut absen di bawah 👇🔥',
            'type' => 'text',
            'likes_count' => 18,
            'created_at' => now()->subHours(2),
        ]);

        $post2 = CommunityPost::create([
            'community_id' => $commVespa->id,
            'user_id' => $member2->id,
            'content' => 'Habis beres restorasi cat dan ganti spuyer Vespa PX 150 tahun 1982. Suara knalpot garingnya bikin nagih buat muter-muter kota nanti malam! Ada yang nongkrong di Muaro malam ini? Gas kita ngopi santai 🛵💨☕',
            'type' => 'text',
            'likes_count' => 24,
            'created_at' => now()->subHours(4),
        ]);

        $post3 = CommunityPost::create([
            'community_id' => $commScuba->id,
            'user_id' => $superAdmin->id,
            'content' => 'Update dari ekspedisi konservasi terumbu karang di Teluk Mandeh kemarin! Visibility air jernih banget sampai 15 meter. Banyak clownfish dan penyu yang mampir di area transplantasi karang baru 🐠🐢💙 Tetap jaga kelestarian laut Minang ya kawan-kawan!',
            'type' => 'text',
            'likes_count' => 31,
            'created_at' => now()->subHours(6),
        ]);

        $post4 = CommunityPost::create([
            'community_id' => $commCoffee->id,
            'user_id' => $generalUser->id,
            'content' => 'Pagi ini seduh biji kopi Arabika Solok Radjo proses Anaerobic Natural dengan V60. Notes rasa nangka dan fruity-nya keluar manis banget! Rekomendasi beans lokal Sumatera Barat yang wajib dicoba pecinta filter coffee ☕👌',
            'type' => 'text',
            'likes_count' => 15,
            'created_at' => now()->subHours(8),
        ]);

        CommunityPostComment::create([
            'community_post_id' => $post1->id,
            'user_id' => $generalUser->id,
            'comment' => 'Hadir capt! Besok bawa bekal pisang dan air mineral yaa 🙌',
            'created_at' => now()->subHours(1),
        ]);

        CommunityPostComment::create([
            'community_post_id' => $post3->id,
            'user_id' => $communityAdmin->id,
            'comment' => 'Keren banget! Next trip diving saya mau gabung dong dok 🤿🌊',
            'created_at' => now()->subHours(3),
        ]);

        CommunityPostLike::create(['community_post_id' => $post1->id, 'user_id' => $generalUser->id]);
        CommunityPostLike::create(['community_post_id' => $post1->id, 'user_id' => $member2->id]);
        CommunityPostLike::create(['community_post_id' => $post2->id, 'user_id' => $communityAdmin->id]);
        CommunityPostLike::create(['community_post_id' => $post3->id, 'user_id' => $generalUser->id]);
        CommunityPostLike::create(['community_post_id' => $post3->id, 'user_id' => $member2->id]);
        CommunityPostLike::create(['community_post_id' => $post4->id, 'user_id' => $superAdmin->id]);

        // ── 10. ARTIKEL PUBLIK ──────────────────────────────────────────────
        Article::create([
            'user_id' => $communityAdmin->id,
            'community_id' => $commRunners->id,
            'title' => '5 Rute Lari Pagi Paling Syahdu di Kota Padang dengan View Sunset & Pantai',
            'slug' => '5-rute-lari-pagi-syahdu-di-kota-padang',
            'content' => "Bagi pegiat lari di Kota Padang, memilih rute lari yang nyaman dan berpemandangan indah adalah kunci menjaga konsistensi latihan.\n\n1. Jalur Pedestrian Pantai Padang (Taplau)\n2. Loop Jembatan Siti Nurbaya & Kawasan Kota Tua\n3. Kompleks GOR H. Agus Salim\n4. Rute Bukit Lampu & Teluk Bayur\n5. Danau Cimpago",
            'category' => 'lifestyle',
            'status' => 'published',
            'published_at' => now()->subDays(1),
        ]);

        Article::create([
            'user_id' => $superAdmin->id,
            'community_id' => $commScuba->id,
            'title' => 'Konservasi Mandeh: Mengembalikan Kejayaan Terumbu Karang Nusantara',
            'slug' => 'konservasi-mandeh-terumbu-karang-nusantara',
            'content' => 'Kawasan Wisata Bahari Terpadu (KWBT) Mandeh di Pesisir Selatan dikenal sebagai Raja Ampat-nya Sumatera Barat. Mari dukung gerakan laut bersih dan bebas sampah plastik!',
            'category' => 'donation',
            'status' => 'published',
            'published_at' => now()->subDays(2),
        ]);

        // ── 11. NOTIFIKASI AKTIVITAS (IN-APP NOTIFICATIONS) ─────────────────
        UserNotification::send(
            $communityAdmin->id,
            $generalUser->id,
            'post_liked',
            'Postingan Anda Disukai',
            'Budi Pratama menyukai postingan Anda: "Pemberitahuan Run Bareng Pagi"',
            route('communities.show', $commRunners->slug).'#post-'.$post1->id,
            'fa-heart',
            'text-rose-500'
        );

        UserNotification::send(
            $communityAdmin->id,
            $generalUser->id,
            'post_commented',
            'Komentar Baru pada Postingan',
            'Budi Pratama berkomentar: "Hadir capt! Besok bawa bekal pisang..."',
            route('communities.show', $commRunners->slug).'#post-'.$post1->id,
            'fa-comment',
            'text-indigo-500'
        );

        UserNotification::send(
            $communityAdmin->id,
            $member2->id,
            'member_joined',
            'Anggota Baru Bergabung',
            'dr. Farhan Malik telah resmi bergabung dengan komunitas '.$commRunners->name,
            route('communities.show', $commRunners->slug).'?tab=members',
            'fa-user-plus',
            'text-emerald-500'
        );

        UserNotification::send(
            $generalUser->id,
            null,
            'ticket_approved',
            'E-Ticket QR Code Terbit!',
            'Pembayaran tiket event "Padang Minang Sunset 10K Run & Festival 2026" berhasil. E-Ticket QR Code Anda telah aktif.',
            route('tickets.show', $ticket1->id),
            'fa-qrcode',
            'text-indigo-500'
        );

        UserNotification::send(
            $generalUser->id,
            $member3->id,
            'post_liked',
            'Postingan Anda Disukai',
            'Siti Rahma menyukai postingan Anda di Padang Vintage Vespa Club',
            route('communities.show', $commVespa->slug).'#post-'.$post2->id,
            'fa-heart',
            'text-rose-500'
        );
    }
}
