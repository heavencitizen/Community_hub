<?php

namespace App\Models;

use App\Support\MediaHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Community extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'category',
        'description',
        'rules',
        'logo',
        'banner',
        'status',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(CommunityPost::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(CommunityMember::class);
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(CommunityMember::class)->where('status', 'active');
    }

    public function products(): HasMany
    {
        return $this->hasMany(CommunityProduct::class);
    }

    public function auctions(): HasMany
    {
        return $this->hasMany(CommunityAuction::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * Logo Komunitas (Single Source of Truth via MediaHelper)
     */
    public function getLogoUrlAttribute(): string
    {
        return MediaHelper::communityLogo($this->id, $this->category, $this->logo);
    }

    /**
     * Banner Komunitas (Single Source of Truth via MediaHelper)
     */
    public function getBannerUrlAttribute(): string
    {
        return MediaHelper::communityBanner($this->id, $this->category, $this->banner);
    }

    public function hasMember(int $userId): bool
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    public function hasActiveMember(int $userId): bool
    {
        return $this->members()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->exists();
    }

    public function getMembership(int $userId): ?CommunityMember
    {
        return $this->members()->where('user_id', $userId)->first();
    }
}
