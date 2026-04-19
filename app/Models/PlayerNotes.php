<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerNotes extends Model
{
    protected $fillable = [
        'player_id',
        'user_id',
        'note_date',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'note_date' => 'date',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Players::class, 'player_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
