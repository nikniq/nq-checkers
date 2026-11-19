@extends('layouts.app')
@section('title', 'Users · Admin')
@section('content')
<h1>Users</h1>
@include('admin.nav')
<form method="GET" class="search">
    <input type="search" name="q" value="{{ $q }}" placeholder="Search name or email">
    <button class="btn" type="submit">Search</button>
</form>
<div class="card">
    <table>
        <thead><tr><th>Name</th><th>Email</th><th>Sign-in</th><th>Games</th><th>Points</th><th>Joined</th><th>Role</th><th></th></tr></thead>
        <tbody>
        @forelse ($users as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ collect(['Google' => $u->google_id, 'Nikniq' => $u->nikniq_id, 'Email' => $u->password])->filter()->keys()->join(', ') }}</td>
                <td>{{ $u->games_count }}</td>
                <td>{{ number_format($u->games_sum_points ?? 0) }}</td>
                <td>{{ $u->created_at->toDateString() }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.users.toggle-admin', $u) }}" class="inline">
                        @csrf
                        <button class="link" type="submit">{{ $u->is_admin ? 'Admin ✓' : 'User' }}</button>
                    </form>
                </td>
                <td>
                    @unless ($u->is(auth()->user()))
                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline" onsubmit="return confirm('Delete {{ addslashes($u->name) }} and all their games?')">
                        @csrf @method('DELETE')
                        <button class="link danger" type="submit">Delete</button>
                    </form>
                    @endunless
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="muted">No users found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
{{ $users->links() }}
@endsection
