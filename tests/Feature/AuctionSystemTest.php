<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\CommunityAuction;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuctionSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $leader;

    protected User $bidder1;

    protected User $bidder2;

    protected Community $community;

    protected CommunityAuction $auction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->leader = User::factory()->create(['name' => 'Ketua Vespa', 'role' => 'user']);
        $this->bidder1 = User::factory()->create(['name' => 'Penawar Satu', 'role' => 'user']);
        $this->bidder2 = User::factory()->create(['name' => 'Penawar Dua', 'role' => 'user']);

        $this->community = Community::create([
            'user_id' => $this->leader->id,
            'name' => 'Komunitas Vespa Padang',
            'slug' => 'komunitas-vespa-padang',
            'description' => 'Komunitas pecinta skuter klasik vespa',
            'category' => 'otomotif',
            'status' => 'active',
        ]);

        $this->auction = CommunityAuction::create([
            'community_id' => $this->community->id,
            'user_id' => $this->leader->id,
            'title' => 'Helm Retro Vintage Classic NOS',
            'slug' => 'helm-retro-vintage-classic-nos',
            'description' => 'Helm retro edisi langka',
            'starting_price' => 200000,
            'bid_increment' => 25000,
            'current_price' => 200000,
            'start_time' => now(),
            'end_time' => now()->addDays(2),
            'status' => 'active',
        ]);
    }

    public function test_public_user_can_access_auctions_catalog_page(): void
    {
        $response = $this->get(route('auctions.index'));

        $response->assertStatus(200);
        $response->assertSee('Arena Lelang Komunitas');
        $response->assertSee('Helm Retro Vintage Classic NOS');
        $response->assertSee('LIVE LELANG');
    }

    public function test_user_can_place_bid_on_active_auction_without_bad_method_call_exception(): void
    {
        $this->actingAs($this->bidder1);

        $response = $this->post(route('communities.auctions.bid', $this->auction->id), [
            'bid_amount' => 225000,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->auction->refresh();
        $this->assertEquals(225000, (float) $this->auction->current_price);
        $this->assertEquals($this->bidder1->id, $this->auction->winner_id);
        $this->assertDatabaseHas('auction_bids', [
            'auction_id' => $this->auction->id,
            'user_id' => $this->bidder1->id,
            'bid_amount' => 225000,
        ]);
    }

    public function test_owner_cannot_bid_on_own_auction(): void
    {
        $this->actingAs($this->leader);

        $response = $this->post(route('communities.auctions.bid', $this->auction->id), [
            'bid_amount' => 225000,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda adalah penyelenggara lelang ini, tidak dapat menawar barang sendiri.');
    }

    public function test_highest_bidder_cannot_re_bid_immediately(): void
    {
        $this->actingAs($this->bidder1);
        $this->post(route('communities.auctions.bid', $this->auction->id), [
            'bid_amount' => 225000,
        ]);

        // Bidder 1 mencoba menawar lagi padahal dia sudah memimpin
        $response = $this->post(route('communities.auctions.bid', $this->auction->id), [
            'bid_amount' => 250000,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda sudah memegang tawaran tertinggi saat ini.');
    }

    public function test_outbid_notification_sent_when_another_user_places_higher_bid(): void
    {
        // Bidder 1 menawar pertama kali
        $this->actingAs($this->bidder1);
        $this->post(route('communities.auctions.bid', $this->auction->id), [
            'bid_amount' => 225000,
        ]);

        // Bidder 2 menawar lebih tinggi
        $this->actingAs($this->bidder2);
        $this->post(route('communities.auctions.bid', $this->auction->id), [
            'bid_amount' => 250000,
        ]);

        // Verifikasi bidder 1 menerima notifikasi outbid
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $this->bidder1->id,
            'sender_id' => $this->bidder2->id,
            'type' => 'auction_outbid',
        ]);
    }

    public function test_leader_can_close_auction_and_winner_receives_won_notification(): void
    {
        // Bidder 1 menawar
        $this->actingAs($this->bidder1);
        $this->post(route('communities.auctions.bid', $this->auction->id), [
            'bid_amount' => 250000,
        ]);

        // Ketua menutup sesi lelang
        $this->actingAs($this->leader);
        $response = $this->post(route('communities.auctions.close', $this->auction->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->auction->refresh();
        $this->assertEquals('closed', $this->auction->status);
        $this->assertEquals($this->bidder1->id, $this->auction->winner_id);
        $this->assertTrue($this->auction->isEnded());
        $this->assertTrue($this->auction->isAwaitingPayment());

        // Verifikasi notifikasi kemenangan ke Bidder 1
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $this->bidder1->id,
            'type' => 'auction_won',
        ]);
    }

    public function test_winner_can_checkout_to_payment_gateway(): void
    {
        // Tetapkan lelang closed dengan pemenang bidder 1
        $this->auction->update([
            'status' => 'closed',
            'current_price' => 250000,
            'winner_id' => $this->bidder1->id,
        ]);

        $this->actingAs($this->bidder1);
        $response = $this->post(route('communities.auctions.checkout', $this->auction->id));

        $response->assertRedirect();
        $transaction = Transaction::where('type', 'auction')->where('reference_id', $this->auction->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals($this->bidder1->id, $transaction->user_id);
        $this->assertEquals(250000, (float) $transaction->amount);
    }

    public function test_is_ended_and_auto_finalize_handles_expired_auctions(): void
    {
        // Buat lelang yang end_time sudah kemarin
        $expiredAuction = CommunityAuction::create([
            'community_id' => $this->community->id,
            'user_id' => $this->leader->id,
            'title' => 'Barang Kedaluwarsa',
            'slug' => 'barang-kedaluwarsa',
            'starting_price' => 100000,
            'bid_increment' => 10000,
            'current_price' => 100000,
            'start_time' => now()->subDays(5),
            'end_time' => now()->subDay(),
            'status' => 'active',
            'winner_id' => $this->bidder1->id,
        ]);

        $this->assertTrue($expiredAuction->isEnded());
        $this->assertFalse($expiredAuction->isActive());

        // Jalankan autoFinalizeIfNeeded
        $expiredAuction->autoFinalizeIfNeeded();
        $expiredAuction->refresh();

        $this->assertEquals('closed', $expiredAuction->status);
    }
}
