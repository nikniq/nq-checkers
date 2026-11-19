<nav class="subnav">
    <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>Dashboard</a>
    <a href="{{ route('admin.users') }}" @class(['active' => request()->routeIs('admin.users')])>Users</a>
</nav>
@if ($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
