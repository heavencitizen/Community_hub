<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'ticket_code',
        'total_price',
        'payment_receipt',
        'status',
        'is_scanned',
        'scanned_at',
    ];

    protected $casts = [
        'is_scanned' => 'boolean',
        'scanned_at' => 'datetime',
        'total_price' => 'integer',
    ];

    // Generate ticket_code otomatis saat dibuat
    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            if (empty($ticket->ticket_code)) {
                $ticket->ticket_code = strtoupper(Str::random(4)).'-'.strtoupper(Str::random(8));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // Helper: apakah tiket sudah approved
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
