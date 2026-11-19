@extends('layouts.app')
@section('title', 'My scores · Checkers')
@section('content')
<h1>My scores</h1>
<div class="tiles">
    <div class="tile"><span>{{ number_format($stats->total ?? 0) }}</span>Total points</div>
    <div class="tile"><span>{{ $stats->played }}</span>Played</div>
    <div class="tile"><span>{{ $stats->wins ?? 0 }}</span>Wins</div>
    <div class="tile"><span>{{ $stats->losses ?? 0 }}</span>Losses</div>
    <div class="tile"><span>{{ $stats->draws ?? 0 }}</span>Draws</div>
</div>
<div class="card">
@if ($games->isEmpty())
    <p class="muted">No games yet. <a href="{{ route('home') }}">Play one!</a></p>
@else
    <table>
        <thead><tr><th>Date</th><th>Result</th><th>Difficulty</th><th>Moves</th><th>Pieces left</th><th>Captured</th><th>Time</th><th>Points</th></tr></thead>
        <tbody>
        @foreach ($games as $g)
            <tr>
                <td>{{ $g->created_at->format('Y-m-d H:i') }}</td>
                <td><span class="badge {{ $g->result }}">{{ ucfirst($g->result) }}</span></td>
                <td>{{ ucfirst($g->difficulty) }}</td>
                <td>{{ $g->moves }}</td>
                <td>{{ $g->pieces_left }}</td>
                <td>{{ $g->captured }}</td>
                <td>{{ gmdate('i:s', $g->duration_seconds) }}</td>
                <td><strong>{{ $g->points }}</strong></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $games->links('pagination::simple-bootstrap-5') }}
@endif
</div>
@endsection
