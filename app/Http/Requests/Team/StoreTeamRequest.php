<?php

namespace App\Http\Requests\Team;

use App\Http\Requests\ApiFormRequest;

class StoreTeamRequest extends ApiFormRequest
{
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
