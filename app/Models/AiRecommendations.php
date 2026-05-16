<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiRecommendations extends Model
{
    protected $fillable = [
        'player_id',
        'match_id',
        'recommendation_type',
        'score',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
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
