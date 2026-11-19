@extends('layouts.app')
@section('title', 'Log in · Checkers')
@section('content')
<div class="card narrow">
    <h1>Log in</h1>
    <p class="muted">Log in to save your scores and appear on the leaderboard.</p>
    @include('partials.oauth')
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label>Email <input type="email" name="email" value="{{ old('email') }}" required autofocus></label>
        <label>Password <input type="password" name="password" required></label>
        <label class="check"><input type="checkbox" name="remember"> Remember me</label>
        @if ($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
        <button class="btn" type="submit">Log in</button>
    </form>
    <p class="muted">No account? <a href="{{ route('register') }}">Register</a></p>
</div>
@endsection
