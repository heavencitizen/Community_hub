<?php

namespace App\Models;

use App\Support\MediaHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'community_id',
        'title',
        'slug',
        'description',
        'location',
        'event_date',
        'price',
        'admin_fee',
        'quota',
        'banner',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'price' => 'integer',
        'admin_fee' => 'integer',
        'quota' => 'integer',
    ];

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Banner Event (Single Source of Truth via MediaHelper)
     */
    public function getBannerUrlAttribute(): string
    {
        return MediaHelper::eventBanner($this->id, $this->banner);
    }

    /**
     * Hitung kalkulasi harga tiket dan fee:
     * Fee tiket: 2% jika anggota komunitas, 5% jika pengguna umum
     */
    public function calculateFee(?int $buyerUserId): array
    {
        if ($this->price <= 0) {
            return [
                'is_member' => false,
                'fee_percent' => 0,
                'fee_amount' => 0,
                'total_amount' => 0,
            ];
        }

        $isMember = $buyerUserId ? $this->community->hasActiveMember($buyerUserId) : false;
        $feePercent = $isMember ? 2.0 : 5.0;
        $feeAmount = round(($this->price * $feePercent) / 100, 2);
        $total = $this->price + $feeAmount;

        return [
            'is_member' => $isMember,
            'fee_percent' => $feePercent,
            'fee_amount' => $feeAmount,
            'total_amount' => $total,
        ];
    }

    public function totalPrice(): int
    {
        return $this->price + $this->admin_fee;
    }

    public function remainingQuota(): int
    {
        $sold = $this->tickets()->whereIn('status', ['approved', 'pending'])->count();

        return max(0, $this->quota - $sold);
    }
}
