<?php

namespace App\Http\Requests\Api\Player;

use App\Http\Requests\ApiFormRequest;

class StoreNoteRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'note_date' => ['required', 'date'],
            'note' => ['required', 'string'],
        ];
    }
}
