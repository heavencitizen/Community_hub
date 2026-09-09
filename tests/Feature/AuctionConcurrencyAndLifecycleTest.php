<?php

namespace Tests\Feature;

use App\Models\AuctionBid;
use App\Models\Community;
use App\Models\CommunityAuction;
use App\Models\CommunityPost;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuctionConcurrencyAndLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected User $leader;

    protected User $bidder1;

    protected User $bidder2;

    protected Community $community;

    protected function setUp(): void
    {
        parent::setUp();

        $this->leader = User::factory()->create(['name' => 'Ketua Komunitas', 'role' => 'user']);
        $this->bidder1 = User::factory()->create(['name' => 'Penawar Satu', 'role' => 'user']);
        $this->bidder2 = User::factory()->create(['name' => 'Penawar Dua', 'role' => 'user']);

        $this->community = Community::create([
            'user_id' => $this->leader->id,
            'name' => 'Komunitas Vespa Padang',
            'slug' => 'komunitas-vespa-padang',
            'description' => 'Komunitas skuter vespa',
            'category' => 'otomotif',
            'status' => 'active',
        ]);
    }

    public function test_bidding_with_pessimistic_locking_updates_auction_and_records_bid(): void
    {
        $auction = CommunityAuction::create([
            'community_id' => $this->community->id,
            'user_id' => $this->leader->id,
            'title' => 'Helm Klasik Original',
            'slug' => 'helm-klasik-original',
            'starting_price' => 100000,
            'bid_increment' => 10000,
            'current_price' => 100000,
            'end_time' => now()->addDays(1),
            'status' => 'active',
        ]);

        $this->actingAs($this->bidder1);
        $response = $this->post(route('communities.auctions.bid', $auction->id), [
            'bid_amount' => 110000,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $auction->refresh();
        $this->assertEquals(110000, $auction->current_price);
        $this->assertEquals($this->bidder1->id, $auction->winner_id);
        $this->assertDatabaseHas('auction_bids', [
            'auction_id' => $auction->id,
            'user_id' => $this->bidder1->id,
            'bid_amount' => 110000,
        ]);

        // Penawar kedua mengajukan tawaran lebih tinggi
        $this->actingAs($this->bidder2);
        $response2 = $this->post(route('communities.auctions.bid', $auction->id), [
            'bid_amount' => 120000,
        ]);

        $response2->assertSessionHasNoErrors();
        $auction->refresh();
        $this->assertEquals(120000, $auction->current_price);
        $this->assertEquals($this->bidder2->id, $auction->winner_id);
        $this->assertEquals($this->bidder1->id, $auction->runner_up_id);
        $this->assertEquals(110000, $auction->runner_up_bid);
    }

    public function test_manage_auction_lifecycle_command_finalizes_expired_auctions(): void
    {
        $auction = CommunityAuction::create([
            'community_id' => $this->community->id,
            'user_id' => $this->leader->id,
            'title' => 'Barang Lelang Kadaluarsa',
            'slug' => 'barang-lelang-kadaluarsa',
            'starting_price' => 200000,
            'bid_increment' => 20000,
            'current_price' => 240000,
            'winner_id' => $this->bidder1->id,
            'end_time' => now()->subMinute(),
            'status' => 'active',
        ]);

        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id' => $this->bidder1->id,
            'bid_amount' => 240000,
        ]);

        $this->artisan('auctions:manage-lifecycle')
            ->assertSuccessful();

        $auction->refresh();
        $this->assertEquals('closed', $auction->status);
        $this->assertEquals($this->bidder1->id, $auction->winner_id);
        $this->assertNotNull($auction->payment_deadline);
    }

    public function test_manage_auction_lifecycle_command_handles_wanprestasi_and_promotes_runner_up(): void
    {
        $auction = CommunityAuction::create([
            'community_id' => $this->community->id,
            'user_id' => $this->leader->id,
            'title' => 'Koleksi Langka Wanprestasi',
            'slug' => 'koleksi-langka-wanprestasi',
            'starting_price' => 500000,
            'bid_increment' => 50000,
            'current_price' => 700000,
            'winner_id' => $this->bidder1->id,
            'runner_up_id' => $this->bidder2->id,
            'runner_up_bid' => 650000,
            'payment_deadline' => now()->subHour(),
            'end_time' => now()->subDays(3),
            'status' => 'closed',
        ]);

        $this->artisan('auctions:manage-lifecycle')
            ->assertSuccessful();

        $auction->refresh();
        $this->assertNotNull($auction->wanprestasi_at);
        // Hak dialihkan ke runner-up
        $this->assertEquals($this->bidder2->id, $auction->winner_id);
        $this->assertEquals(650000, $auction->current_price);
        $this->assertNull($auction->runner_up_id);
    }

    public function test_payment_checkout_and_process_requires_ownership_authorization(): void
    {
        $transaction = Transaction::create([
            'transaction_code' => 'TRX-SECURE-TEST-01',
            'user_id' => $this->bidder1->id,
            'type' => 'donation',
            'reference_id' => 1,
            'amount' => 50000,
            'platform_fee_percent' => 0,
            'platform_fee_amount' => 0,
            'total_amount' => 50000,
            'payment_method' => 'qris',
            'payment_status' => 'pending',
        ]);

        // User lain (bidder2) mencoba mengakses checkout transaksi bidder1 -> 403 Forbidden
        $this->actingAs($this->bidder2);
        $resForbidden = $this->get(route('payment.checkout', $transaction->transaction_code));
        $resForbidden->assertStatus(403);

        $resProcessForbidden = $this->post(route('payment.process', $transaction->transaction_code), [
            'payment_method' => 'bca_va',
        ]);
        $resProcessForbidden->assertStatus(403);

        // Pemilik asli (bidder1) dapat mengakses checkout
        $this->actingAs($this->bidder1);
        $resAllowed = $this->get(route('payment.checkout', $transaction->transaction_code));
        $resAllowed->assertStatus(200);

        // Super admin dapat mengakses checkout
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($superAdmin);
        $resAdmin = $this->get(route('payment.checkout', $transaction->transaction_code));
        $resAdmin->assertStatus(200);
    }

    public function test_user_unread_notifications_count_is_memoized(): void
    {
        $user = User::factory()->create();

        UserNotification::create([
            'user_id' => $user->id,
            'type' => 'test',
            'title' => 'Test Notif',
            'message' => 'Pesan',
            'is_read' => false,
        ]);

        $this->assertEquals(1, $user->unreadNotificationsCount());

        // Buat notifikasi baru langsung di DB tanpa refresh instance user
        UserNotification::create([
            'user_id' => $user->id,
            'type' => 'test2',
            'title' => 'Test Notif 2',
            'message' => 'Pesan 2',
            'is_read' => false,
        ]);

        // Nilai masih di-cache (1)
        $this->assertEquals(1, $user->unreadNotificationsCount());

        // Setelah clear cache, nilai terbarui menjadi 2
        $user->clearNotificationsCache();
        $this->assertEquals(2, $user->unreadNotificationsCount());
    }

    public function test_community_post_deleting_cleans_up_media_file(): void
    {
        Storage::fake('public');

        $fakeFilePath = 'forum_media/test_image.jpg';
        Storage::disk('public')->put($fakeFilePath, 'fake content');

        $this->assertTrue(Storage::disk('public')->exists($fakeFilePath));

        $post = CommunityPost::create([
            'community_id' => $this->community->id,
            'user_id' => $this->leader->id,
            'content' => 'Postingan uji media cleanup',
            'media_url' => $fakeFilePath,
            'type' => 'photo',
        ]);

        $post->delete();

        // Verifikasi berkas fisik otomatis dihapus oleh model deleting hook
        $this->assertFalse(Storage::disk('public')->exists($fakeFilePath));
    }

    public function test_auction_ticker_endpoint_returns_live_state_json(): void
    {
        $auction = CommunityAuction::create([
            'community_id' => $this->community->id,
            'user_id' => $this->leader->id,
            'title' => 'Jaket Vespa Vintage',
            'slug' => 'jaket-vespa-vintage',
            'starting_price' => 200000,
            'bid_increment' => 25000,
            'current_price' => 200000,
            'end_time' => now()->addHours(5),
            'status' => 'active',
        ]);

        $response = $this->get(route('auctions.ticker', $auction->id));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $auction->id,
            'status' => 'active',
            'is_live' => true,
            'is_ended' => false,
            'current_price' => 200000,
            'current_price_formatted' => 'Rp200.000',
            'min_next_bid' => 225000,
            'min_next_bid_formatted' => 'Rp225.000',
            'total_bids' => 0,
            'winner' => null,
        ]);

        // Place a bid and verify ticker reflects winner and updated price
        AuctionBid::create([
            'auction_id' => $auction->id,
            'user_id' => $this->bidder1->id,
            'bid_amount' => 250000,
        ]);
        $auction->update([
            'current_price' => 250000,
            'winner_id' => $this->bidder1->id,
        ]);

        $responseWithBid = $this->get(route('auctions.ticker', $auction->id));
        $responseWithBid->assertStatus(200);
        $responseWithBid->assertJson([
            'id' => $auction->id,
            'current_price' => 250000,
            'current_price_formatted' => 'Rp250.000',
            'min_next_bid' => 275000,
            'total_bids' => 1,
            'winner' => [
                'id' => $this->bidder1->id,
                'name' => 'Penawar Satu',
            ],
        ]);
    }
}
