<?php

namespace App\Http\Requests\Web\Training;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTrainingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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
