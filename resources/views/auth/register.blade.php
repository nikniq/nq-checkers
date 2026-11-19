@extends('layouts.app')
@section('title', 'Register · Checkers')
@section('content')
<div class="card narrow">
    <h1>Create account</h1>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <label>Display name <input type="text" name="name" value="{{ old('name') }}" required maxlength="50" autofocus></label>
        <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
        <label>Password <input type="password" name="password" required></label>
        <label>Confirm password <input type="password" name="password_confirmation" required></label>
        @if ($errors->any())
            <div class="error">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        @endif
        <button class="btn" type="submit">Register</button>
    </form>
    <p class="muted">Already registered? <a href="{{ route('login') }}">Log in</a></p>
</div>
@endsection
