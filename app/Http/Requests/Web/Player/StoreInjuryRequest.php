<?php

namespace App\Http\Requests\Web\Player;

use Illuminate\Foundation\Http\FormRequest;

class StoreInjuryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'injury_type' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:ongoing,recovered'],
            'description' => ['nullable', 'string'],
        ];
    }
}
