<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Players extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'team_id',
        'position_id',
        'first_name',
        'last_name',
        'birth_date',
        'jersey_number',
        'height',
        'weight',
        'dominant_foot',
        'nationality',
        'status',
        'photo',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Teams::class, 'team_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Positions::class, 'position_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function trainingPerformances(): HasMany
    {
        return $this->hasMany(PlayerTrainingPerformances::class, 'player_id');
    }

    public function matchStats(): HasMany
    {
        return $this->hasMany(PlayerMatchStats::class, 'player_id');
    }

    public function injuries(): HasMany
    {
        return $this->hasMany(Injuries::class, 'player_id');
    }

    public function physicalMeasurements(): HasMany
    {
        return $this->hasMany(PhysicalMeasurements::class, 'player_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(PlayerNotes::class, 'player_id');
    }

    public function developmentReports(): HasMany
    {
        return $this->hasMany(DevelopmentReports::class, 'player_id');
    }
}
