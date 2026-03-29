<?php

namespace App\Http\Requests\Web\Team;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'age_category' => 'sometimes|required|string|max:255',
            'season' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
