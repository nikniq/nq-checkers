@php($google = \App\Http\Controllers\Auth\SocialAuthController::enabled('google'))
@php($nikniq = \App\Http\Controllers\Auth\NikniqController::enabled())
@if ($google || $nikniq)
    <div class="oauth">
        @if ($nikniq)
            <a href="{{ route('auth.nikniq') }}" class="btn oauth-btn oauth-nikniq">Continue with nikniq.com</a>
        @endif
        @if ($google)
            <a href="{{ route('oauth.redirect', 'google') }}" class="btn oauth-btn oauth-google">Continue with Google</a>
        @endif
    </div>
    <div class="divider"><span>or use email</span></div>
@endif
