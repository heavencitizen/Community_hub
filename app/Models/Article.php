<?php

namespace App\Models;

use App\Support\MediaHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = [
        'user_id',
        'community_id',
        'title',
        'slug',
        'content',
        'image',
        'category',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Thumbnail Foto Berita/Artikel (Single Source of Truth via MediaHelper)
     */
    public function getThumbnailUrlAttribute(): string
    {
        return MediaHelper::articleThumbnail($this->id, $this->category, $this->image);
    }

    // Scope: hanya artikel yang sudah dipublish
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    // Scope: berdasarkan kategori
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }
}
