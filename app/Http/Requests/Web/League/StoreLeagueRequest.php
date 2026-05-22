<?php

namespace App\Http\Requests\Web\League;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeagueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('leagues', 'name')
                    ->where('season', $this->input('season'))
                    ->ignore($this->route('league')),
            ],
            'season' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'team_ids' => ['required', 'array', 'min:2'],
            'team_ids.*' => ['integer', 'exists:teams,id', 'distinct'],
        ];
    }
}
