<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'user_id',
        'type',
        'reference_id',
        'amount',
        'platform_fee_percent',
        'platform_fee_amount',
        'total_amount',
        'payment_method',
        'payment_status',
        'payment_code',
        'payment_details',
        'guest_name',
        'guest_email',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee_percent' => 'decimal:2',
        'platform_fee_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payment_details' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCustomerNameAttribute(): string
    {
        return $this->user ? $this->user->name : ($this->guest_name ?? 'Tamu / Umum');
    }

    public function getCustomerEmailAttribute(): ?string
    {
        return $this->user ? $this->user->email : $this->guest_email;
    }
}
