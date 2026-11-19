<?php

namespace App\Providers;

use App\Socialite\NikniqProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as Socialite;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app->make(Socialite::class)->extend('nikniq', function ($app) {
            return $app->make(Socialite::class)->buildProvider(NikniqProvider::class, config('services.nikniq'));
        });
    }
}
