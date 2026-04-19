<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerMatchStats extends Model
{
    protected $fillable = [
        'player_id',
        'match_id',
        'minutes_played',
        'is_starting',
        'goals',
        'assists',
        'shots',
        'successful_passes',
        'pass_accuracy',
        'tackles',
        'interceptions',
        'dribbles',
        'fouls',
        'yellow_cards',
        'red_cards',
        'match_rating',
    ];

    protected function casts(): array
    {
        return [
            'is_starting' => 'boolean',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Players::class, 'player_id');
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(Matches::class, 'match_id');
    }
}
