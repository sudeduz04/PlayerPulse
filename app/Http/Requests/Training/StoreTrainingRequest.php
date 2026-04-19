<?php

namespace App\Http\Requests\Training;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StoreTrainingRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'team_id' => ['required', Rule::exists('teams', 'id')->whereNull('deleted_at')],
            'title' => ['required', 'string', 'max:255'],
            'training_date' => ['required', 'date'],
            'training_type' => ['required', 'string', 'max:100'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'description' => ['nullable', 'string'],
            'coach_note' => ['nullable', 'string'],
        ];
    }
}
