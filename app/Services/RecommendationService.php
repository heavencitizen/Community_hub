<?php

namespace App\Services;

use App\Models\Community;
use App\Models\CommunityPost;
use App\Models\CommunityPostComment;
use App\Models\CommunityPostLike;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class RecommendationService
{
    /**
     * Bobot Algoritma Pembobotan Konten (Scoring Weights)
     */
    protected const WEIGHT_LIKE = 2.0;

    protected const WEIGHT_COMMENT = 3.5;

    protected const BONUS_MEDIA = 1.5;

    protected const BONUS_JOINED_COMMUNITY = 15.0;

    protected const BONUS_CATEGORY_AFFINITY_MAX = 12.0;

    protected const BONUS_FOLLOWED_AUTHOR = 8.0;

    protected const BONUS_INTERACTED_AUTHOR = 4.0;

    protected const BASE_SCORE = 5.0;

    protected const TIME_DECAY_GRAVITY = 1.2;

    protected const TIME_DECAY_HALFLIFE_HOURS = 24.0;

    /**
     * 1. CONTENT RANKING: Personalized Feed ("Untuk Anda")
     */
    public function getPersonalizedFeed(User $user, int $perPage = 10, ?int $page = null): LengthAwarePaginator
    {
        $page = $page ?: Paginator::resolveCurrentPage('page');

        // 1. Ekstrak Konteks Pengguna (User Behavioral Signals)
        $context = $this->buildUserContext($user);

        // 2. Ambil Kandidat Postingan Aktif (hingga 150 postingan terbaru dalam 45 hari)
        $candidatePosts = CommunityPost::with([
            'author',
            'community',
            'comments.user',
            'likes.user',
        ])
            ->withCount(['comments', 'likes'])
            ->where('created_at', '>=', now()->subDays(45))
            ->orderBy('id', 'desc')
            ->limit(150)
            ->get();

        // Fallback jika database masih sedikit konten
        if ($candidatePosts->isEmpty()) {
            $candidatePosts = CommunityPost::with([
                'author',
                'community',
                'comments.user',
                'likes.user',
            ])
                ->withCount(['comments', 'likes'])
                ->latest()
                ->limit(50)
                ->get();
        }

        // 3. Hitung Skor Tiap Postingan Berdasarkan Rule-Based Hybrid Engine
        $scoredPosts = $candidatePosts->map(function (CommunityPost $post) use ($user, $context) {
            $scoreData = $this->calculatePostScore($post, $user, $context);
            $post->recommendation_score = $scoreData['score'];
            $post->recommendation_reason = $scoreData['reason'];
            $post->score_breakdown = $scoreData['breakdown'];

            return $post;
        });

        // 4. Urutkan berdasarkan skor tertinggi (Ranked Content)
        $sortedPosts = $scoredPosts->sortByDesc('recommendation_score')->values();

        // 5. Buat LengthAwarePaginator agar kompatibel dengan view pagination Blade
        $pagedSlice = $sortedPosts->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $pagedSlice,
            $sortedPosts->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );
    }

    /**
     * Hitung skor individual untuk sebuah postingan terhadap pengguna.
     */
    public function calculatePostScore(CommunityPost $post, User $user, array $context): array
    {
        // A. Skor Engagement & Popularitas Konten (S_engagement)
        $likesScore = ($post->likes_count ?? 0) * self::WEIGHT_LIKE;
        $commentsScore = ($post->comments_count ?? 0) * self::WEIGHT_COMMENT;
        $mediaBonus = $post->media_url ? self::BONUS_MEDIA : 0.0;

        $engagementScore = $likesScore + $commentsScore + $mediaBonus;

        // B. Skor Afinitas Personal Pengguna (S_affinity)
        $isMember = in_array($post->community_id, $context['my_community_ids']);
        $membershipBonus = $isMember ? self::BONUS_JOINED_COMMUNITY : 0.0;

        $category = strtolower($post->community->category ?? '');
        $affinityRatio = $context['category_affinities'][$category] ?? 0.0;
        $categoryBonus = self::BONUS_CATEGORY_AFFINITY_MAX * $affinityRatio;

        $isFollowedAuthor = in_array($post->user_id, $context['followed_author_ids']);
        $authorFollowBonus = $isFollowedAuthor ? self::BONUS_FOLLOWED_AUTHOR : 0.0;

        $hasInteractedWithAuthor = in_array($post->user_id, $context['interacted_author_ids']);
        $authorInteractionBonus = (! $isFollowedAuthor && $hasInteractedWithAuthor) ? self::BONUS_INTERACTED_AUTHOR : 0.0;

        $affinityScore = $membershipBonus + $categoryBonus + $authorFollowBonus + $authorInteractionBonus;

        // C. Fungsi Peluruhan Waktu (Gravity Time Decay)
        $hoursOld = max(0.1, Carbon::parse($post->created_at)->diffInMinutes(now()) / 60.0);
        $timeDecay = 1.0 / pow(1.0 + ($hoursOld / self::TIME_DECAY_HALFLIFE_HOURS), self::TIME_DECAY_GRAVITY);

        // D. Final Calculated Score
        $totalRawScore = self::BASE_SCORE + $engagementScore + $affinityScore;
        $finalScore = round($totalRawScore * $timeDecay, 2);

        // E. Tentukan Alasan Rekomendasi (Explainable AI / Transparency)
        $reason = $this->determineRecommendationReason(
            $isMember,
            $affinityRatio,
            $category,
            $isFollowedAuthor,
            $commentsScore,
            $likesScore,
            $hoursOld
        );

        return [
            'score' => $finalScore,
            'reason' => $reason,
            'breakdown' => [
                'engagement' => round($engagementScore, 1),
                'affinity' => round($affinityScore, 1),
                'decay' => round($timeDecay, 3),
                'hours_old' => round($hoursOld, 1),
            ],
        ];
    }

    /**
     * 2. SOCIAL RECOMMENDATION: Saran Pertemanan / Anggota Komunitas Relevan
     */
    public function getSocialRecommendations(User $user, int $limit = 5): Collection
    {
        // Pengguna yang sudah diikuti atau diri sendiri
        $excludedUserIds = $user->following()->pluck('following_id')->push($user->id)->toArray();

        $context = $this->buildUserContext($user);
        $myCommIds = $context['my_community_ids'];
        $topCategories = array_keys(array_filter($context['category_affinities'], fn ($val) => $val > 0.2));

        if (empty($topCategories)) {
            $topCategories = ['olahraga', 'otomotif', 'kuliner', 'seni'];
        }

        // Ambil kandidat user dari komunitas yang sama atau yang aktif di kategori serupa
        $candidates = User::whereNotIn('id', $excludedUserIds)
            ->with([
                'communityMemberships.community',
                'communityPosts' => fn ($q) => $q->latest()->take(3),
            ])
            ->withCount(['communityMemberships', 'communityPosts', 'followers'])
            ->take(30)
            ->get();

        if ($candidates->isEmpty()) {
            return collect();
        }

        $scoredCandidates = $candidates->map(function (User $candidate) use ($user, $myCommIds, $topCategories) {
            $candidateCommIds = $candidate->communityMemberships->pluck('community_id')->toArray();
            $sharedCommCount = count(array_intersect($myCommIds, $candidateCommIds));

            $candidateCategories = $candidate->communityMemberships->pluck('community.category')
                ->filter()
                ->map(fn ($c) => strtolower($c))
                ->unique()
                ->toArray();

            $sharedCategoryMatches = array_intersect($topCategories, $candidateCategories);
            $sharedCategoryCount = count($sharedCategoryMatches);

            // Apakah kandidat ini mengikuti pengguna saat ini? (Follow-back opportunity)
            $isFollowingMe = $candidate->isFollowing($user->id);

            // Perhitungan skor relasi sosial
            $score = 0.0;
            $score += $sharedCommCount * 6.0;
            $score += $sharedCategoryCount * 4.0;
            $score += $isFollowingMe ? 8.0 : 0.0;
            $score += min($candidate->community_posts_count, 5) * 1.0;

            // Alasan rekomendasi yang jelas bagi pengguna
            $reason = 'Anggota Aktif';
            if ($isFollowingMe) {
                $reason = 'Mengikuti Anda';
            } elseif ($sharedCommCount > 0) {
                $reason = "{$sharedCommCount} Komunitas Bersama";
            } elseif (! empty($sharedCategoryMatches)) {
                $firstCat = ucfirst(reset($sharedCategoryMatches));
                $reason = "Minat Sama: {$firstCat}";
            }

            $candidate->social_score = $score;
            $candidate->recommendation_reason = $reason;
            $candidate->shared_communities_count = $sharedCommCount;

            return $candidate;
        });

        return $scoredCandidates
            ->sortByDesc('social_score')
            ->take($limit)
            ->values();
    }

    /**
     * 3. RELATED CONTENT RECOMMENDATION: Postingan Terkait untuk Pengguna
     */
    public function getRelatedPostsForUser(User $user, int $limit = 3): Collection
    {
        $context = $this->buildUserContext($user);
        $topCategories = array_keys(array_filter($context['category_affinities'], fn ($val) => $val > 0.15));

        $query = CommunityPost::with(['author', 'community'])
            ->withCount('comments')
            ->where('user_id', '!=', $user->id);

        if (! empty($topCategories)) {
            $query->whereHas('community', function ($q) use ($topCategories) {
                $q->whereIn('category', $topCategories);
            });
        }

        return $query->orderBy('likes_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Related Posts untuk Detail Postingan Tertentu (Content-Based Similarity)
     */
    public function getRelatedPosts(CommunityPost $post, ?User $user = null, int $limit = 4): Collection
    {
        $communityId = $post->community_id;
        $category = $post->community->category ?? null;

        return CommunityPost::with(['author', 'community'])
            ->where('id', '!=', $post->id)
            ->where(function ($query) use ($communityId, $category) {
                $query->where('community_id', $communityId);
                if ($category) {
                    $query->orWhereHas('community', fn ($q) => $q->where('category', $category));
                }
            })
            ->orderBy('likes_count', 'desc')
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * 4. Ekstrak Profil & Afinitas Minat Pengguna (User Profiling)
     */
    public function buildUserContext(User $user): array
    {
        // A. ID Komunitas yang Diikuti / Dimiliki
        $myMemberships = $user->communityMemberships()
            ->where('status', 'active')
            ->pluck('community_id')
            ->toArray();

        $ownedCommunities = Community::where('user_id', $user->id)->pluck('id')->toArray();
        $allMyCommunityIds = array_values(array_unique(array_merge($myMemberships, $ownedCommunities)));

        // B. ID Penulis yang Diikuti (Social Network)
        $followedAuthorIds = $user->following()->pluck('following_id')->toArray();

        // C. ID Penulis yang Pernah Berinteraksi (Likes & Komentar)
        $likedPostAuthorIds = CommunityPostLike::where('community_post_likes.user_id', $user->id)
            ->join('community_posts', 'community_post_likes.community_post_id', '=', 'community_posts.id')
            ->pluck('community_posts.user_id')
            ->toArray();

        $commentedPostAuthorIds = CommunityPostComment::where('community_post_comments.user_id', $user->id)
            ->join('community_posts', 'community_post_comments.community_post_id', '=', 'community_posts.id')
            ->pluck('community_posts.user_id')
            ->toArray();

        $interactedAuthorIds = array_values(array_unique(array_merge($likedPostAuthorIds, $commentedPostAuthorIds)));

        // D. Vektor Afinitas Kategori Minat Pengguna
        $categoryAffinities = $this->calculateUserCategoryAffinities($user, $allMyCommunityIds);

        return [
            'my_community_ids' => $allMyCommunityIds,
            'followed_author_ids' => $followedAuthorIds,
            'interacted_author_ids' => $interactedAuthorIds,
            'category_affinities' => $categoryAffinities,
        ];
    }

    /**
     * Hitung preferensi kategori pengguna berdasarkan histori keanggotaan dan interaksi
     */
    public function calculateUserCategoryAffinities(User $user, ?array $communityIds = null): array
    {
        $weights = [];

        // 1. Sinyal Keanggotaan Komunitas (Bobot = 3.0)
        $joinedCategories = Community::whereIn('id', $communityIds ?? [])
            ->pluck('category')
            ->filter();

        foreach ($joinedCategories as $cat) {
            $c = strtolower($cat);
            $weights[$c] = ($weights[$c] ?? 0.0) + 3.0;
        }

        // 2. Sinyal Like Postingan (Bobot = 1.0)
        $likedCategories = CommunityPostLike::where('community_post_likes.user_id', $user->id)
            ->join('community_posts', 'community_post_likes.community_post_id', '=', 'community_posts.id')
            ->join('communities', 'community_posts.community_id', '=', 'communities.id')
            ->pluck('communities.category')
            ->filter();

        foreach ($likedCategories as $cat) {
            $c = strtolower($cat);
            $weights[$c] = ($weights[$c] ?? 0.0) + 1.0;
        }

        // 3. Sinyal Komentar Postingan (Bobot = 1.5)
        $commentedCategories = CommunityPostComment::where('community_post_comments.user_id', $user->id)
            ->join('community_posts', 'community_post_comments.community_post_id', '=', 'community_posts.id')
            ->join('communities', 'community_posts.community_id', '=', 'communities.id')
            ->pluck('communities.category')
            ->filter();

        foreach ($commentedCategories as $cat) {
            $c = strtolower($cat);
            $weights[$c] = ($weights[$c] ?? 0.0) + 1.5;
        }

        // Normalisasi Skor ke rentang [0.0 - 1.0]
        if (empty($weights)) {
            return [];
        }

        $maxWeight = max($weights);
        $normalized = [];
        foreach ($weights as $cat => $val) {
            $normalized[$cat] = $maxWeight > 0 ? round($val / $maxWeight, 2) : 0.0;
        }

        return $normalized;
    }

    /**
     * Berikan penjelasan alasan rekomendasi yang mudah dipahami manusia
     */
    protected function determineRecommendationReason(
        bool $isMember,
        float $affinityRatio,
        string $category,
        bool $isFollowedAuthor,
        float $commentsScore,
        float $likesScore,
        float $hoursOld
    ): string {
        if ($isMember) {
            return 'Komunitas Anda';
        }

        if ($isFollowedAuthor) {
            return 'Penulis Diikuti';
        }

        if ($affinityRatio > 0.3 && ! empty($category)) {
            return 'Minat '.ucfirst($category);
        }

        if ($commentsScore >= 7.0) {
            return 'Diskusi Hangat';
        }

        if ($likesScore >= 6.0) {
            return 'Sedang Tren';
        }

        if ($hoursOld <= 12.0) {
            return 'Terbaru';
        }

        return 'Populer di Komunitas';
    }
}
