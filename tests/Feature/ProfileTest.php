<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'username' => 'testuser_updated',
                'email' => 'test@example.com',
                'bio' => 'This is my test bio.',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('testuser_updated', $user->username);
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('This is my test bio.', $user->bio);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'username' => $user->username,
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_public_profile_can_be_viewed_by_username(): void
    {
        $user = User::factory()->create([
            'username' => 'unique_social_user',
            'name' => 'Unique User',
            'bio' => 'A passionate explorer of West Sumatra.',
        ]);

        $response = $this->get('/users/'.$user->username);

        $response->assertOk();
        $response->assertSee('Unique User');
        $response->assertSee('@unique_social_user');
        $response->assertSee('A passionate explorer of West Sumatra.');
    }

    public function test_user_can_follow_and_unfollow_another_user_via_ajax(): void
    {
        $user1 = User::factory()->create(['username' => 'follower_user']);
        $user2 = User::factory()->create(['username' => 'followed_user']);

        // Follow
        $response = $this
            ->actingAs($user1)
            ->postJson('/users/'.$user2->id.'/follow');

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'following' => true,
            'followers_count' => 1,
        ]);

        $this->assertTrue($user1->isFollowing($user2));
        $this->assertEquals(1, $user2->followers()->count());

        // Unfollow
        $response = $this
            ->actingAs($user1)
            ->postJson('/users/'.$user2->id.'/follow');

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'following' => false,
            'followers_count' => 0,
        ]);

        $this->assertFalse($user1->isFollowing($user2));
    }

    public function test_cannot_follow_oneself(): void
    {
        $user = User::factory()->create(['username' => 'myself_user']);

        $response = $this
            ->actingAs($user)
            ->postJson('/users/'.$user->id.'/follow');

        $response->assertStatus(422);
        $this->assertFalse($user->isFollowing($user));
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
