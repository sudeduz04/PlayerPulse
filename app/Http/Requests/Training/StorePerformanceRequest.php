<?php

namespace App\Http\Requests\Training;

use App\Http\Requests\ApiFormRequest;

class StorePerformanceRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'player_id' => ['required', 'exists:players,id'],
            'attendance_status' => ['required', 'in:attended,absent,excused'],
            'performance_score' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'speed_score' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'endurance_score' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'technique_score' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'discipline_score' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'coach_comment' => ['nullable', 'string'],
        ];
    }
}
