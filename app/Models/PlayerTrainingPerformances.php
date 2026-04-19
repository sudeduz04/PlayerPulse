<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerTrainingPerformances extends Model
{
    protected $fillable = [
        'player_id',
        'training_id',
        'attendance_status',
        'performance_score',
        'speed_score',
        'endurance_score',
        'technique_score',
        'discipline_score',
        'coach_comment',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Players::class, 'player_id');
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(Trainings::class, 'training_id');
    }
}
