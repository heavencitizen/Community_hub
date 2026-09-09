<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Community;
use App\Models\CommunityAuction;
use App\Models\CommunityPost;
use App\Models\CommunityProduct;
use App\Models\Donation;
use App\Models\Event;
use App\Models\User;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected RecommendationService $recommendationService
    ) {}

    /**
     * Halaman Beranda Interaktif untuk Pengguna Terautentikasi (Social Media Community Feed)
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Dapatkan daftar ID komunitas yang diikuti atau dimiliki pengguna
        $myMembershipIds = $user->communityMemberships()
            ->where('status', 'active')
            ->pluck('community_id')
            ->toArray();

        $ownedCommunityIds = Community::where('user_id', $user->id)->pluck('id')->toArray();
        $allMyCommunityIds = array_unique(array_merge($myMembershipIds, $ownedCommunityIds));

        // 2. Komunitas yang saya ikuti (untuk sidebar & opsi composer)
        $myCommunities = Community::whereIn('id', $allMyCommunityIds)
            ->withCount('members')
            ->get();

        // 3. Filter Feed Sosial Media ('personalized' = default Untuk Anda, 'following' = komunitas yang diikuti, 'all' = semua kronologis)
        $feedFilter = $request->query('filter', 'personalized');

        if ($feedFilter === 'following') {
            $postsQuery = CommunityPost::with([
                'author',
                'community',
                'comments.user',
                'likes.user',
            ])->latest();

            if (! empty($allMyCommunityIds)) {
                $postsQuery->whereIn('community_id', $allMyCommunityIds);
            } else {
                $postsQuery->whereIn('community_id', [-1]); // Kosong jika belum ikut komunitas
            }

            $posts = $postsQuery->paginate(10)->withQueryString();
        } elseif ($feedFilter === 'all') {
            $posts = CommunityPost::with([
                'author',
                'community',
                'comments.user',
                'likes.user',
            ])->latest()->paginate(10)->withQueryString();
        } else {
            // Default: Algoritma Rekomendasi Personal
            $posts = $this->recommendationService->getPersonalizedFeed($user, 10);
        }

        // 4. Tiket Event Saya yang Akan Datang
        $myTickets = $user->tickets()
            ->with('event')
            ->whereHas('event', function ($query) {
                $query->where('event_date', '>=', now());
            })
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();

        // 5. Penawaran Lelang Terakhir Saya
        $myBids = $user->auctionBids()
            ->with('auction.community')
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();

        // 6. Rekomendasi Event & Komunitas Berdasarkan Preferensi
        $suggestedEvents = Event::with('community')
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->take(3)
            ->get();
        $suggestedCommunities = Community::whereNotIn('id', $allMyCommunityIds)
            ->where('status', 'active')
            ->withCount('members')
            ->orderBy('members_count', 'desc')
            ->take(3)
            ->get();

        // 7. Donasi Amal Pilihan
        $featuredDonations = Donation::with('community')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();

        // 8. Berita & Artikel Terbaru Komunitas
        $recentArticles = Article::with(['author', 'community'])
            ->published()
            ->latest('published_at')
            ->take(3)
            ->get();

        // 9. Rekomendasi Pertemanan (Social Recommendation)
        $friendRecommendations = $this->recommendationService->getSocialRecommendations($user, 4);

        // 10. Postingan Terkait Minat Pengguna (Related Content Recommendation)
        $relatedPosts = $this->recommendationService->getRelatedPostsForUser($user, 3);

        return view('dashboard', compact(
            'user',
            'myCommunities',
            'feedFilter',
            'posts',
            'myTickets',
            'myBids',
            'suggestedEvents',
            'suggestedCommunities',
            'featuredDonations',
            'recentArticles',
            'friendRecommendations',
            'relatedPosts'
        ));
    }

    /**
     * Halaman Landing / Beranda Tamu (Non-User / Guest)
     */
    public function welcome()
    {
        // Jika sudah login, langsung alihkan ke social feed dashboard
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        $upcomingEvents = Event::with('community')
            ->orderByRaw('CASE WHEN event_date >= ? THEN 0 ELSE 1 END', [now()])
            ->orderBy('event_date', 'asc')
            ->take(8)
            ->get();

        $activeCommunities = Community::withCount('members')
            ->where('status', 'active')
            ->orderBy('members_count', 'desc')
            ->take(8)
            ->get();

        $featuredArticles = Article::with(['author', 'community'])
            ->published()
            ->orderBy('published_at', 'desc')
            ->take(6)
            ->get();

        $featuredUsers = User::withCount(['communityPosts', 'followers'])
            ->whereNotNull('username')
            ->orderBy('community_posts_count', 'desc')
            ->take(8)
            ->get();

        $featuredDonations = Donation::with('community')
            ->where('status', 'active')
            ->orderBy('collected_amount', 'desc')
            ->take(6)
            ->get();

        $featuredAuctions = CommunityAuction::with(['community', 'bids'])
            ->whereIn('status', ['active', 'scheduled', 'closed'])
            ->orderByRaw("CASE WHEN status = 'active' THEN 1 WHEN status = 'scheduled' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $featuredProducts = CommunityProduct::with(['community', 'seller'])
            ->where('status', 'available')
            ->latest()
            ->take(8)
            ->get();

        $stats = [
            'total_communities' => Community::where('status', 'active')->count(),
            'total_events' => Event::count(),
            'total_articles' => Article::published()->count(),
            'total_users' => User::count(),
            'total_auctions' => CommunityAuction::count(),
            'total_products' => CommunityProduct::where('status', 'available')->count(),
        ];

        return view('welcome', compact(
            'upcomingEvents',
            'activeCommunities',
            'featuredArticles',
            'featuredUsers',
            'featuredDonations',
            'featuredAuctions',
            'featuredProducts',
            'stats'
        ));
    }
}
