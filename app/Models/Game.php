<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['result', 'difficulty', 'moves', 'pieces_left', 'captured', 'duration_seconds', 'points'])]
class Game extends Model
{
    public const DIFFICULTY_MULTIPLIER = ['easy' => 1, 'medium' => 2, 'hard' => 3];

    /**
     * Points are computed server-side from the reported stats.
     */
    public static function calculatePoints(string $result, string $difficulty, int $piecesLeft, int $captured): int
    {
        $base = match ($result) {
            'win' => 100 + $piecesLeft * 10,
            'draw' => 25,
            'loss' => 0,
        };

        return ($base + $captured * 2) * self::DIFFICULTY_MULTIPLIER[$difficulty];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
