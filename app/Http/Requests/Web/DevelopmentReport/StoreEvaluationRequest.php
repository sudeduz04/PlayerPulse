<?php

namespace App\Http\Requests\Web\DevelopmentReport;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'player_id' => ['required', Rule::exists('players', 'id')->whereNull('deleted_at')],
            'report_date' => ['required', 'date'],
            'technical_development' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'physical_development' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'tactical_development' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'mental_development' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'overall_score' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'strengths' => ['nullable', 'string'],
            'weaknesses' => ['nullable', 'string'],
            'recommendations' => ['nullable', 'string'],
        ];
    }
}
