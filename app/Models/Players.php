<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Players extends Model
{
    use SoftDeletes;

    protected $fillable = [
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

    protected function casts()
    {
        return [
            'birth_date' => 'date',
            'status' => 'boolean',
        ];
    }
}
