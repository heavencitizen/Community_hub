<?php

use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\CommunityPostController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Digital Community Hub & Media Aggregator
|--------------------------------------------------------------------------
*/

// Route root page to welcome view & dashboard
Route::get('/', [DashboardController::class, 'welcome'])->name('home');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// ── 1. MODUL EVENT (Public Catalog & Detail) ───────────────────────────────
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');

// ── 2. MODUL ARTIKEL (Portal Berita & Konten Publik) ───────────────────────
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');

// ── 3. MODUL KOMUNITAS (Direktori & Halaman Detail Komunitas) ───────────────
Route::get('/communities', [CommunityController::class, 'index'])->name('communities.index');
Route::get('/communities/{slug}', [CommunityController::class, 'show'])->name('communities.show');

// ── 4. MODUL DONASI AMAL (Publik & Bebas Fee Potongan) ─────────────────────
Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
Route::get('/donations/{slug}', [DonationController::class, 'show'])->name('donations.show');
Route::post('/donations/{donation}/donate', [DonationController::class, 'donate'])->name('donations.donate');

// ── 5. MODUL LELANG & JUAL BELI (Katalog Publik & Dokumen Hasil Lelang) ─────
Route::get('/auctions', [AuctionController::class, 'index'])->name('auctions.index');
Route::get('/auctions/{auction}/certificate', [AuctionController::class, 'certificate'])->name('communities.auctions.certificate');
Route::get('/auctions/{auction}/ticker', [AuctionController::class, 'ticker'])->name('auctions.ticker');
Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');

// ── 5. MODUL PAYMENT GATEWAY (Universal Checkout & Success) ─────────────────
Route::get('/payment/checkout/{transactionCode}', [PaymentController::class, 'checkout'])->name('payment.checkout');
Route::post('/payment/{transactionCode}/token', [PaymentController::class, 'getToken'])->name('payment.token'); // Route baru untuk mengambil token dinamis spesifik per metode
Route::post('/payment/{transactionCode}/pending-notification', [PaymentController::class, 'pendingNotification'])->name('payment.pendingNotification');
Route::get('/payment/success/{transactionCode}', [PaymentController::class, 'success'])->name('payment.success');
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');

// ── 6. MODUL PROFIL PUBLIK PENGGUNA (/users/{username}) ───────────────────
Route::get('/users/{user:username}', [ProfileController::class, 'show'])->name('users.show');

// ── AUTHENTICATED USER ROUTES ──────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // ── Profil & E-Ticket Pengguna ──────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'myProfile'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/my-tickets', [ProfileController::class, 'tickets'])->name('profile.tickets');

    // ── Notifikasi Pengguna ────────────────────────────────────────────────
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');

    // ── Pembelian Tiket & Tampilan E-Ticket dengan QR Code ─────────────────
    Route::post('/events/{event}/buy-ticket', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

    // ── Interaksi Komunitas (Join, Leave, Forum Posting, Like & Comment) ──
    Route::post('/communities/{community}/join', [CommunityController::class, 'join'])->name('communities.join');
    Route::post('/communities/{community}/leave', [CommunityController::class, 'leave'])->name('communities.leave');
    Route::post('/communities/{community}/posts', [CommunityPostController::class, 'store'])->name('communities.posts.store');
    Route::post('/posts/quick-store', [CommunityPostController::class, 'quickStore'])->name('posts.quickStore');
    Route::post('/posts/{post}/like', [CommunityPostController::class, 'like'])->name('communities.posts.like');
    Route::post('/posts/{post}/comment', [CommunityPostController::class, 'comment'])->name('communities.posts.comment');
    Route::delete('/posts/{post}', [CommunityPostController::class, 'destroy'])->name('communities.posts.destroy');
    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])->name('users.follow');

    // ── Fitur Jual Beli Komunitas ──────────────────────────────────────────
    Route::post('/communities/{community}/products', [MarketplaceController::class, 'store'])->name('communities.products.store');
    Route::delete('/products/{product}', [MarketplaceController::class, 'destroy'])->name('communities.products.destroy');
    Route::post('/products/{product}/buy', [MarketplaceController::class, 'buy'])->name('communities.products.buy');

    // ── Fitur Lelang Komunitas ─────────────────────────────────────────────
    Route::post('/communities/{community}/auctions', [AuctionController::class, 'store'])->name('communities.auctions.store');
    Route::post('/auctions/{auction}/bid', [AuctionController::class, 'bid'])->name('communities.auctions.bid');
    Route::post('/auctions/{auction}/checkout', [AuctionController::class, 'checkoutWinner'])->name('communities.auctions.checkout');
    Route::post('/auctions/{auction}/close', [AuctionController::class, 'close'])->name('communities.auctions.close');
    Route::post('/auctions/{auction}/cancel', [AuctionController::class, 'cancel'])->name('communities.auctions.cancel');
    Route::post('/auctions/{auction}/wanprestasi', [AuctionController::class, 'declareWanprestasi'])->name('communities.auctions.wanprestasi');

    // ── Buat & Edit Komunitas serta Aturan ──────────────────────────────────
    Route::get('/community/create', [CommunityController::class, 'create'])->name('communities.create');
    Route::post('/community', [CommunityController::class, 'store'])->name('communities.store');
    Route::put('/communities/{community}', [CommunityController::class, 'update'])->name('communities.update');
    Route::put('/communities/{community}/rules', [CommunityController::class, 'updateRules'])->name('communities.rules.update');
    Route::patch('/communities/{community}/members/{member}', [CommunityController::class, 'updateMemberStatus'])->name('communities.members.updateStatus');

    // ── Donasi Amal Management (Admin / Ketua Komunitas) ───────────────────
    Route::get('/donations-manage/create', [DonationController::class, 'create'])->name('donations.create');
    Route::post('/donations-manage', [DonationController::class, 'store'])->name('donations.store');

    // ── Pengaturan Akun & Sistem ───────────────────────────────────────────
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.password');

    // ── ADMIN KOMUNITAS / EO & SUPER ADMIN ─────────────────────────────────
    Route::middleware('role:community_admin,super_admin')->group(function () {
        // Manajemen Event
        Route::get('/events/manage/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events/manage', [EventController::class, 'store'])->name('events.store');

        // Buat Draf Artikel
        Route::get('/articles/manage/create', [ArticleController::class, 'create'])->name('articles.create');
        Route::post('/articles/manage', [ArticleController::class, 'store'])->name('articles.store');

        // Verifikasi Tiket Pembayaran & Pindai QR Masuk
        Route::get('/admin/tickets', [VerificationController::class, 'index'])->name('admin.tickets.index');
        Route::post('/admin/tickets/{ticket}/approve', [VerificationController::class, 'approve'])->name('admin.tickets.approve');
        Route::post('/admin/tickets/{ticket}/reject', [VerificationController::class, 'reject'])->name('admin.tickets.reject');
        Route::post('/admin/tickets/{ticket}/scan', [TicketController::class, 'scan'])->name('admin.tickets.scan');
    });

    // ── SUPER ADMIN KHUSUS (Moderasi Artikel, Feed & Pengaturan Komisi) ─────
    Route::middleware('role:super_admin')->group(function () {
        // Moderasi Artikel
        Route::get('/admin/moderation/articles', [ModerationController::class, 'articles'])->name('admin.moderation.articles');
        Route::post('/admin/moderation/articles/{article}/approve', [ModerationController::class, 'approveArticle'])->name('admin.moderation.articles.approve');
        Route::post('/admin/moderation/articles/{article}/reject', [ModerationController::class, 'rejectArticle'])->name('admin.moderation.articles.reject');

        // Moderasi Feed Komunitas
        Route::get('/admin/moderation/posts', [ModerationController::class, 'posts'])->name('admin.moderation.posts');
    });
});

// ── PWA ROUTES (Manifest & Service Worker) ──────────────────────────────────
Route::get('/manifest.webmanifest', function () {
    return response(file_get_contents(public_path('manifest.webmanifest')), 200, [
        'Content-Type' => 'application/manifest+json; charset=utf-8',
    ]);
})->name('pwa.manifest');

Route::get('/sw.js', function () {
    return response(file_get_contents(public_path('sw.js')), 200, [
        'Content-Type' => 'application/javascript; charset=utf-8',
        'Service-Worker-Allowed' => '/',
    ]);
})->name('pwa.serviceWorker');

require __DIR__.'/auth.php';