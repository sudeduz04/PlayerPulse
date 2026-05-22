<?php

namespace App\Http\Requests\Web\Lineup;

use Illuminate\Foundation\Http\FormRequest;

class StoreLineupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'match_id' => ['required', 'integer', 'exists:matches,id'],
            'formation' => ['required', 'string', 'max:20'],
            'note' => ['nullable', 'string', 'max:2000'],
            'players' => ['required', 'array', 'size:11'],
            'players.*.player_id' => ['required', 'integer', 'exists:players,id', 'distinct'],
            'players.*.position_id' => ['required', 'integer', 'exists:positions,id'],
            'players.*.slot_key' => ['nullable', 'string', 'max:20'],
            'players.*.field_x' => ['nullable', 'integer', 'between:0,100'],
            'players.*.field_y' => ['nullable', 'integer', 'between:0,100'],
        ];
    }

    public function messages(): array
    {
        return [
            'players.size' => 'Kadroda tam olarak 11 ilk 11 oyuncusu olmalı.',
            'players.*.player_id.distinct' => 'Aynı oyuncu birden fazla kez seçilemez.',
        ];
    }
}
