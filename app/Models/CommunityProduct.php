<?php

namespace App\Models;

use App\Support\MediaHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CommunityProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_id',
        'user_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    protected static function booted(): void
    {
        static::deleting(function (CommunityProduct $product) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
        });
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Foto Produk (Single Source of Truth via MediaHelper)
     */
    public function getImageUrlAttribute(): string
    {
        return MediaHelper::productImage($this->id, $this->image);
    }

    /**
     * Hitung fee marketplace:
     * 1% jika pembeli adalah anggota komunitas
     * 2% jika pembeli adalah umum (non-anggota)
     */
    public function calculateFee(?int $buyerUserId): array
    {
        $isMember = $buyerUserId ? $this->community->hasActiveMember($buyerUserId) : false;
        $feePercent = $isMember ? 1.0 : 2.0;
        $feeAmount = round(($this->price * $feePercent) / 100, 2);
        $total = $this->price + $feeAmount;

        return [
            'is_member' => $isMember,
            'fee_percent' => $feePercent,
            'fee_amount' => $feeAmount,
            'total_amount' => $total,
        ];
    }
}
