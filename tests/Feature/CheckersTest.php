<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckersTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_play(): void
    {
        $this->get('/')->assertOk()->assertSee('Playing as guest');
    }

    public function test_user_can_register_and_login(): void
    {
        $this->post('/register', [
            'name' => 'Ann', 'email' => 'ann@example.com',
            'password' => 'secret-pass-1', 'password_confirmation' => 'secret-pass-1',
        ])->assertRedirect('/');
        $this->assertAuthenticated();

        $this->post('/logout');
        $this->assertGuest();

        $this->post('/login', ['email' => 'ann@example.com', 'password' => 'secret-pass-1'])->assertRedirect('/');
        $this->assertAuthenticated();
    }

    public function test_guest_cannot_save_score(): void
    {
        $this->postJson('/games', [])->assertUnauthorized();
    }

    public function test_logged_in_user_saves_score_with_server_side_points(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/games', [
            'result' => 'win', 'difficulty' => 'hard', 'moves' => 30,
            'pieces_left' => 5, 'captured' => 12, 'duration_seconds' => 300,
        ])->assertCreated()->assertJson(['points' => (100 + 50 + 24) * 3]);

        $this->actingAs($user)->get('/scores')->assertOk()->assertSee('522');
        $this->get('/leaderboard')->assertOk()->assertSee($user->name);
    }

    public function test_public_path_is_public_html(): void
    {
        $this->assertSame(base_path('public_html'), public_path());
    }

    public function test_how_to_play_page_and_hint_button(): void
    {
        $this->get('/how-to-play')->assertOk()->assertSee('Captures are mandatory');
        $this->get('/')->assertOk()->assertSee('id="hint"', false)->assertSee('How to play');
    }
}
