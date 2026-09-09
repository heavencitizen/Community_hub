<?php

namespace App\Http\Controllers;

use App\Models\AuctionBid;
use App\Models\Community;
use App\Models\CommunityAuction;
use App\Models\Transaction;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AuctionController extends Controller
{
    /**
     * Katalog Publik Arena Lelang Komunitas
     */
    public function index(Request $request)
    {
        // Auto-finalize lelang yang sudah lewat waktu
        CommunityAuction::where('status', 'active')
            ->where('end_time', '<=', now())
            ->each(function (CommunityAuction $auction) {
                $auction->autoFinalizeIfNeeded();
            });

        $query = CommunityAuction::with(['community', 'creator', 'winner', 'bids.user']);

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        // Filter Komunitas
        if ($request->filled('community_id')) {
            $query->where('community_id', $request->community_id);
        }

        // Filter Status
        $statusFilter = $request->get('status', 'all');
        if ($statusFilter === 'live') {
            $query->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('start_time')->orWhere('start_time', '<=', now());
                })
                ->where('end_time', '>', now());
        } elseif ($statusFilter === 'scheduled') {
            $query->where('status', 'active')->where('start_time', '>', now());
        } elseif ($statusFilter === 'closed') {
            $query->where('status', 'closed');
        } elseif ($statusFilter === 'completed') {
            $query->where('status', 'completed');
        }

        // Urutan: Yang live berakhir paling cepat ditaruh paling atas, diikuti terjadwal
        $nowStr = now()->toDateTimeString();
        $auctions = $query->orderByRaw("
            CASE 
                WHEN status = 'active' AND (start_time IS NULL OR start_time <= '{$nowStr}') AND end_time > '{$nowStr}' THEN 1 
                WHEN status = 'active' AND start_time > '{$nowStr}' THEN 2 
                WHEN status = 'closed' THEN 3 
                WHEN status = 'completed' THEN 4 
                ELSE 5 
            END ASC
        ")->orderBy('end_time', 'asc')->paginate(9)->withQueryString();

        $communities = Community::where('status', 'active')->get();

        // Hitung statistik ringkas untuk header
        $stats = [
            'total_live' => CommunityAuction::where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('start_time')->orWhere('start_time', '<=', now());
                })
                ->where('end_time', '>', now())
                ->count(),
            'total_scheduled' => CommunityAuction::where('status', 'active')->where('start_time', '>', now())->count(),
            'total_completed' => CommunityAuction::where('status', 'completed')->count(),
            'total_bids' => AuctionBid::count(),
        ];

        return view('auctions.index', compact('auctions', 'communities', 'stats', 'statusFilter'));
    }

    /**
     * Buat lelang baru (Hanya Ketua Komunitas / Super Admin)
     */
    public function store(Request $request, Community $community)
    {
        if ($community->user_id !== auth()->id() && ! auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Hanya ketua komunitas yang berhak membuat program lelang.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'starting_price' => 'required|numeric|min:10000',
            'bid_increment' => 'required|numeric|min:5000',
            'start_time' => 'nullable|date',
            'end_time' => 'required|date',
            'description' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:1000',
            'anti_sniping' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $startTime = $request->filled('start_time') ? Carbon::parse($request->start_time) : now();

        if (Carbon::parse($request->end_time)->lte($startTime)) {
            return back()->withErrors(['end_time' => 'Waktu selesai lelang harus setelah waktu mulai lelang.'])->withInput();
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('auctions', 'public');
        }

        $slug = Str::slug($request->title).'-'.Str::random(5);

        $community->auctions()->create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'notes' => $request->notes,
            'image' => $imagePath,
            'starting_price' => $request->starting_price,
            'bid_increment' => $request->bid_increment,
            'current_price' => $request->starting_price,
            'start_time' => $startTime,
            'end_time' => $request->end_time,
            'anti_sniping' => $request->boolean('anti_sniping', true),
            'bidding_type' => 'open',
            'status' => 'active',
        ]);

        return back()->with('success', 'Program lelang komunitas berhasil dibuka!');
    }

    /**
     * Ajukan penawaran lelang (Place Bid)
     */
    public function bid(Request $request, CommunityAuction $auction)
    {
        return DB::transaction(function () use ($request, $auction) {
            // Lock record lelang untuk mencegah race condition (pessimistic lock)
            $lockedAuction = CommunityAuction::where('id', $auction->id)->lockForUpdate()->firstOrFail();

            // Auto finalize jika waktu berakhir sudah terlewati
            $lockedAuction->autoFinalizeIfNeeded();

            if ($lockedAuction->isEnded() || $lockedAuction->status !== 'active') {
                return back()->with('error', 'Lelang ini telah berakhir atau ditutup.');
            }

            // Cek jika lelang masih berstatus terjadwal (belum dimulai)
            if ($lockedAuction->isScheduled()) {
                return back()->with('error', 'Lelang ini belum dimulai. Sesi open bidding baru akan dibuka pada '.$lockedAuction->start_time->format('d M Y, H:i').' WIB.');
            }

            // Mencegah pemilik menawar barang sendiri
            if ($lockedAuction->user_id === auth()->id()) {
                return back()->with('error', 'Anda adalah penyelenggara lelang ini, tidak dapat menawar barang sendiri.');
            }

            // Mencegah menawar jika sudah menjadi penawar tertinggi
            if ($lockedAuction->winner_id === auth()->id()) {
                return back()->with('error', 'Anda sudah memegang tawaran tertinggi saat ini.');
            }

            $minBid = (float) $lockedAuction->current_price + (float) $lockedAuction->bid_increment;

            $request->validate([
                'bid_amount' => 'required|numeric|min:'.$minBid,
            ], [
                'bid_amount.min' => 'Nilai tawaran minimal adalah Rp'.number_format($minBid, 0, ',', '.').' (Tawaran tertinggi + kelipatan bid).',
            ]);

            $previousWinnerId = $lockedAuction->winner_id;
            $previousPrice = (float) $lockedAuction->current_price;

            // Rekam penawar sebelumnya sebagai pemenang cadangan (runner-up)
            if ($previousWinnerId && $previousWinnerId !== auth()->id()) {
                $lockedAuction->recordDisplacedLeader($previousWinnerId, $previousPrice);
            }

            // Catat bid ke database
            AuctionBid::create([
                'auction_id' => $lockedAuction->id,
                'user_id' => auth()->id(),
                'bid_amount' => $request->bid_amount,
            ]);

            // Terapkan Anti-Sniping (+2 menit jika tawaran masuk di <120 detik terakhir)
            $extended = $lockedAuction->applyAntiSnipingIfNeeded();

            // Update current price dan pemenang sementara
            $lockedAuction->update([
                'current_price' => $request->bid_amount,
                'winner_id' => auth()->id(),
            ]);

            // Kirim notifikasi ke penawar tertinggi sebelumnya jika ada dan bukan diri sendiri (Outbid Notification)
            if ($previousWinnerId && $previousWinnerId !== auth()->id()) {
                UserNotification::send(
                    $previousWinnerId,
                    auth()->id(),
                    'auction_outbid',
                    'Tawaran Anda Telah Disalip!',
                    'Tawaran Anda untuk "'.$lockedAuction->title.'" telah disalip oleh '.auth()->user()->name.' dengan penawaran Rp'.number_format($request->bid_amount, 0, ',', '.').'. Pasang tawaran lebih tinggi untuk memenangkan lelang!',
                    route('communities.show', $lockedAuction->community->slug).'?tab=auctions',
                    'fa-arrow-up-right-dots',
                    'text-rose-500'
                );
            }

            // Kirim notifikasi ke penyelenggara/ketua lelang
            UserNotification::send(
                $lockedAuction->user_id,
                auth()->id(),
                'auction_new_bid',
                'Tawaran Baru Masuk di Lelang Anda',
                auth()->user()->name.' mengajukan penawaran sebesar Rp'.number_format($request->bid_amount, 0, ',', '.').' pada "'.$lockedAuction->title.'".',
                route('communities.show', $lockedAuction->community->slug).'?tab=auctions',
                'fa-gavel',
                'text-purple-600'
            );

            $successMsg = 'Tawaran lelang sebesar Rp'.number_format($request->bid_amount, 0, ',', '.').' berhasil diajukan!';
            if ($extended) {
                $successMsg .= ' Sesi lelang otomatis diperpanjang +2 menit (Anti-Sniping).';
            }

            return back()->with('success', $successMsg);
        });
    }

    /**
     * Tutup sesi lelang secara manual oleh Ketua Komunitas / Super Admin
     */
    public function close(CommunityAuction $auction)
    {
        if ($auction->community->user_id !== auth()->id() && ! auth()->user()->isSuperAdmin() && $auction->user_id !== auth()->id()) {
            return back()->with('error', 'Hanya ketua komunitas atau penyelenggara yang berhak menutup sesi lelang.');
        }

        if (in_array($auction->status, ['closed', 'completed', 'cancelled', 'wanprestasi'])) {
            return back()->with('error', 'Sesi lelang ini sudah tidak dalam keadaan aktif.');
        }

        $highestBid = $auction->bids()->first();

        $auction->update([
            'status' => 'closed',
            'winner_id' => $highestBid ? $highestBid->user_id : $auction->winner_id,
            'payment_deadline' => $highestBid ? now()->addHours(48) : null,
        ]);

        if ($auction->winner_id) {
            UserNotification::send(
                $auction->winner_id,
                auth()->id(),
                'auction_won',
                'Selamat! Anda Memenangkan Lelang',
                'Sesi lelang "'.$auction->title.'" di '.$auction->community->name.' telah resmi ditutup dan Anda adalah pemenangnya (Rp'.number_format($auction->current_price, 0, ',', '.').'). Silakan lakukan pelunasan dalam batas waktu 48 jam.',
                route('communities.show', $auction->community->slug).'?tab=auctions',
                'fa-trophy',
                'text-amber-500'
            );
        }

        return back()->with('success', 'Sesi lelang berhasil ditutup dan pemenang telah ditetapkan!');
    }

    /**
     * Batalkan lelang oleh Ketua Komunitas / Super Admin
     */
    public function cancel(CommunityAuction $auction)
    {
        if ($auction->community->user_id !== auth()->id() && ! auth()->user()->isSuperAdmin() && $auction->user_id !== auth()->id()) {
            return back()->with('error', 'Hanya ketua komunitas atau penyelenggara yang berhak membatalkan program lelang.');
        }

        if ($auction->status === 'completed') {
            return back()->with('error', 'Lelang yang sudah selesai dibayar tidak dapat dibatalkan.');
        }

        $auction->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Program lelang telah dibatalkan.');
    }

    /**
     * Selesaikan lelang dan proses checkout pemenang
     */
    public function checkoutWinner(CommunityAuction $auction)
    {
        if (! $auction->winner_id) {
            return back()->with('error', 'Lelang belum memiliki pemenang.');
        }

        if ($auction->status === 'completed') {
            return back()->with('error', 'Transaksi lelang ini sudah lunas dibayar.');
        }

        if (auth()->id() !== $auction->winner_id && auth()->id() !== $auction->community->user_id && ! auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Hanya pemenang lelang atau ketua komunitas yang dapat memproses transaksi ini.');
        }

        // Cek jika sudah ada transaksi pending untuk lelang ini
        $existing = Transaction::where('type', 'auction')
            ->where('reference_id', $auction->id)
            ->where('user_id', $auction->winner_id)
            ->where('payment_status', 'pending')
            ->first();

        if ($existing) {
            return redirect()->route('payment.checkout', $existing->transaction_code);
        }

        // Hitung fee lelang: 1% jika anggota komunitas, 2% jika umum
        $feeData = $auction->calculateFee($auction->winner_id);

        $transactionCode = 'AUCT-'.strtoupper(Str::random(10));

        $transaction = Transaction::create([
            'transaction_code' => $transactionCode,
            'user_id' => $auction->winner_id,
            'type' => 'auction',
            'reference_id' => $auction->id,
            'amount' => $auction->current_price,
            'platform_fee_percent' => $feeData['fee_percent'],
            'platform_fee_amount' => $feeData['fee_amount'],
            'total_amount' => $feeData['total_amount'],
            'payment_method' => 'qris',
            'payment_status' => 'pending',
            'notes' => 'Penyelesaian lelang '.$auction->title.' komunitas '.$auction->community->name,
        ]);

        return redirect()->route('payment.checkout', $transaction->transaction_code);
    }

    /**
     * Gugurkan pemenang wanprestasi & alihkan ke pemenang cadangan (runner-up)
     */
    public function declareWanprestasi(CommunityAuction $auction)
    {
        if ($auction->community->user_id !== auth()->id() && ! auth()->user()->isSuperAdmin() && $auction->user_id !== auth()->id()) {
            return back()->with('error', 'Hanya ketua komunitas atau penyelenggara lelang yang berhak memproses status wanprestasi.');
        }

        if (! $auction->isAwaitingPayment() && ! $auction->isWanprestasi()) {
            return back()->with('error', 'Lelang ini tidak dalam status menunggu pelunasan.');
        }

        $hadRunnerUp = $auction->runner_up_id !== null;
        $auction->markAsWanprestasi();

        if ($hadRunnerUp) {
            return back()->with('success', 'Pemenang utama dinyatakan wanprestasi. Hak penebusan lelang berhasil dialihkan ke Pemenang Cadangan (Runner-up) dengan batas waktu 48 jam.');
        }

        return back()->with('success', 'Pemenang lelang dinyatakan wanprestasi dan sesi lelang telah resmi ditutup.');
    }

    /**
     * Unduh / Tampilkan Kutipan Hasil Lelang CommunityHub (Digital Deed)
     */
    public function certificate(CommunityAuction $auction)
    {
        if (! $auction->winner_id && ! in_array($auction->status, ['closed', 'completed'])) {
            return redirect()->route('communities.show', $auction->community->slug)
                ->with('error', 'Kutipan Hasil Lelang hanya tersedia untuk lelang yang telah memiliki pemenang.');
        }

        $auction->load(['community', 'creator', 'winner', 'runnerUp', 'bids.user']);

        $qrUrl = route('communities.auctions.certificate', $auction->id);
        $qrCode = QrCode::size(140)->color(15, 23, 42)->generate($qrUrl);

        $feeData = $auction->calculateFee($auction->winner_id);

        return view('auctions.certificate', compact('auction', 'qrCode', 'feeData'));
    }

    /**
     * Real-time polling endpoint for live auction ticker
     */
    public function ticker(CommunityAuction $auction)
    {
        $auction->autoFinalizeIfNeeded();

        $winner = null;
        if ($auction->winner) {
            $winner = [
                'id' => $auction->winner->id,
                'name' => $auction->winner->name,
                'avatar' => $auction->winner->avatar_url,
            ];
        }

        $minNextBid = (float) $auction->current_price + (float) $auction->bid_increment;

        return response()->json([
            'id' => $auction->id,
            'status' => $auction->status,
            'is_live' => $auction->isActive(),
            'is_ended' => $auction->isEnded(),
            'current_price' => (float) $auction->current_price,
            'current_price_formatted' => 'Rp'.number_format($auction->current_price, 0, ',', '.'),
            'min_next_bid' => $minNextBid,
            'min_next_bid_formatted' => 'Rp'.number_format($minNextBid, 0, ',', '.'),
            'total_bids' => $auction->bids()->count(),
            'winner' => $winner,
            'end_time_iso' => $auction->end_time ? $auction->end_time->toISOString() : null,
        ]);
    }
}
