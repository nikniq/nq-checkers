<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'Play checkers free in your browser. Beat the computer, climb the leaderboard and track every win.')">
    <title>@yield('title', 'NQ Checkers · Play free checkers online')</title>
    <link rel="stylesheet" href="{{ asset('css/checkers.css') }}">
</head>
<body>
<header class="topbar">
    <a href="{{ route('home') }}" class="brand">● NQ Checkers</a>
    <input type="checkbox" id="nav-toggle" class="nav-toggle" aria-label="Toggle menu">
    <label for="nav-toggle" class="nav-burger" aria-hidden="true">☰</label>
    <nav class="mainnav">
        <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Play</a>
        <a href="{{ route('leaderboard') }}" @class(['active' => request()->routeIs('leaderboard')])>Leaderboard</a>
        <a href="{{ route('pricing') }}" @class(['active' => request()->routeIs('pricing')])>Pricing</a>
        @auth
            <a href="{{ route('scores') }}" @class(['active' => request()->routeIs('scores')])>My scores</a>
            @if (auth()->user()->is_admin)
                <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.*')])>Admin</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="link">Log out ({{ auth()->user()->name }})</button>
            </form>
        @else
            <a href="{{ route('login') }}">Log in</a>
            <a href="{{ route('register') }}" class="btn small">Sign up free</a>
        @endauth
    </nav>
</header>
<main class="container">
    @if (session('status'))<div class="note">{{ session('status') }}</div>@endif
    @yield('content')
</main>
<footer class="footer">
    <div class="footer-inner">
        <div>
            <a href="{{ route('home') }}" class="brand">● NQ Checkers</a>
            <p class="muted small">Classic checkers against a smart computer opponent. Free to play, no download.</p>
        </div>
        <nav>
            <strong>Play</strong>
            <a href="{{ route('home') }}">Play now</a>
            <a href="{{ route('leaderboard') }}">Leaderboard</a>
            <a href="{{ route('pricing') }}">Pricing</a>
        </nav>
        <nav>
            <strong>Account</strong>
            @auth
                <a href="{{ route('scores') }}">My scores</a>
            @else
                <a href="{{ route('register') }}">Sign up free</a>
                <a href="{{ route('login') }}">Log in</a>
            @endauth
        </nav>
        <nav>
            <strong>Legal</strong>
            <a href="{{ route('privacy') }}">Privacy</a>
            <a href="{{ route('terms') }}">Terms</a>
        </nav>
    </div>
    <p class="muted small footer-copy">© {{ date('Y') }} NQ Checkers. All rights reserved.</p>
</footer>
@stack('scripts')
</body>
</html>
