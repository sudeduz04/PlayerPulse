<?php

namespace App\Http\Requests\SmartLineup;

use App\Http\Requests\ApiFormRequest;

class StoreSmartLineupRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'match_id' => ['required', 'integer', 'exists:matches,id'],
            'formation' => ['required', 'string', 'max:20'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
