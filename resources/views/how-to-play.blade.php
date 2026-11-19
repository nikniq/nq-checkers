@extends('layouts.app')
@section('title', 'How to play checkers · NQ Checkers')
@section('description', 'Learn the rules of checkers in two minutes, plus strategy tips to start winning against the computer.')
@section('content')
<section class="center">
    <h1>How to play checkers</h1>
    <p class="lead">Learn the rules in two minutes, then use the tips below to start winning.</p>
</section>

<div class="rules-grid">
    <div class="card">
        <h2>🎯 The goal</h2>
        <p>Capture all of your opponent's pieces, or leave them with no legal move. You play <strong>red</strong> and move first.</p>
    </div>
    <div class="card">
        <h2>♟️ Moving</h2>
        <ul>
            <li>Pieces move diagonally, one square forward, on the dark squares only.</li>
            <li>Click a piece that can move (it lifts on hover), then click a green dot to move there.</li>
        </ul>
    </div>
    <div class="card">
        <h2>⚔️ Capturing</h2>
        <ul>
            <li>Jump diagonally over an opponent's piece onto an empty square right behind it to capture it.</li>
            <li><strong>Captures are mandatory.</strong> If you can jump, you must.</li>
            <li>If you land where you can jump again, keep going. That's a multi-jump.</li>
        </ul>
    </div>
    <div class="card">
        <h2>👑 Kings</h2>
        <ul>
            <li>Reach the far row and your piece is crowned a king (♛).</li>
            <li>Kings move and capture both forwards and backwards.</li>
        </ul>
    </div>
    <div class="card">
        <h2>🤝 Draws</h2>
        <p>If 40 moves pass for each side with no capture and no new king, the game is a draw.</p>
    </div>
    <div class="card">
        <h2>🏆 Scoring</h2>
        <p>Win = 100 + 10 for each piece left. Draw = 25. Add 2 for each capture, then multiply by difficulty: ×1 Easy, ×2 Medium, ×3 Hard. <a href="{{ route('register') }}">Sign up free</a> to save your points.</p>
    </div>
</div>

<div class="card prose">
    <h2>💡 Hints and strategy</h2>
    <ol>
        <li><strong>Stuck? Press "💡 Hint"</strong> during a game. It outlines the suggested move in blue and explains why.</li>
        <li><strong>Guard your back row.</strong> While it's filled, your opponent can't crown kings.</li>
        <li><strong>Take the centre.</strong> Pieces in the middle have more options than pieces on the edges.</li>
        <li><strong>Move in pairs.</strong> A piece with a friend right behind it can't be jumped.</li>
        <li><strong>Look one move ahead.</strong> Before you move, check whether it gives your opponent a jump.</li>
        <li><strong>Use forced captures.</strong> Offer one piece so your opponent must jump into a spot where you take two back.</li>
        <li><strong>Trade when you're ahead.</strong> Swapping pieces one for one helps whoever has more.</li>
        <li><strong>Crown early.</strong> A king can attack from behind, and that often decides the game.</li>
    </ol>
    <p><a href="{{ route('home') }}" class="btn">Start playing</a></p>
</div>
@endsection
