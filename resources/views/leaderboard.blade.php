@extends('layouts.app')
@section('title', 'Leaderboard · Checkers')
@section('content')
<h1>Leaderboard</h1>
<div class="card">
@if ($leaders->isEmpty())
    <p class="muted">No scores yet. Be the first!</p>
@else
    <table>
        <thead><tr><th>#</th><th>Player</th><th>Points</th><th>Wins</th><th>Played</th></tr></thead>
        <tbody>
        @foreach ($leaders as $i => $row)
            <tr><td>{{ $i + 1 }}</td><td>{{ $row->name }}</td><td><strong>{{ number_format($row->total) }}</strong></td><td>{{ $row->wins }}</td><td>{{ $row->played }}</td></tr>
        @endforeach
        </tbody>
    </table>
@endif
</div>
@endsection
