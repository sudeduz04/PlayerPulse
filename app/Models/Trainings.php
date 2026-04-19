<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trainings extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'team_id',
        'created_by',
        'title',
        'training_date',
        'training_type',
        'duration_minutes',
        'description',
        'coach_note',
    ];

    protected function casts(): array
    {
        return [
            'training_date' => 'date',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Teams::class, 'team_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function performances(): HasMany
    {
        return $this->hasMany(PlayerTrainingPerformances::class, 'training_id');
    }
}
