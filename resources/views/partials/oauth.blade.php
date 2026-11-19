@php($providers = array_filter(\App\Http\Controllers\Auth\SocialAuthController::PROVIDERS, [\App\Http\Controllers\Auth\SocialAuthController::class, 'enabled']))
@if ($providers)
    <div class="oauth">
        @foreach ($providers as $p)
            <a href="{{ route('oauth.redirect', $p) }}" class="btn oauth-btn oauth-{{ $p }}">Continue with {{ $p === 'nikniq' ? 'Nikniq' : 'Google' }}</a>
        @endforeach
    </div>
    <div class="divider"><span>or use email</span></div>
@endif
