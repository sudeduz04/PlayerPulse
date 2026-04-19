<?php

namespace App\Http\Requests\Web\Match;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'team_id' => ['sometimes', 'required', Rule::exists('teams', 'id')->whereNull('deleted_at')],
            'opponent_team' => ['sometimes', 'required', 'string', 'max:255'],
            'match_date' => ['sometimes', 'required', 'date'],
            'match_type' => ['sometimes', 'required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'result' => ['nullable', 'string', 'max:50'],
            'goals_for' => ['sometimes', 'required', 'integer', 'min:0'],
            'goals_against' => ['sometimes', 'required', 'integer', 'min:0'],
            'coach_note' => ['nullable', 'string'],
        ];
    }
}
