<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineupPlayers extends Model
{
    protected $fillable = [
        'lineup_id',
        'player_id',
        'position_id',
        'slot_key',
        'field_x',
        'field_y',
        'is_starting',
        'recommendation_score',
    ];

    protected function casts(): array
    {
        return [
            'is_starting' => 'boolean',
        ];
    }

    public function lineup(): BelongsTo
    {
        return $this->belongsTo(Lineups::class, 'lineup_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Players::class, 'player_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Positions::class, 'position_id');
    }
}
