<?php

namespace App\Http\Requests\Team;

use App\Http\Requests\ApiFormRequest;

class UpdateTeamRequest extends ApiFormRequest
{
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
