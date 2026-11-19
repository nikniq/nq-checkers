<?php

namespace Tests\Feature;

use Tests\TestCase;

class OAuthRedirectTest extends TestCase
{
    public function test_google_redirect_points_to_google(): void
    {
        config(['services.google.client_id' => 'gid', 'services.google.client_secret' => 's']);
        $url = $this->get('/auth/google/redirect')->assertRedirect()->headers->get('Location');
        $this->assertStringStartsWith('https://accounts.google.com/o/oauth2/auth', $url);
        $this->assertStringContainsString('client_id=gid', $url);
        $this->assertStringContainsString(urlencode(url('/auth/google/callback')), $url);
    }

    public function test_nikniq_redirect_points_to_nikniq(): void
    {
        config(['services.nikniq.client_id' => 'nid', 'services.nikniq.client_secret' => 's']);
        $url = $this->get('/auth/nikniq/redirect')->assertRedirect()->headers->get('Location');
        $this->assertStringStartsWith('https://nikniq.com/oauth/authorize', $url);
        $this->assertStringContainsString('client_id=nid', $url);
        $this->assertStringContainsString(urlencode(url('/auth/nikniq/callback')), $url);
    }

    public function test_login_shows_both_buttons_when_configured(): void
    {
        config(['services.google.client_id' => 'g', 'services.nikniq.client_id' => 'n']);
        $this->get('/login')->assertSee('Continue with Google')->assertSee('Continue with Nikniq');
    }

    public function test_footer_links_to_nikniq(): void
    {
        $this->get('/')->assertSee('https://nikniq.com', false);
    }
}
