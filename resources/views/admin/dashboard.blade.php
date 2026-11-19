@extends('layouts.app')
@section('title', 'Admin · NQ Checkers')
@section('content')
<h1>Admin</h1>
@include('admin.nav')
<div class="tiles">
    @foreach ($stats as $label => $value)
        <div class="tile"><span>{{ number_format($value) }}</span>{{ $label }}</div>
    @endforeach
</div>
<div class="card">
    <h2>Recent games</h2>
    @if ($recentGames->isEmpty())
        <p class="muted">No games yet.</p>
    @else
    <table>
        <thead><tr><th>When</th><th>Player</th><th>Result</th><th>Difficulty</th><th>Points</th></tr></thead>
        <tbody>
        @foreach ($recentGames as $g)
            <tr><td>{{ $g->created_at->diffForHumans() }}</td><td>{{ $g->user?->name }}</td><td>{{ ucfirst($g->result) }}</td><td>{{ ucfirst($g->difficulty) }}</td><td>{{ $g->points }}</td></tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
