<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CommunityPost extends Model
{
    protected $fillable = [
        'community_id',
        'user_id',
        'content',
        'media_url',
        'type',
        'likes_count',
    ];

    protected static function booted(): void
    {
        static::deleting(function (CommunityPost $post) {
            if ($post->media_url && Storage::disk('public')->exists($post->media_url)) {
                Storage::disk('public')->delete($post->media_url);
            }
        });
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(CommunityPostComment::class)->with('user')->latest();
    }

    public function likes()
    {
        return $this->hasMany(CommunityPostLike::class);
    }

    public function isLikedBy($userId): bool
    {
        if (! $userId) {
            return false;
        }

        return $this->likes()->where('user_id', $userId)->exists();
    }
}
