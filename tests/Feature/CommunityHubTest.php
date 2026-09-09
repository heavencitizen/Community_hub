<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Community;
use App\Models\CommunityMember;
use App\Models\CommunityPost;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityHubTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $eoUser;

    protected User $normalUser;

    protected Community $community;

    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        $this->eoUser = User::create([
            'name' => 'EO Admin',
            'email' => 'eo@test.com',
            'password' => bcrypt('password'),
            'role' => 'community_admin',
        ]);

        $this->normalUser = User::create([
            'name' => 'Normal User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $this->community = Community::create([
            'user_id' => $this->eoUser->id,
            'name' => 'Laravel Indonesia',
            'slug' => 'laravel-indonesia',
            'description' => 'Komunitas pengembang Laravel',
            'status' => 'active',
        ]);

        CommunityMember::create([
            'community_id' => $this->community->id,
            'user_id' => $this->normalUser->id,
            'joined_at' => now(),
        ]);

        $this->event = Event::create([
            'community_id' => $this->community->id,
            'title' => 'Laravel Meetup 2026',
            'slug' => 'laravel-meetup-2026',
            'description' => 'Diskusi seputar ekosistem Laravel',
            'location' => 'Jakarta',
            'event_date' => now()->addDays(5),
            'price' => 50000,
            'admin_fee' => 3000,
            'quota' => 50,
        ]);
    }

    public function test_dashboard_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('CommunityHub');
    }

    public function test_events_catalog_and_detail_are_accessible(): void
    {
        $response = $this->get('/events');
        $response->assertStatus(200);
        $response->assertSee('Laravel Meetup 2026');

        $detailResponse = $this->get('/events/'.$this->event->slug);
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Laravel Meetup 2026');
    }

    public function test_paid_ticket_purchase_sets_status_to_pending(): void
    {
        $response = $this->actingAs($this->normalUser)->post('/events/'.$this->event->id.'/buy-ticket', []);
        $response->assertRedirectContains('/payment/checkout/');

        $this->assertDatabaseHas('tickets', [
            'user_id' => $this->normalUser->id,
            'event_id' => $this->event->id,
            'status' => 'pending',
        ]);
    }

    public function test_free_ticket_purchase_sets_status_to_approved_automatically(): void
    {
        $freeEvent = Event::create([
            'community_id' => $this->community->id,
            'title' => 'Free Community Meetup',
            'slug' => 'free-community-meetup',
            'location' => 'Online',
            'event_date' => now()->addDays(2),
            'price' => 0,
            'admin_fee' => 0,
            'quota' => 100,
        ]);

        $response = $this->actingAs($this->normalUser)->post('/events/'.$freeEvent->id.'/buy-ticket', []);

        $ticket = Ticket::where('user_id', $this->normalUser->id)->where('event_id', $freeEvent->id)->first();
        $this->assertNotNull($ticket);
        $this->assertEquals('approved', $ticket->status);
    }

    public function test_super_admin_can_approve_ticket(): void
    {
        $ticket = Ticket::create([
            'user_id' => $this->normalUser->id,
            'event_id' => $this->event->id,
            'total_price' => 53000,
            'status' => 'pending',
            'is_scanned' => false,
        ]);

        $response = $this->actingAs($this->superAdmin)->post('/admin/tickets/'.$ticket->id.'/approve');
        $response->assertSessionHas('success');

        $this->assertEquals('approved', $ticket->fresh()->status);
    }

    public function test_community_member_can_post_on_forum(): void
    {
        $response = $this->actingAs($this->normalUser)->post('/communities/'.$this->community->id.'/posts', [
            'content' => 'Halo kawan-kawan komunitas!',
            'type' => 'text',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('community_posts', [
            'community_id' => $this->community->id,
            'user_id' => $this->normalUser->id,
            'content' => 'Halo kawan-kawan komunitas!',
        ]);
    }

    public function test_article_moderation_flow(): void
    {
        $article = Article::create([
            'user_id' => $this->eoUser->id,
            'community_id' => $this->community->id,
            'title' => 'Review Fitur Baru Framework',
            'slug' => 'review-fitur-baru-framework',
            'content' => 'Konten pembahasan fitur terkini...',
            'category' => 'trend',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->superAdmin)->post('/admin/moderation/articles/'.$article->id.'/approve');
        $response->assertSessionHas('success');

        $this->assertEquals('published', $article->fresh()->status);
        $this->assertNotNull($article->fresh()->published_at);
    }

    public function test_user_can_like_and_unlike_post_via_ajax(): void
    {
        $post = CommunityPost::create([
            'community_id' => $this->community->id,
            'user_id' => $this->eoUser->id,
            'content' => 'Postingan untuk pengujian like',
            'type' => 'text',
        ]);

        // 1. Like
        $responseLike = $this->actingAs($this->normalUser)
            ->postJson(route('communities.posts.like', $post->id));

        $responseLike->assertOk();
        $responseLike->assertJson([
            'success' => true,
            'liked' => true,
            'likes_count' => 1,
        ]);
        $this->assertEquals(1, $post->fresh()->likes_count);
        $this->assertTrue($post->fresh()->isLikedBy($this->normalUser->id));

        // 2. Unlike
        $responseUnlike = $this->actingAs($this->normalUser)
            ->postJson(route('communities.posts.like', $post->id));

        $responseUnlike->assertOk();
        $responseUnlike->assertJson([
            'success' => true,
            'liked' => false,
            'likes_count' => 0,
        ]);
        $this->assertEquals(0, $post->fresh()->likes_count);
    }

    public function test_user_can_comment_on_post(): void
    {
        $post = CommunityPost::create([
            'community_id' => $this->community->id,
            'user_id' => $this->eoUser->id,
            'content' => 'Postingan untuk pengujian komentar',
            'type' => 'text',
        ]);

        $response = $this->actingAs($this->normalUser)
            ->post(route('communities.posts.comment', $post->id), [
                'comment' => 'Komentar pengujian dari normal user',
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('community_post_comments', [
            'community_post_id' => $post->id,
            'user_id' => $this->normalUser->id,
            'comment' => 'Komentar pengujian dari normal user',
        ]);
    }

    public function test_author_can_delete_own_post(): void
    {
        $post = CommunityPost::create([
            'community_id' => $this->community->id,
            'user_id' => $this->normalUser->id,
            'content' => 'Postingan yang akan dihapus',
            'type' => 'text',
        ]);

        $response = $this->actingAs($this->normalUser)
            ->delete(route('communities.posts.destroy', $post->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('community_posts', [
            'id' => $post->id,
        ]);
    }
}
