<?php

namespace App\Http\Requests\Match;

use App\Http\Requests\ApiFormRequest;

class StoreMatchStatsRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'player_id' => ['required', 'exists:players,id'],
            'minutes_played' => ['required', 'integer', 'min:0', 'max:200'],
            'is_starting' => ['nullable', 'boolean'],
            'goals' => ['required', 'integer', 'min:0'],
            'assists' => ['required', 'integer', 'min:0'],
            'shots' => ['required', 'integer', 'min:0'],
            'successful_passes' => ['required', 'integer', 'min:0'],
            'pass_accuracy' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tackles' => ['required', 'integer', 'min:0'],
            'interceptions' => ['required', 'integer', 'min:0'],
            'dribbles' => ['required', 'integer', 'min:0'],
            'fouls' => ['required', 'integer', 'min:0'],
            'yellow_cards' => ['required', 'integer', 'min:0', 'max:2'],
            'red_cards' => ['required', 'integer', 'min:0', 'max:1'],
            'match_rating' => ['nullable', 'numeric', 'min:0', 'max:10'],
        ];
    }
}
