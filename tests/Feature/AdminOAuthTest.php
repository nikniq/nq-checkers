<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialUser;
use Tests\TestCase;

class AdminOAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_admin(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }

    public function test_admin_can_view_dashboard_and_users(): void
    {
        $admin = User::factory()->create(['email' => 'boss@example.com']);
        $admin->forceFill(['is_admin' => true])->save();

        $this->actingAs($admin)->get('/admin')->assertOk()->assertSee('Recent games');
        $this->actingAs($admin)->get('/admin/users?q=boss')->assertOk()->assertSee('boss@example.com');
    }

    public function test_admin_can_delete_other_user_but_not_self(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $other = User::factory()->create();

        $this->actingAs($admin)->delete("/admin/users/{$other->id}")->assertRedirect();
        $this->assertModelMissing($other);
        $this->actingAs($admin)->delete("/admin/users/{$admin->id}");
        $this->assertModelExists($admin);
    }

    public function test_oauth_disabled_without_client_id(): void
    {
        config(['services.google.client_id' => null]);
        $this->get('/auth/google/redirect')->assertNotFound();
    }

    public function test_google_callback_creates_and_logs_in_user(): void
    {
        config(['services.google.client_id' => 'x']);
        $social = (new SocialUser)->map(['id' => 'g-123', 'name' => 'Gina', 'email' => 'gina@example.com', 'avatar' => null]);
        Socialite::shouldReceive('driver->user')->andReturn($social);

        $this->get('/auth/google/callback')->assertRedirect('/');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'gina@example.com', 'google_id' => 'g-123']);
    }

    public function test_nikniq_callback_links_existing_account(): void
    {
        config(['services.nikniq.client_id' => 'x']);
        $user = User::factory()->create(['email' => 'nik@example.com']);
        $social = (new SocialUser)->map(['id' => 42, 'name' => 'Nik', 'email' => 'nik@example.com', 'avatar' => null]);
        Socialite::shouldReceive('driver->user')->andReturn($social);

        $this->get('/auth/nikniq/callback')->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
        $this->assertSame('42', (string) $user->fresh()->nikniq_id);
    }

    public function test_pricing_and_footer_render(): void
    {
        $this->get('/pricing')->assertOk()->assertSee('Create free account')->assertSee('Privacy');
    }
}
