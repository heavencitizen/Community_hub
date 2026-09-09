<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\CommunityAuction;
use App\Models\CommunityProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected User $seller;

    protected User $buyer;

    protected Community $community1;

    protected Community $community2;

    protected CommunityProduct $product1;

    protected CommunityProduct $product2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create(['name' => 'Penjual Komunitas', 'username' => 'sellerhub']);
        $this->buyer = User::factory()->create(['name' => 'Pembeli Antusias', 'username' => 'buyerhub']);

        $this->community1 = Community::create([
            'user_id' => $this->seller->id,
            'name' => 'Komunitas Vespa Padang',
            'slug' => 'komunitas-vespa-padang',
            'description' => 'Komunitas pecinta vespa',
            'category' => 'otomotif',
            'status' => 'active',
        ]);

        $this->community2 = Community::create([
            'user_id' => $this->seller->id,
            'name' => 'Klub Scuba Ranah Minang',
            'slug' => 'klub-scuba-ranah-minang',
            'description' => 'Klub penyelaman laut',
            'category' => 'kesehatan',
            'status' => 'active',
        ]);

        $this->product1 = CommunityProduct::create([
            'community_id' => $this->community1->id,
            'user_id' => $this->seller->id,
            'name' => 'Helm Bogo Retro Classic',
            'slug' => 'helm-bogo-retro-classic',
            'description' => 'Helm bogo original kulit sintetis',
            'price' => 150000,
            'stock' => 5,
            'status' => 'available',
        ]);

        $this->product2 = CommunityProduct::create([
            'community_id' => $this->community2->id,
            'user_id' => $this->seller->id,
            'name' => 'Masker Snorkeling Anti Fog',
            'slug' => 'masker-snorkeling-anti-fog',
            'description' => 'Masker selam kaca tempered',
            'price' => 275000,
            'stock' => 2,
            'status' => 'available',
        ]);
    }

    public function test_public_user_can_access_marketplace_catalog(): void
    {
        $response = $this->get(route('marketplace.index'));

        $response->assertStatus(200);
        $response->assertSee('Etalase Jual Beli Komunitas');
        $response->assertSee('Helm Bogo Retro Classic');
        $response->assertSee('Masker Snorkeling Anti Fog');
        $response->assertSee('Komunitas Vespa Padang');
        $response->assertSee('Klub Scuba Ranah Minang');
    }

    public function test_marketplace_search_filters_products(): void
    {
        $response = $this->get(route('marketplace.index', ['search' => 'Bogo']));

        $response->assertStatus(200);
        $response->assertSee('Helm Bogo Retro Classic');
        $response->assertDontSee('Masker Snorkeling Anti Fog');
    }

    public function test_marketplace_category_filters_products(): void
    {
        $response = $this->get(route('marketplace.index', ['category' => 'kesehatan']));

        $response->assertStatus(200);
        $response->assertSee('Masker Snorkeling Anti Fog');
        $response->assertDontSee('Helm Bogo Retro Classic');
    }

    public function test_marketplace_price_sorting_works(): void
    {
        $response = $this->get(route('marketplace.index', ['sort' => 'price_desc']));

        $response->assertStatus(200);
        $content = $response->getContent();
        $posProduct2 = strpos($content, 'Masker Snorkeling Anti Fog');
        $posProduct1 = strpos($content, 'Helm Bogo Retro Classic');

        $this->assertTrue($posProduct2 < $posProduct1, 'Produk harga tertinggi harus muncul lebih dahulu pada pengurutan price_desc');
    }

    public function test_authenticated_user_can_initiate_product_purchase(): void
    {
        $response = $this->actingAs($this->buyer)->post(route('communities.products.buy', $this->product1->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->buyer->id,
            'type' => 'product',
            'reference_id' => $this->product1->id,
            'amount' => 150000,
        ]);
    }

    public function test_welcome_page_displays_auctions_and_marketplace_carousels(): void
    {
        CommunityAuction::create([
            'community_id' => $this->community1->id,
            'user_id' => $this->seller->id,
            'title' => 'Speedometer Vespa Klasik NOS',
            'slug' => 'speedometer-vespa-klasik-nos',
            'starting_price' => 500000,
            'current_price' => 500000,
            'bid_increment' => 50000,
            'start_time' => now(),
            'end_time' => now()->addDays(2),
            'status' => 'active',
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Arena Lelang Komunitas');
        $response->assertSee('Etalase Jual Beli Komunitas');
        $response->assertSee('Speedometer Vespa Klasik NOS');
        $response->assertSee('Helm Bogo Retro Classic');
    }

    public function test_navigation_includes_jual_beli_and_lelang_links(): void
    {
        $response = $this->get(route('events.index'));

        $response->assertStatus(200);
        $response->assertSee(route('marketplace.index'));
        $response->assertSee(route('auctions.index'));
    }
}
