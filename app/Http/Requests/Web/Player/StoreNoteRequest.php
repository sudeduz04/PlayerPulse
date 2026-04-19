<?php

namespace App\Http\Requests\Web\Player;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'note_date' => ['required', 'date'],
            'note' => ['required', 'string'],
        ];
    }
}
