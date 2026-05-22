<?php

namespace App\Http\Requests\AiAnalysis;

use App\Http\Requests\ApiFormRequest;

class StoreAnalysisRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'player_id' => ['required', 'integer', 'exists:players,id'],
            'focus' => ['nullable', 'string', 'max:500'],
        ];
    }
}
