<?php

namespace App\Socialite;

use GuzzleHttp\RequestOptions;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

/**
 * Generic OAuth2 provider for the Nikniq identity server.
 * Endpoints are configured in config/services.php under "nikniq".
 */
class NikniqProvider extends AbstractProvider
{
    protected $scopes = [];

    protected $scopeSeparator = ' ';

    protected function url(string $key): string
    {
        $cfg = config('services.nikniq');

        return rtrim($cfg['base_url'], '/').'/'.ltrim($cfg[$key], '/');
    }

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase($this->url('authorize_path'), $state);
    }

    protected function getTokenUrl(): string
    {
        return $this->url('token_path');
    }

    protected function getUserByToken($token): array
    {
        $response = $this->getHttpClient()->get($this->url('user_path'), [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        $data = json_decode((string) $response->getBody(), true);

        return $data['data'] ?? $data;
    }

    protected function mapUserToObject(array $user): User
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'] ?? $user['sub'] ?? null,
            'nickname' => $user['username'] ?? null,
            'name' => $user['name'] ?? $user['username'] ?? null,
            'email' => $user['email'] ?? null,
            'avatar' => $user['avatar'] ?? $user['picture'] ?? null,
        ]);
    }
}
