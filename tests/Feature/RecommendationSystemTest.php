<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\CommunityMember;
use App\Models\CommunityPost;
use App\Models\User;
use App\Services\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecommendationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $userA;

    protected User $userB;

    protected User $userC;

    protected Community $communitySports;

    protected Community $communityTech;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userA = User::create([
            'name' => 'User A (Olahraga)',
            'email' => 'usera@test.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $this->userB = User::create([
            'name' => 'User B (Teman Olahraga)',
            'email' => 'userb@test.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $this->userC = User::create([
            'name' => 'User C (Teknologi)',
            'email' => 'userc@test.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $this->communitySports = Community::create([
            'user_id' => $this->userB->id,
            'name' => 'Padang Trail Runners',
            'slug' => 'padang-trail-runners',
            'category' => 'olahraga',
            'description' => 'Komunitas pelari lintas alam',
            'status' => 'active',
        ]);

        $this->communityTech = Community::create([
            'user_id' => $this->userC->id,
            'name' => 'Startup Kito Minang',
            'slug' => 'startup-kito-minang',
            'category' => 'teknologi',
            'description' => 'Komunitas startup & teknologi',
            'status' => 'active',
        ]);

        // User A bergabung ke komunitas olahraga bersama User B
        CommunityMember::create([
            'community_id' => $this->communitySports->id,
            'user_id' => $this->userA->id,
            'status' => 'active',
            'joined_at' => now(),
        ]);

        CommunityMember::create([
            'community_id' => $this->communitySports->id,
            'user_id' => $this->userB->id,
            'status' => 'active',
            'joined_at' => now(),
        ]);

        // User C bergabung ke komunitas teknologi
        CommunityMember::create([
            'community_id' => $this->communityTech->id,
            'user_id' => $this->userC->id,
            'status' => 'active',
            'joined_at' => now(),
        ]);
    }

    public function test_dashboard_loads_with_personalized_feed_by_default(): void
    {
        $response = $this->actingAs($this->userA)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Untuk Anda');
        $response->assertSee('Komunitas Saya');
        $response->assertSee('Semua');
        $response->assertSee('Saran Teman Komunitas');
    }

    public function test_personalized_feed_ranks_affiliated_content_higher(): void
    {
        // Buat postingan di komunitas Olahraga (diikuti User A)
        $sportsPost = CommunityPost::create([
            'community_id' => $this->communitySports->id,
            'user_id' => $this->userB->id,
            'content' => 'Latihan lari pagi di Gunung Singgalang!',
            'type' => 'text',
            'likes_count' => 1,
            'created_at' => now(),
        ]);

        // Buat postingan di komunitas Tech (TIDAK diikuti User A)
        $techPost = CommunityPost::create([
            'community_id' => $this->communityTech->id,
            'user_id' => $this->userC->id,
            'content' => 'Tips arsitektur cloud server scalable',
            'type' => 'text',
            'likes_count' => 1,
            'created_at' => now(),
        ]);

        /** @var RecommendationService $service */
        $service = app(RecommendationService::class);
        $feed = $service->getPersonalizedFeed($this->userA, 10);

        $this->assertNotEmpty($feed);
        $firstItem = $feed->first();
        $lastItem = $feed->last();

        // Postingan olahraga harus berada di urutan pertama karena bonus keanggotaan & afinitas kategori
        $this->assertEquals($sportsPost->id, $firstItem->id);
        $this->assertEquals($techPost->id, $lastItem->id);
        $this->assertGreaterThan($lastItem->recommendation_score, $firstItem->recommendation_score);
        $this->assertEquals('Komunitas Anda', $firstItem->recommendation_reason);
    }

    public function test_social_recommendation_suggests_relevant_users(): void
    {
        /** @var RecommendationService $service */
        $service = app(RecommendationService::class);
        $recommendations = $service->getSocialRecommendations($this->userA, 5);

        // User B berbagi komunitas olahraga dengan User A, maka harus direkomendasikan
        $this->assertTrue($recommendations->contains('id', $this->userB->id));
        $bCandidate = $recommendations->firstWhere('id', $this->userB->id);
        $this->assertStringContainsString('Komunitas Bersama', $bCandidate->recommendation_reason);

        // Diri sendiri tidak boleh masuk dalam rekomendasi
        $this->assertFalse($recommendations->contains('id', $this->userA->id));
    }

    public function test_user_can_follow_and_unfollow_another_user(): void
    {
        // 1. Follow User B
        $response = $this->actingAs($this->userA)->postJson(route('users.follow', $this->userB->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'following' => true,
        ]);

        $this->assertTrue($this->userA->isFollowing($this->userB->id));

        // 2. Unfollow User B
        $responseUnfollow = $this->actingAs($this->userA)->postJson(route('users.follow', $this->userB->id));

        $responseUnfollow->assertStatus(200);
        $responseUnfollow->assertJson([
            'success' => true,
            'following' => false,
        ]);

        $this->assertFalse($this->userA->fresh()->isFollowing($this->userB->id));
    }

    public function test_cannot_follow_self(): void
    {
        $response = $this->actingAs($this->userA)->postJson(route('users.follow', $this->userA->id));

        $response->assertStatus(422);
        $this->assertFalse($this->userA->isFollowing($this->userA->id));
    }

    public function test_dashboard_renders_usernames_and_handles_as_evaluated_text_not_raw_blade_syntax(): void
    {
        CommunityPost::create([
            'community_id' => $this->communitySports->id,
            'user_id' => $this->userB->id,
            'content' => 'Postingan pengetesan tampilan username',
            'type' => 'text',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->userA)->get(route('dashboard'));

        $response->assertOk();

        // Pastikan tidak ada sintaks Blade mentah yang bocor ke output HTML
        $response->assertDontSee('{{ $post->author->username }}', false);
        $response->assertDontSee('{{ $friend->username }}', false);
        $response->assertDontSee('{{ $user->username }}', false);

        // Pastikan nilai sebenarnya tampil dengan benar
        $response->assertSee('@'.$this->userB->username);
        $response->assertSee('@'.$this->userA->username);
        $response->assertSee(route('users.show', $this->userB->username));
        $response->assertSee(route('users.show', $this->userA->username));
    }
}
