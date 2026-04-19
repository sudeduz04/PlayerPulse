<?php

namespace App\Http\Requests\Training;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateTrainingRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'team_id' => ['sometimes', 'required', Rule::exists('teams', 'id')->whereNull('deleted_at')],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'training_date' => ['sometimes', 'required', 'date'],
            'training_type' => ['sometimes', 'required', 'string', 'max:100'],
            'duration_minutes' => ['sometimes', 'required', 'integer', 'min:1', 'max:600'],
            'description' => ['nullable', 'string'],
            'coach_note' => ['nullable', 'string'],
        ];
    }
}
