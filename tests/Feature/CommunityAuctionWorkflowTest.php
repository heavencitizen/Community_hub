<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\CommunityAuction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityAuctionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $leader;

    protected User $bidder1;

    protected User $bidder2;

    protected Community $community;

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
    }

    public function test_cannot_bid_on_scheduled_auction_before_start_time(): void
    {
        $scheduledAuction = CommunityAuction::create([
            'community_id' => $this->community->id,
            'user_id' => $this->leader->id,
            'title' => 'Helm Klasik Terjadwal',
            'slug' => 'helm-klasik-terjadwal',
            'starting_price' => 100000,
            'bid_increment' => 10000,
            'current_price' => 100000,
            'start_time' => now()->addHours(5),
            'end_time' => now()->addDays(2),
            'status' => 'active',
        ]);

        $this->assertTrue($scheduledAuction->isScheduled());
        $this->assertFalse($scheduledAuction->isActive());

        $this->actingAs($this->bidder1);
        $response = $this->post(route('communities.auctions.bid', $scheduledAuction->id), [
            'bid_amount' => 110000,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('belum dimulai', session('error'));
        $this->assertEquals(0, $scheduledAuction->bids()->count());
    }

    public function test_anti_sniping_extends_end_time_when_bid_placed_in_last_2_minutes(): void
    {
        $auction = CommunityAuction::create([
            'community_id' => $this->community->id,
            'user_id' => $this->leader->id,
            'title' => 'Vespa Diecast Anti Sniping',
            'slug' => 'vespa-diecast-anti-sniping',
            'starting_price' => 100000,
            'bid_increment' => 10000,
            'current_price' => 100000,
            'start_time' => now()->subHours(2),
            'end_time' => now()->addSeconds(60), // Kurang dari 120 detik tersisa
            'anti_sniping' => true,
            'status' => 'active',
        ]);

        $initialEndTime = $auction->end_time->copy();

        $this->actingAs($this->bidder1);
        $response = $this->post(route('communities.auctions.bid', $auction->id), [
            'bid_amount' => 120000,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertStringContainsString('Anti-Sniping', session('success'));

        $auction->refresh();
        // Waktu harus bertambah +2 menit (120 detik)
        $this->assertTrue($auction->end_time->greaterThan($initialEndTime));
        $this->assertEquals(120, abs($auction->end_time->diffInSeconds($initialEndTime)));
    }

    public function test_displaced_bidder_is_recorded_as_runner_up(): void
    {
        $auction = CommunityAuction::create([
            'community_id' => $this->community->id,
            'user_id' => $this->leader->id,
            'title' => 'Karburator Original NOS',
            'slug' => 'karburator-original-nos',
            'starting_price' => 300000,
            'bid_increment' => 20000,
            'current_price' => 300000,
            'start_time' => now()->subHour(),
            'end_time' => now()->addDay(),
            'status' => 'active',
        ]);

        // Bidder 1 menawar pertama kali
        $this->actingAs($this->bidder1);
        $this->post(route('communities.auctions.bid', $auction->id), [
            'bid_amount' => 320000,
        ]);

        $auction->refresh();
        $this->assertEquals($this->bidder1->id, $auction->winner_id);
        $this->assertNull($auction->runner_up_id);

        // Bidder 2 menawar lebih tinggi
        $this->actingAs($this->bidder2);
        $this->post(route('communities.auctions.bid', $auction->id), [
            'bid_amount' => 350000,
        ]);

        $auction->refresh();
        // Bidder 2 memimpin, Bidder 1 tercatat sebagai runner-up
        $this->assertEquals($this->bidder2->id, $auction->winner_id);
        $this->assertEquals($this->bidder1->id, $auction->runner_up_id);
        $this->assertEquals(320000, (float) $auction->runner_up_bid);
    }

    public function test_auction_closure_sets_payment_deadline_48_hours(): void
    {
        $auction = CommunityAuction::create([
            'community_id' => $this->community->id,
            'user_id' => $this->leader->id,
            'title' => 'Spion Bulat Classic',
            'slug' => 'spion-bulat-classic',
            'starting_price' => 150000,
            'bid_increment' => 10000,
            'current_price' => 150000,
            'start_time' => now()->subHour(),
            'end_time' => now()->addDay(),
            'status' => 'active',
        ]);

        $this->actingAs($this->bidder1);
        $this->post(route('communities.auctions.bid', $auction->id), [
            'bid_amount' => 160000,
        ]);

        // Ketua menutup lelang
        $this->actingAs($this->leader);
        $response = $this->post(route('communities.auctions.close', $auction->id));

        $response->assertRedirect();
        $auction->refresh();

        $this->assertEquals('closed', $auction->status);
        $this->assertNotNull($auction->payment_deadline);
        $this->assertTrue($auction->isAwaitingPayment());
        // Batas bayar mendekati 48 jam ke depan
        $this->assertGreaterThan(47, now()->diffInHours($auction->payment_deadline, false));
    }

    public function test_declaring_wanprestasi_promotes_runner_up_with_new_deadline(): void
    {
        $auction = CommunityAuction::create([
            'community_id' => $this->community->id,
            'user_id' => $this->leader->id,
            'title' => 'Velg Tubeless BGM',
            'slug' => 'velg-tubeless-bgm',
            'starting_price' => 500000,
            'bid_increment' => 50000,
            'current_price' => 600000,
            'start_time' => now()->subDays(2),
            'end_time' => now()->subHour(),
            'status' => 'closed',
            'winner_id' => $this->bidder1->id,
            'runner_up_id' => $this->bidder2->id,
            'runner_up_bid' => 550000,
            'payment_deadline' => now()->subMinute(), // Batas waktu pelunasan sudah lewat
        ]);

        $this->actingAs($this->leader);
        $response = $this->post(route('communities.auctions.wanprestasi', $auction->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $auction->refresh();

        // Verifikasi Bidder 2 dipromosikan menggantikan Bidder 1
        $this->assertEquals($this->bidder2->id, $auction->winner_id);
        $this->assertEquals(550000, (float) $auction->current_price);
        $this->assertNull($auction->runner_up_id);
        $this->assertNotNull($auction->wanprestasi_at);
        $this->assertTrue($auction->payment_deadline->isFuture());

        // Verifikasi notifikasi promosi dikirim ke Bidder 2
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $this->bidder2->id,
            'type' => 'auction_runner_up_promoted',
        ]);
    }

    public function test_certificate_page_renders_with_valid_qr_and_auction_details(): void
    {
        $auction = CommunityAuction::create([
            'community_id' => $this->community->id,
            'user_id' => $this->leader->id,
            'title' => 'Jaket Kulit Touring Vintage',
            'slug' => 'jaket-kulit-touring-vintage',
            'starting_price' => 400000,
            'bid_increment' => 25000,
            'current_price' => 450000,
            'start_time' => now()->subDays(2),
            'end_time' => now()->subDay(),
            'status' => 'completed',
            'winner_id' => $this->bidder1->id,
        ]);

        $response = $this->get(route('communities.auctions.certificate', $auction->id));

        $response->assertStatus(200);
        $response->assertSee('KUTIPAN HASIL LELANG COMMUNITYHUB');
        $response->assertSee($auction->auction_code);
        $response->assertSee('Jaket Kulit Touring Vintage');
        $response->assertSee($this->bidder1->name);
        $response->assertSee('450.000');
    }
}
