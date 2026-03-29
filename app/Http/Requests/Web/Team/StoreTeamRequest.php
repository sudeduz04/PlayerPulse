<?php

namespace App\Http\Requests\Web\Team;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'age_category' => 'required|string|max:255',
            'season' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
