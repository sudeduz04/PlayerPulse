<?php

namespace App\Http\Requests\Lineup;

use App\Http\Requests\ApiFormRequest;

class StoreLineupRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'match_id' => ['required', 'integer', 'exists:matches,id'],
            'formation' => ['required', 'string', 'max:20'],
            'note' => ['nullable', 'string', 'max:2000'],
            'players' => ['required', 'array', 'size:11'],
            'players.*.player_id' => ['required', 'integer', 'exists:players,id', 'distinct'],
            'players.*.position_id' => ['required', 'integer', 'exists:positions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'players.size' => 'Kadroda tam olarak 11 ilk 11 oyuncusu olmalidir.',
            'players.*.player_id.distinct' => 'Ayni oyuncu birden fazla kez secilemez.',
        ];
    }
}
