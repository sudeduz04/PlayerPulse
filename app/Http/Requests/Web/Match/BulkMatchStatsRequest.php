<?php

namespace App\Http\Requests\Web\Match;

use Illuminate\Foundation\Http\FormRequest;

class BulkMatchStatsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'players' => ['required', 'array', 'min:1'],
            'players.*.player_id' => ['required', 'exists:players,id'],
            'players.*.minutes_played' => ['required', 'integer', 'min:0', 'max:200'],
            'players.*.is_starting' => ['nullable', 'boolean'],
            'players.*.goals' => ['required', 'integer', 'min:0'],
            'players.*.assists' => ['required', 'integer', 'min:0'],
            'players.*.shots' => ['required', 'integer', 'min:0'],
            'players.*.successful_passes' => ['required', 'integer', 'min:0'],
            'players.*.pass_accuracy' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'players.*.tackles' => ['required', 'integer', 'min:0'],
            'players.*.interceptions' => ['required', 'integer', 'min:0'],
            'players.*.dribbles' => ['required', 'integer', 'min:0'],
            'players.*.fouls' => ['required', 'integer', 'min:0'],
            'players.*.yellow_cards' => ['required', 'integer', 'min:0', 'max:2'],
            'players.*.red_cards' => ['required', 'integer', 'min:0', 'max:1'],
            'players.*.match_rating' => ['nullable', 'numeric', 'min:0', 'max:10'],
        ];
    }
}
