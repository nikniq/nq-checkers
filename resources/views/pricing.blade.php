@extends('layouts.app')
@section('title', 'Pricing · NQ Checkers')
@section('content')
<section class="center">
    <h1>Simple pricing. Start free.</h1>
    <p class="lead">Every core feature is free. Upgrade when you want to go further.</p>
</section>
<div class="plans">
    <div class="card plan">
        <h2>Guest</h2>
        <p class="price">$0</p>
        <ul>
            <li>✓ Unlimited games</li>
            <li>✓ vs Computer and 2-player mode</li>
            <li class="muted">✗ Scores not saved</li>
            <li class="muted">✗ Not on the leaderboard</li>
        </ul>
        <a href="{{ route('home') }}" class="btn ghost">Play now</a>
    </div>
    <div class="card plan featured">
        <span class="badge">Most popular</span>
        <h2>Free account</h2>
        <p class="price">$0 <small>forever</small></p>
        <ul>
            <li>✓ Everything in Guest</li>
            <li>✓ Every game saved</li>
            <li>✓ Personal stats and win rate</li>
            <li>✓ Leaderboard ranking</li>
            <li>✓ One-click Google sign-in</li>
        </ul>
        @auth
            <a href="{{ route('home') }}" class="btn">You're in. Play now</a>
        @else
            <a href="{{ route('register') }}" class="btn">Create free account</a>
        @endauth
    </div>
    <div class="card plan">
        <h2>Pro</h2>
        <p class="price">$3 <small>/ month</small></p>
        <ul>
            <li>✓ Everything in Free</li>
            <li>✓ Expert AI level</li>
            <li>✓ Game replay and move analysis</li>
            <li>✓ Pro badge on the leaderboard</li>
            <li>✓ No ads, ever</li>
        </ul>
        <a href="mailto:{{ config('mail.from.address') }}?subject=NQ%20Checkers%20Pro" class="btn ghost">Join the Pro waitlist</a>
    </div>
</div>
<section class="card faq">
    <h2>Questions</h2>
    <h3>Is it really free?</h3><p class="muted">Yes. Playing, saving scores and the leaderboard are free with no time limit.</p>
    <h3>Do I need to download anything?</h3><p class="muted">No. It runs in any modern browser on phone, tablet or desktop.</p>
    <h3>How are points calculated?</h3><p class="muted">Win = 100 + 10 per piece left, draw = 25, plus 2 per capture, multiplied by difficulty (×1, ×2 or ×3).</p>
</section>
@endsection
