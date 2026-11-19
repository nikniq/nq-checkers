<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'Users' => User::count(),
                'New users (7d)' => User::where('created_at', '>=', now()->subDays(7))->count(),
                'Games saved' => Game::count(),
                'Games (7d)' => Game::where('created_at', '>=', now()->subDays(7))->count(),
                'Google sign-ins' => User::whereNotNull('google_id')->count(),
                'Nikniq sign-ins' => User::whereNotNull('nikniq_id')->count(),
            ],
            'recentGames' => Game::with('user:id,name')->latest()->limit(10)->get(),
        ]);
    }

    public function users(Request $request): View
    {
        $q = trim((string) $request->query('q'));

        $users = User::query()
            ->withCount('games')
            ->withSum('games', 'points')
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w->where('name', 'like', "%$q%")->orWhere('email', 'like', "%$q%")))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.users', ['users' => $users, 'q' => $q]);
    }

    public function toggleAdmin(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'You cannot change your own admin role.']);
        }

        $user->forceFill(['is_admin' => ! $user->is_admin])->save();

        return back()->with('status', "Updated {$user->name}.");
    }

    public function destroyUser(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'You cannot delete your own account here.']);
        }

        $user->delete();

        return back()->with('status', "Deleted {$user->name}.");
    }
}
