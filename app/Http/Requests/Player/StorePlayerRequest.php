<?php

namespace App\Http\Requests\Player;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StorePlayerRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'team_id' => 'required|exists:teams,id',
            'position_id' => 'required|exists:positions,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'jersey_number' => [
                'required',
                'integer',
                'min:1',
                'max:99',
                Rule::unique('players')->where(function ($query) {
                    return $query->where('team_id', $this->team_id);
                }),
            ],
            'height' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dominant_foot' => 'required|in:left,right,both',
            'nationality' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,injured',
            'photo' => 'nullable|string|max:255',
        ];
    }
}
