<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResponsiveAndPwaTest extends TestCase
{
    use RefreshDatabase;

    public function test_pwa_manifest_is_accessible_and_valid_json(): void
    {
        $response = $this->get('/manifest.webmanifest');

        $response->assertStatus(200);
        $this->assertStringContainsString('application/manifest+json', $response->headers->get('Content-Type'));

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
        $this->assertEquals('CommunityHub - Platform Komunitas & Event', $data['name']);
        $this->assertEquals('CommunityHub', $data['short_name']);
        $this->assertEquals('standalone', $data['display']);
        $this->assertNotEmpty($data['icons']);
    }

    public function test_pwa_service_worker_is_accessible_and_has_network_first_strategy(): void
    {
        $response = $this->get('/sw.js');

        $response->assertStatus(200);
        $this->assertStringContainsString('javascript', $response->headers->get('Content-Type'));
        $this->assertEquals('/', $response->headers->get('Service-Worker-Allowed'));

        $content = $response->getContent();
        $this->assertStringContainsString('communityhub-shell-v1', $content);
        $this->assertStringContainsString('fetch(event.request)', $content);
    }

    public function test_pwa_icon_assets_exist_on_filesystem(): void
    {
        $icon192 = public_path('images/icons/icon-192x192.png');
        $icon512 = public_path('images/icons/icon-512x512.png');

        $this->assertFileExists($icon192);
        $this->assertFileExists($icon512);

        $size192 = getimagesize($icon192);
        $size512 = getimagesize($icon512);

        $this->assertEquals(192, $size192[0]);
        $this->assertEquals(192, $size192[1]);
        $this->assertEquals(512, $size512[0]);
        $this->assertEquals(512, $size512[1]);
    }

    public function test_authenticated_page_renders_bottom_nav_and_mobile_post_modal(): void
    {
        $user = User::factory()->create([
            'username' => 'testuser',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        // PWA Meta Tags
        $response->assertSee('manifest.webmanifest', false);
        $response->assertSee('apple-mobile-web-app-capable', false);
        $response->assertSee('theme-color', false);

        // Bottom Nav Bar Component
        $response->assertSee('md:hidden fixed bottom-0', false);
        $response->assertSee('open-mobile-composer', false);

        // Mobile Post Modal Component
        $response->assertSee('Buat Postingan Komunitas', false);
    }

    public function test_guest_landing_page_renders_bottom_nav_with_login_link(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('md:hidden fixed bottom-0', false);
        $response->assertSee(route('login'), false);
    }
}
