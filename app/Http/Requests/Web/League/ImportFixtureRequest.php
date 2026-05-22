<?php

namespace App\Http\Requests\Web\League;

use Illuminate\Foundation\Http\FormRequest;

class ImportFixtureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'fixture_file' => ['nullable', 'file', 'mimes:csv,txt,xlsx,xls', 'max:5120'],
            'rows' => ['nullable', 'array'],
            'rows.*.week' => ['nullable', 'integer', 'min:1'],
            'rows.*.date' => ['nullable', 'date'],
            'rows.*.home_team' => ['nullable', 'string', 'max:255'],
            'rows.*.away_team' => ['nullable', 'string', 'max:255'],
            'rows.*.location' => ['nullable', 'string', 'max:255'],
        ];
    }
}
