<?php

namespace App\Http\Requests\Web\Player;

use Illuminate\Foundation\Http\FormRequest;

class StoreMeasurementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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
