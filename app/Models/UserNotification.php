<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sender_id',
        'type',
        'title',
        'message',
        'link',
        'icon',
        'icon_color',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Helper cepat untuk mengirim notifikasi in-app
     */
    public static function send(
        int $userId,
        ?int $senderId,
        string $type,
        string $title,
        string $message,
        ?string $link = null,
        string $icon = 'fa-bell',
        string $iconColor = 'text-indigo-500'
    ): ?self {
        // Jangan kirim notifikasi ke diri sendiri jika sender === recipient
        if ($senderId && $senderId === $userId) {
            return null;
        }

        return self::create([
            'user_id' => $userId,
            'sender_id' => $senderId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'icon' => $icon,
            'icon_color' => $iconColor,
            'is_read' => false,
        ]);
    }
}
