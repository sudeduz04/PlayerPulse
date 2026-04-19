<?php

namespace App\Http\Requests\Training;

use App\Http\Requests\ApiFormRequest;

class BulkPerformanceRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'players' => ['required', 'array', 'min:1'],
            'players.*.player_id' => ['required', 'exists:players,id'],
            'players.*.attendance_status' => ['required', 'in:attended,absent,excused'],
            'players.*.performance_score' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'players.*.speed_score' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'players.*.endurance_score' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'players.*.technique_score' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'players.*.discipline_score' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'players.*.coach_comment' => ['nullable', 'string'],
        ];
    }
}
