<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhysicalMeasurements extends Model
{
    protected $fillable = [
        'player_id',
        'measurement_date',
        'height',
        'weight',
        'body_fat_percentage',
        'sprint_time',
        'agility_score',
        'endurance_level',
        'strength_score',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'measurement_date' => 'date',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Players::class, 'player_id');
    }
}
