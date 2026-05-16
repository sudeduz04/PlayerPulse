<?php

namespace App\Http\Requests\Api\Player;

use App\Http\Requests\ApiFormRequest;

class StoreMeasurementRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'measurement_date' => ['required', 'date'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:250'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:200'],
            'body_fat_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sprint_time' => ['nullable', 'numeric', 'min:0'],
            'agility_score' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'endurance_level' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'strength_score' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'note' => ['nullable', 'string'],
        ];
    }
}
