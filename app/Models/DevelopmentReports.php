<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevelopmentReports extends Model
{
    protected $fillable = [
        'player_id',
        'created_by',
        'report_date',
        'technical_development',
        'physical_development',
        'tactical_development',
        'mental_development',
        'overall_score',
        'strengths',
        'weaknesses',
        'recommendations',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Players::class, 'player_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
