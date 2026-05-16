<?php

namespace App\Http\Requests\Api\Player;

use App\Http\Requests\ApiFormRequest;

class StoreInjuryRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'injury_type' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:ongoing,recovered'],
            'description' => ['nullable', 'string'],
        ];
    }
}
