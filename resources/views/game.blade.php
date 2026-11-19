@extends('layouts.app')
@section('content')
@guest
<section class="hero">
    <div>
        <h1>Play checkers free. Beat the computer. Climb the leaderboard.</h1>
        <p class="lead">Three difficulty levels, a two-player mode and saved stats for every game. No download needed. Your first move is just below.</p>
        <div class="hero-cta">
            <a href="{{ route('register') }}" class="btn big">Create free account</a>
            <a href="#board" class="btn ghost big">Play as guest</a>
        </div>
        <ul class="proof">
            <li>✓ Free forever</li><li>✓ Sign in with Google in one click</li><li>✓ Works on phone and desktop</li>
        </ul>
    </div>
</section>
@endguest
<div class="game-layout">
    <section class="board-wrap">
        <div id="board" class="board" aria-label="Checkers board"></div>
    </section>
    <aside class="card side">
        <h2 id="status">Your move (red)</h2>
        <label>Mode
            <select id="mode">
                <option value="ai">vs Computer</option>
                <option value="local">2 players (same device)</option>
            </select>
        </label>
        <label id="difficulty-wrap">Difficulty
            <select id="difficulty">
                <option value="easy">Easy</option>
                <option value="medium" selected>Medium</option>
                <option value="hard">Hard</option>
            </select>
        </label>
        <button id="new-game" class="btn">New game</button>
        <dl class="stats">
            <dt>Moves</dt><dd id="moves">0</dd>
            <dt>Red pieces</dt><dd id="red-count">12</dd>
            <dt>Black pieces</dt><dd id="black-count">12</dd>
        </dl>
        <p class="muted small">Captures are mandatory. Reach the far row to crown a king.</p>
        @auth
            <p class="note">Games vs the computer are saved to your scores.</p>
        @else
            <p class="note">Playing as guest. Your wins aren't being saved yet. <a href="{{ route('register') }}"><strong>Sign up free</strong></a> to keep your scores and get on the leaderboard.</p>
        @endauth
        <div id="result" class="result" hidden></div>
        @guest
            <a href="{{ route('register') }}" class="btn ghost upsell">Save this result: sign up free</a>
        @endguest
    </aside>
</div>
@endsection
@push('scripts')
<script>
    window.CHECKERS = {
        loggedIn: @json(auth()->check()),
        saveUrl: @json(route('games.store')),
    };
</script>
<script src="{{ asset('js/checkers.js') }}"></script>
@endpush
