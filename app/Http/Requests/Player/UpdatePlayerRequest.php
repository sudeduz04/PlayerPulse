<?php

namespace App\Http\Requests\Player;

use App\Http\Requests\ApiFormRequest;
use App\Models\Players;
use Illuminate\Validation\Rule;

class UpdatePlayerRequest extends ApiFormRequest
{
    public function rules(): array
    {
        $playerId = $this->route('player');
        $teamId = $this->team_id ?? null;

        $jerseyRule = [
            'sometimes',
            'required',
            'integer',
            'min:1',
            'max:99',
        ];

        if ($teamId) {
            $jerseyRule[] = Rule::unique('players')->where(function ($query) use ($teamId) {
                return $query->where('team_id', $teamId);
            })->ignore($playerId);
        } else {
            $jerseyRule[] = Rule::unique('players')->where(function ($query) use ($playerId) {
                $player = Players::find($playerId);

                return $query->where('team_id', $player?->team_id);
            })->ignore($playerId);
        }

        return [
            'team_id' => ['sometimes', 'required', Rule::exists('teams', 'id')->whereNull('deleted_at')],
            'position_id' => ['sometimes', 'required', Rule::exists('positions', 'id')->whereNull('deleted_at')],
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'birth_date' => 'sometimes|required|date|before:today',
            'jersey_number' => $jerseyRule,
            'height' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dominant_foot' => 'sometimes|required|in:left,right,both',
            'nationality' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,injured',
            'photo' => 'nullable|string|max:255',
        ];
    }
}
