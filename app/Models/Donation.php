<?php

namespace App\Models;

use App\Support\MediaHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_id',
        'user_id',
        'title',
        'slug',
        'description',
        'banner',
        'target_amount',
        'collected_amount',
        'status',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'collected_amount' => 'decimal:2',
    ];

    /**
     * Banner Donasi (Single Source of Truth via MediaHelper)
     */
    public function getBannerUrlAttribute(): string
    {
        return MediaHelper::donationBanner($this->id, $this->banner);
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'reference_id')->where('type', 'donation')->where('payment_status', 'completed');
    }

    public function progressPercentage(): int
    {
        if ($this->target_amount <= 0) {
            return 100;
        }

        return min(100, (int) round(($this->collected_amount / $this->target_amount) * 100));
    }
}
