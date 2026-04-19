<?php

namespace App\Http\Requests\Match;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StoreMatchRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'team_id' => ['required', Rule::exists('teams', 'id')->whereNull('deleted_at')],
            'opponent_team' => ['required', 'string', 'max:255'],
            'match_date' => ['required', 'date'],
            'match_type' => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'result' => ['nullable', 'string', 'max:50'],
            'goals_for' => ['required', 'integer', 'min:0'],
            'goals_against' => ['required', 'integer', 'min:0'],
            'coach_note' => ['nullable', 'string'],
        ];
    }
}
