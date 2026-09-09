<?php

namespace App\Models;

use App\Support\MediaHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'avatar',
        'bio',
        'banner',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->username)) {
                $base = ! empty($user->email) ? explode('@', $user->email)[0] : $user->name;
                $slug = Str::slug($base, '');
                if (empty($slug)) {
                    $slug = 'user'.uniqid();
                }

                $candidate = $slug;
                $counter = 1;
                while (static::where('username', $candidate)->exists()) {
                    $candidate = $slug.$counter;
                    $counter++;
                }

                $user->username = $candidate;
            }
        });
    }

    // ── Role helpers ──────────────────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isCommunityAdmin(): bool
    {
        return $this->role === 'community_admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Foto Profil Pengguna (Single Source of Truth via MediaHelper)
     */
    public function getAvatarUrlAttribute(): string
    {
        return MediaHelper::userAvatar($this->id, $this->name, $this->avatar);
    }

    /**
     * Foto Sampul / Banner Profil Pengguna (Single Source of Truth via MediaHelper)
     */
    public function getBannerUrlAttribute(): string
    {
        return MediaHelper::userBanner($this->id, $this->banner);
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function communities(): HasMany
    {
        return $this->hasMany(Community::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function communityPosts(): HasMany
    {
        return $this->hasMany(CommunityPost::class);
    }

    public function communityMemberships(): HasMany
    {
        return $this->hasMany(CommunityMember::class);
    }

    public function auctionBids(): HasMany
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
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

    public function appNotifications(): HasMany
    {
        return $this->hasMany(UserNotification::class, 'user_id')->latest();
    }

    protected ?int $unreadNotificationsCountCache = null;

    public function unreadNotificationsCount(): int
    {
        return $this->unreadNotificationsCountCache ??= $this->appNotifications()->where('is_read', false)->count();
    }

    public function clearNotificationsCache(): void
    {
        $this->unreadNotificationsCountCache = null;
    }

    // ── Social Following System ───────────────────────────────────────────────

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_follows', 'follower_id', 'following_id')->withTimestamps();
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_follows', 'following_id', 'follower_id')->withTimestamps();
    }

    public function isFollowing(int|User $user): bool
    {
        $targetId = $user instanceof User ? $user->id : $user;

        return $this->following()->where('following_id', $targetId)->exists();
    }

    public function follow(int|User $user): void
    {
        $targetId = $user instanceof User ? $user->id : $user;
        if ($this->id !== $targetId && ! $this->isFollowing($targetId)) {
            $this->following()->attach($targetId);
        }
    }

    public function unfollow(int|User $user): void
    {
        $targetId = $user instanceof User ? $user->id : $user;
        $this->following()->detach($targetId);
    }

    public function toggleFollow(int|User $user): bool
    {
        $targetId = $user instanceof User ? $user->id : $user;
        if ($this->isFollowing($targetId)) {
            $this->unfollow($targetId);

            return false;
        }

        $this->follow($targetId);

        return true;
    }

    /**
     * Komunitas yang diikuti atau dimiliki pengguna
     */
    public function getJoinedCommunitiesAttribute()
    {
        $membershipIds = $this->communityMemberships()
            ->where('status', 'active')
            ->pluck('community_id')
            ->toArray();

        $ownedIds = Community::where('user_id', $this->id)->pluck('id')->toArray();
        $allIds = array_unique(array_merge($membershipIds, $ownedIds));

        return Community::whereIn('id', $allIds)->get();
    }
}
