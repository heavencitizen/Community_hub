<?php

namespace App\Models;

use App\Support\MediaHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CommunityAuction extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_code',
        'community_id',
        'user_id',
        'title',
        'slug',
        'description',
        'notes',
        'starting_price',
        'bid_increment',
        'current_price',
        'image',
        'start_time',
        'end_time',
        'payment_deadline',
        'winner_id',
        'runner_up_id',
        'runner_up_bid',
        'wanprestasi_at',
        'status',
        'bidding_type',
        'anti_sniping',
    ];

    protected $casts = [
        'starting_price' => 'decimal:2',
        'bid_increment' => 'decimal:2',
        'current_price' => 'decimal:2',
        'runner_up_bid' => 'decimal:2',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'payment_deadline' => 'datetime',
        'wanprestasi_at' => 'datetime',
        'anti_sniping' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (CommunityAuction $auction) {
            if (empty($auction->auction_code)) {
                $auction->auction_code = static::generateUniqueCode();
            }
        });
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = 'CH-AUC-'.date('Ym').'-'.strtoupper(Str::random(5));
        } while (static::where('auction_code', $code)->exists());

        return $code;
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function runnerUp(): BelongsTo
    {
        return $this->belongsTo(User::class, 'runner_up_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(AuctionBid::class, 'auction_id')->orderBy('bid_amount', 'desc');
    }

    /**
     * Foto Barang Lelang (Single Source of Truth via MediaHelper)
     */
    public function getImageUrlAttribute(): string
    {
        return MediaHelper::auctionImage($this->id, $this->image);
    }

    /**
     * Cek apakah lelang sudah berakhir
     */
    public function isEnded(): bool
    {
        if (in_array($this->status, ['closed', 'completed', 'cancelled', 'wanprestasi'])) {
            return true;
        }

        return $this->end_time && $this->end_time->isPast();
    }

    /**
     * Cek apakah lelang masih dalam status terjadwal (belum mulai)
     */
    public function isScheduled(): bool
    {
        return $this->status === 'active' && $this->start_time && $this->start_time->isFuture();
    }

    /**
     * Cek apakah lelang masih aktif berjalan (Live)
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && ! $this->isScheduled() && ! $this->isEnded();
    }

    /**
     * Cek apakah lelang dalam mode Open Bidding aktif
     */
    public function isOpenBidding(): bool
    {
        return $this->isActive() && ($this->bidding_type ?? 'open') === 'open';
    }

    /**
     * Cek apakah lelang telah ditutup dan menunggu pembayaran pemenang
     */
    public function isAwaitingPayment(): bool
    {
        return ($this->status === 'closed' || ($this->isEnded() && ! in_array($this->status, ['completed', 'cancelled', 'wanprestasi'])))
            && $this->winner_id !== null
            && ! $this->isPaid()
            && ! $this->isWanprestasi();
    }

    /**
     * Cek apakah pemenang lelang dinyatakan wanprestasi
     */
    public function isWanprestasi(): bool
    {
        return $this->status === 'wanprestasi' || ($this->wanprestasi_at !== null && $this->status !== 'completed');
    }

    /**
     * Cek apakah batas waktu pelunasan pemenang telah terlewati
     */
    public function isPaymentExpired(): bool
    {
        return $this->isAwaitingPayment() && $this->payment_deadline && $this->payment_deadline->isPast();
    }

    /**
     * Cek apakah transaksi lelang telah lunas dibayar
     */
    public function isPaid(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Terapkan aturan Anti-Sniping:
     * Jika penawaran sah masuk di 120 detik terakhir sebelum batas akhir,
     * otomatis perpanjang waktu lelang sebesar +2 menit untuk menjaga fairness.
     */
    public function applyAntiSnipingIfNeeded(): bool
    {
        if (! $this->anti_sniping || ! $this->end_time || $this->isEnded()) {
            return false;
        }

        $remainingSecs = now()->diffInSeconds($this->end_time, false);
        if ($remainingSecs <= 120) {
            $this->end_time = $this->end_time->addMinutes(2);
            $this->save();

            return true;
        }

        return false;
    }

    /**
     * Rekam penawar tertinggi sebelumnya sebagai Pemenang Cadangan (Runner-up)
     */
    public function recordDisplacedLeader(int $displacedUserId, float $displacedAmount): void
    {
        $this->runner_up_id = $displacedUserId;
        $this->runner_up_bid = $displacedAmount;
        $this->save();
    }

    /**
     * Eksekusi status Wanprestasi jika pemenang tidak melunasi tepat waktu.
     * Jika ada pemenang cadangan (runner-up), alihkan hak pembelian kepadanya.
     */
    public function markAsWanprestasi(?string $reason = null): bool
    {
        if (! $this->winner_id) {
            return false;
        }

        $defaultedWinnerId = $this->winner_id;
        $this->wanprestasi_at = now();

        // Notifikasi wanprestasi ke pemenang yang gugur
        UserNotification::send(
            $defaultedWinnerId,
            $this->user_id,
            'auction_wanprestasi',
            'Batas Waktu Pelunasan Lelang Berakhir (Wanprestasi)',
            'Batas waktu pelunasan untuk lelang "'.$this->title.'" telah terlewati tanpa pembayaran. Hak kemenangan lelang telah dibatalkan.',
            route('communities.show', $this->community->slug).'?tab=auctions',
            'fa-triangle-exclamation',
            'text-rose-600'
        );

        // Jika ada penawar cadangan (runner-up), alihkan hak penebusan ke runner-up
        if ($this->runner_up_id && $this->runner_up_bid) {
            $newWinnerId = $this->runner_up_id;
            $newWinningPrice = $this->runner_up_bid;

            $this->winner_id = $newWinnerId;
            $this->current_price = $newWinningPrice;
            $this->payment_deadline = now()->addHours(48);
            $this->status = 'closed';
            $this->runner_up_id = null;
            $this->runner_up_bid = null;
            $this->save();

            // Notifikasi ke penawar cadangan bahwa hak lelang dialihkan ke dirinya
            UserNotification::send(
                $newWinnerId,
                $this->user_id,
                'auction_runner_up_promoted',
                'Hak Pembelian Lelang Dialihkan Kepada Anda!',
                'Pemenang utama lelang "'.$this->title.'" berhalangan/wanprestasi. Sebagai penawar cadangan (Rp'.number_format($newWinningPrice, 0, ',', '.').'), Anda berhak menebus barang ini dalam 48 jam ke depan.',
                route('communities.show', $this->community->slug).'?tab=auctions',
                'fa-trophy',
                'text-emerald-500'
            );

            return true;
        }

        // Jika tidak ada runner-up
        $this->status = 'closed';
        $this->save();

        return true;
    }

    /**
     * Otomatis finalisasi lelang jika waktu berakhir telah lewat namun status masih active
     */
    public function autoFinalizeIfNeeded(): void
    {
        if ($this->status === 'active' && $this->end_time && $this->end_time->isPast()) {
            $highestBid = $this->bids()->first();

            $this->update([
                'status' => 'closed',
                'winner_id' => $highestBid ? $highestBid->user_id : $this->winner_id,
                'payment_deadline' => $highestBid ? now()->addHours(48) : null,
            ]);

            // Jika ada pemenang, kirim notifikasi kemenangan
            if ($this->winner_id) {
                UserNotification::send(
                    $this->winner_id,
                    $this->user_id,
                    'auction_won',
                    'Selamat! Anda Memenangkan Lelang',
                    'Anda memenangkan lelang "'.$this->title.'" di '.$this->community->name.' dengan tawaran Rp'.number_format($this->current_price, 0, ',', '.').'. Silakan lakukan pelunasan dalam batas waktu 48 jam.',
                    route('communities.show', $this->community->slug).'?tab=auctions',
                    'fa-trophy',
                    'text-amber-500'
                );
            }
        }
    }

    /**
     * Total tawaran yang masuk
     */
    public function totalBids(): int
    {
        return $this->bids()->count();
    }

    /**
     * Penawaran tertinggi saat ini
     */
    public function highestBid(): ?AuctionBid
    {
        return $this->bids()->first();
    }

    /**
     * Sisa detik sebelum lelang berakhir
     */
    public function remainingSeconds(): int
    {
        if ($this->isEnded() || ! $this->end_time) {
            return 0;
        }

        return max(0, now()->diffInSeconds($this->end_time, false));
    }

    /**
     * Sisa detik sebelum jadwal lelang dimulai (untuk lelang terjadwal)
     */
    public function remainingSecondsToStart(): int
    {
        if (! $this->isScheduled() || ! $this->start_time) {
            return 0;
        }

        return max(0, now()->diffInSeconds($this->start_time, false));
    }

    /**
     * Sisa detik batas waktu pelunasan pemenang
     */
    public function remainingPaymentSeconds(): int
    {
        if (! $this->payment_deadline || $this->payment_deadline->isPast()) {
            return 0;
        }

        return max(0, now()->diffInSeconds($this->payment_deadline, false));
    }

    /**
     * Hitung fee lelang untuk pemenang:
     * 1% jika pemenang adalah anggota komunitas
     * 2% jika pemenang adalah umum
     */
    public function calculateFee(?int $winnerUserId): array
    {
        $isMember = $winnerUserId ? $this->community->hasActiveMember($winnerUserId) : false;
        $feePercent = $isMember ? 1.0 : 2.0;
        $feeAmount = round(($this->current_price * $feePercent) / 100, 2);
        $total = $this->current_price + $feeAmount;

        return [
            'is_member' => $isMember,
            'fee_percent' => $feePercent,
            'fee_amount' => $feeAmount,
            'total_amount' => $total,
        ];
    }
}
