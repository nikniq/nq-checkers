<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GameController extends Controller
{
    public function play(): View
    {
        return view('game');
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'result' => ['required', 'in:win,loss,draw'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'moves' => ['required', 'integer', 'min:1', 'max:1000'],
            'pieces_left' => ['required', 'integer', 'min:0', 'max:12'],
            'captured' => ['required', 'integer', 'min:0', 'max:12'],
            'duration_seconds' => ['required', 'integer', 'min:0', 'max:86400'],
        ]);

        $data['points'] = Game::calculatePoints($data['result'], $data['difficulty'], $data['pieces_left'], $data['captured']);

        $game = $request->user()->games()->create($data);

        return response()->json([
            'points' => $game->points,
            'total' => (int) $request->user()->games()->sum('points'),
        ], 201);
    }

    public function history(Request $request): View
    {
        $user = $request->user();

        return view('scores', [
            'games' => $user->games()->latest()->paginate(20),
            'stats' => $user->games()->selectRaw(
                "count(*) as played, sum(points) as total,
                 sum(case when result = 'win' then 1 else 0 end) as wins,
                 sum(case when result = 'loss' then 1 else 0 end) as losses,
                 sum(case when result = 'draw' then 1 else 0 end) as draws"
            )->first(),
        ]);
    }

    public function leaderboard(): View
    {
        $leaders = DB::table('games')
            ->join('users', 'users.id', '=', 'games.user_id')
            ->select('users.name')
            ->selectRaw("sum(games.points) as total, count(*) as played, sum(case when games.result = 'win' then 1 else 0 end) as wins")
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->limit(25)
            ->get();

        return view('leaderboard', ['leaders' => $leaders]);
    }
}
