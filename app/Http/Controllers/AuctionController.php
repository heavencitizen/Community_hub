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
    public function index(Request $request)
    {
        CommunityAuction::where('status', 'active')
            ->where('end_time', '<=', now())
            ->each(function (CommunityAuction $auction) {
                $auction->autoFinalizeIfNeeded();
            });

        $query = CommunityAuction::with(['community', 'creator', 'winner', 'bids.user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('community_id')) {
            $query->where('community_id', $request->community_id);
        }

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

    public function payDeposit(CommunityAuction $auction)
    {
        if ($auction->user_id === auth()->id()) {
            return back()->with('error', 'Penyelenggara tidak perlu membayar uang jaminan.');
        }

        if (!$auction->isActive()) {
            return back()->with('error', 'Sesi lelang ini belum dimulai atau sudah ditutup.');
        }

        $existing = Transaction::where('type', 'auction_deposit')
            ->where('reference_id', $auction->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            if ($existing->payment_status === 'completed') {
                return back()->with('success', 'Anda sudah membayar uang jaminan untuk lelang ini. Silakan mulai menawar!');
            }
            return redirect()->route('payment.checkout', $existing->transaction_code);
        }

        $depositAmount = max(50000, $auction->starting_price * 0.05);

        $transaction = Transaction::create([
            'transaction_code' => 'DEP-'.strtoupper(Str::random(10)),
            'user_id' => auth()->id(),
            'type' => 'auction_deposit',
            'reference_id' => $auction->id,
            'amount' => $depositAmount,
            'platform_fee_percent' => 0,
            'platform_fee_amount' => 0,
            'total_amount' => $depositAmount,
            'payment_method' => 'qris',
            'payment_status' => 'pending',
            'notes' => 'Uang Jaminan Peserta Lelang: '.$auction->title,
        ]);

        return redirect()->route('payment.checkout', $transaction->transaction_code);
    }

    public function bid(Request $request, CommunityAuction $auction)
    {
        return DB::transaction(function () use ($request, $auction) {
            $lockedAuction = CommunityAuction::where('id', $auction->id)->lockForUpdate()->firstOrFail();
            $lockedAuction->autoFinalizeIfNeeded();

            if ($lockedAuction->isEnded() || $lockedAuction->status !== 'active') {
                return back()->with('error', 'Lelang ini telah berakhir atau ditutup.');
            }

            if ($lockedAuction->isScheduled()) {
                return back()->with('error', 'Lelang ini belum dimulai. Sesi open bidding baru akan dibuka pada '.$lockedAuction->start_time->format('d M Y, H:i').' WIB.');
            }

            if ($lockedAuction->user_id === auth()->id()) {
                return back()->with('error', 'Anda adalah penyelenggara lelang ini, tidak dapat menawar barang sendiri.');
            }

            // --- PROTEKSI UANG JAMINAN ---
            $hasPaidDeposit = Transaction::where('type', 'auction_deposit')
                ->where('reference_id', $lockedAuction->id)
                ->where('user_id', auth()->id())
                ->where('payment_status', 'completed')
                ->exists();

            if (!$hasPaidDeposit) {
                return back()->with('error', 'AKSES DITOLAK: Anda harus membayar Uang Jaminan Lelang terlebih dahulu sebelum dapat mengajukan penawaran.');
            }
            // -----------------------------

            if ($lockedAuction->winner_id === auth()->id()) {
                return back()->with('error', 'Anda sudah memegang tawaran tertinggi saat ini.');
            }

            $minBid = (float) $lockedAuction->current_price + (float) $lockedAuction->bid_increment;

            $request->validate([
                'bid_amount' => 'required|numeric|min:'.$minBid,
            ], [
                'bid_amount.min' => 'Nilai tawaran minimal adalah Rp'.number_format($minBid, 0, ',', '.').'.',
            ]);

            $previousWinnerId = $lockedAuction->winner_id;
            $previousPrice = (float) $lockedAuction->current_price;

            if ($previousWinnerId && $previousWinnerId !== auth()->id()) {
                $lockedAuction->recordDisplacedLeader($previousWinnerId, $previousPrice);
            }

            AuctionBid::create([
                'auction_id' => $lockedAuction->id,
                'user_id' => auth()->id(),
                'bid_amount' => $request->bid_amount,
            ]);

            $extended = $lockedAuction->applyAntiSnipingIfNeeded();

            $lockedAuction->update([
                'current_price' => $request->bid_amount,
                'winner_id' => auth()->id(),
            ]);

            if ($previousWinnerId && $previousWinnerId !== auth()->id()) {
                UserNotification::send(
                    $previousWinnerId,
                    auth()->id(),
                    'auction_outbid',
                    'Tawaran Anda Telah Disalip!',
                    'Tawaran Anda untuk "'.$lockedAuction->title.'" telah disalip oleh '.auth()->user()->name.' (Rp'.number_format($request->bid_amount, 0, ',', '.').'). Pasang tawaran lebih tinggi!',
                    route('communities.show', $lockedAuction->community->slug).'?tab=auctions',
                    'fa-arrow-up-right-dots',
                    'text-rose-500'
                );
            }

            UserNotification::send(
                $lockedAuction->user_id,
                auth()->id(),
                'auction_new_bid',
                'Tawaran Baru Masuk di Lelang',
                auth()->user()->name.' menawar Rp'.number_format($request->bid_amount, 0, ',', '.').' pada "'.$lockedAuction->title.'".',
                route('communities.show', $lockedAuction->community->slug).'?tab=auctions',
                'fa-gavel',
                'text-purple-600'
            );

            $successMsg = 'Tawaran lelang berhasil diajukan!';
            if ($extended) $successMsg .= ' Sesi lelang otomatis diperpanjang +2 menit (Anti-Sniping).';

            return back()->with('success', $successMsg);
        });
    }

    public function close(CommunityAuction $auction)
    {
        if ($auction->community->user_id !== auth()->id() && ! auth()->user()->isSuperAdmin() && $auction->user_id !== auth()->id()) {
            return back()->with('error', 'Akses ditolak.');
        }

        if (in_array($auction->status, ['closed', 'completed', 'cancelled', 'wanprestasi'])) {
            return back()->with('error', 'Sesi lelang ini sudah tidak aktif.');
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
                'Sesi lelang "'.$auction->title.'" resmi ditutup dan Anda adalah pemenangnya. Silakan lunasi dalam 48 jam.',
                route('communities.show', $auction->community->slug).'?tab=auctions',
                'fa-trophy',
                'text-amber-500'
            );
        }

        return back()->with('success', 'Sesi lelang berhasil ditutup!');
    }

    public function cancel(CommunityAuction $auction)
    {
        if ($auction->community->user_id !== auth()->id() && ! auth()->user()->isSuperAdmin() && $auction->user_id !== auth()->id()) {
            return back()->with('error', 'Akses ditolak.');
        }

        if ($auction->status === 'completed') {
            return back()->with('error', 'Lelang yang selesai tidak dapat dibatalkan.');
        }

        $auction->update(['status' => 'cancelled']);
        return back()->with('success', 'Lelang dibatalkan.');
    }

    public function checkoutWinner(Request $request, CommunityAuction $auction)
    {
        if (! $auction->winner_id) return back()->with('error', 'Lelang belum ada pemenang.');
        if ($auction->status === 'completed') return back()->with('error', 'Sudah lunas.');
        if (auth()->id() !== $auction->winner_id && auth()->id() !== $auction->community->user_id && ! auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Akses ditolak.');
        }

        $paymentMethod = $request->input('payment_method', 'system');

        $existing = Transaction::where('type', 'auction')->where('reference_id', $auction->id)
            ->where('user_id', $auction->winner_id)->whereIn('payment_status', ['pending', 'pending_cod'])->first();

        // POTONGAN OTOMATIS: Uang Jaminan
        $depositPaid = Transaction::where('type', 'auction_deposit')
            ->where('reference_id', $auction->id)
            ->where('user_id', $auction->winner_id)
            ->where('payment_status', 'completed')
            ->value('amount') ?? 0;

        $feeData = $auction->calculateFee($auction->winner_id);
        
        // Tagihan akhir = (Harga Lelang + Fee) - Uang Jaminan
        $finalTagihan = max(0, $feeData['total_amount'] - $depositPaid);

        // Jika Pemenang Memilih COD
        if ($paymentMethod === 'cod') {
            if ($existing) {
                $existing->update([
                    'payment_method' => 'cod', 
                    'payment_status' => 'pending_cod', 
                    'notes' => 'Pelunasan Lelang COD: '.$auction->title.' (Sisa Tagihan: Rp'.number_format($finalTagihan, 0, ',', '.').')'
                ]);
            } else {
                Transaction::create([
                    'transaction_code' => 'COD-'.strtoupper(Str::random(10)),
                    'user_id' => $auction->winner_id,
                    'type' => 'auction',
                    'reference_id' => $auction->id,
                    'amount' => $auction->current_price,
                    'platform_fee_percent' => $feeData['fee_percent'],
                    'platform_fee_amount' => $feeData['fee_amount'],
                    'total_amount' => $finalTagihan,
                    'payment_method' => 'cod',
                    'payment_status' => 'pending_cod',
                    'notes' => 'Pelunasan Lelang COD: '.$auction->title.' (Sisa Tagihan: Rp'.number_format($finalTagihan, 0, ',', '.').')',
                ]);
            }

            UserNotification::send(
                $auction->user_id, auth()->id(), 'auction_cod', 'Pemenang Memilih COD',
                auth()->user()->name.' memilih opsi Bayar di Tempat (COD) untuk melunasi "'.$auction->title.'". Silakan hubungi pemenang untuk serah terima.',
                route('communities.show', $auction->community->slug).'?tab=auctions', 'fa-handshake', 'text-emerald-600'
            );

            return back()->with('success', 'Metode Bayar di Tempat (COD) berhasil dipilih! Silakan hubungi penyelenggara untuk serah terima barang.');
        }

        // Jika Pemenang Memilih Transfer Midtrans (System)
        $transactionCode = 'AUCT-'.strtoupper(Str::random(10));
        
        if ($existing) {
            $existing->update([
                'payment_method' => 'qris', 
                'payment_status' => 'pending', 
                'transaction_code' => $transactionCode
            ]);
            $transaction = $existing;
        } else {
            $transaction = Transaction::create([
                'transaction_code' => $transactionCode,
                'user_id' => $auction->winner_id,
                'type' => 'auction',
                'reference_id' => $auction->id,
                'amount' => $auction->current_price,
                'platform_fee_percent' => $feeData['fee_percent'],
                'platform_fee_amount' => $feeData['fee_amount'],
                'total_amount' => $finalTagihan,
                'payment_method' => 'qris',
                'payment_status' => 'pending',
                'notes' => 'Pelunasan Lelang: '.$auction->title.' (Diposting Uang Jaminan Rp'.number_format($depositPaid, 0, ',', '.').')',
            ]);
        }

        return redirect()->route('payment.checkout', $transaction->transaction_code);
    }

    /**
     * FUNGSI BARU: Konfirmasi COD Selesai (Oleh Ketua/Penyelenggara)
     */
    public function completeCod(CommunityAuction $auction)
    {
        if ($auction->community->user_id !== auth()->id() && $auction->user_id !== auth()->id() && !auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Akses ditolak.');
        }

        $transaction = Transaction::where('type', 'auction')
            ->where('reference_id', $auction->id)
            ->where('payment_method', 'cod')
            ->where('payment_status', 'pending_cod')
            ->first();

        if ($transaction) {
            $transaction->update(['payment_status' => 'completed']);
        }
        
        $auction->update(['status' => 'completed']);

        UserNotification::send(
            $auction->winner_id, auth()->id(), 'auction_cod_success', 'Transaksi Selesai',
            'Penyelenggara telah mengonfirmasi pembayaran COD untuk "'.$auction->title.'". Lelang resmi dinyatakan Lunas.',
            route('communities.show', $auction->community->slug).'?tab=auctions', 'fa-check-circle', 'text-emerald-600'
        );

        return back()->with('success', 'Transaksi COD berhasil dikonfirmasi dan Lelang resmi dinyatakan Lunas!');
    }

    public function declareWanprestasi(CommunityAuction $auction)
    {
        if ($auction->community->user_id !== auth()->id() && ! auth()->user()->isSuperAdmin() && $auction->user_id !== auth()->id()) {
            return back()->with('error', 'Akses ditolak.');
        }
        if (! $auction->isAwaitingPayment() && ! $auction->isWanprestasi()) {
            return back()->with('error', 'Status tidak valid.');
        }

        $hadRunnerUp = $auction->runner_up_id !== null;
        $auction->markAsWanprestasi();

        if ($hadRunnerUp) {
            return back()->with('success', 'Pemenang utama dinyatakan wanprestasi. Hak penebusan lelang berhasil dialihkan ke Pemenang Cadangan (Runner-up).');
        }
        return back()->with('success', 'Pemenang lelang dinyatakan wanprestasi dan sesi lelang telah resmi ditutup (Uang Jaminan ditahan sistem).');
    }

    public function certificate(CommunityAuction $auction)
    {
        if (! $auction->winner_id && ! in_array($auction->status, ['closed', 'completed'])) {
            return redirect()->route('communities.show', $auction->community->slug)->with('error', 'Belum ada pemenang.');
        }
        $auction->load(['community', 'creator', 'winner', 'runnerUp', 'bids.user']);
        $qrUrl = route('communities.auctions.certificate', $auction->id);
        $qrCode = QrCode::size(140)->color(15, 23, 42)->generate($qrUrl);
        $feeData = $auction->calculateFee($auction->winner_id);
        return view('auctions.certificate', compact('auction', 'qrCode', 'feeData'));
    }

    public function ticker(CommunityAuction $auction)
    {
        $auction->autoFinalizeIfNeeded();
        $winner = null;
        if ($auction->winner) {
            $winner = ['id' => $auction->winner->id, 'name' => $auction->winner->name, 'avatar' => $auction->winner->avatar_url];
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