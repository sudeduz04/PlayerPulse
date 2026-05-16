<?php

namespace App\Http\Requests\Api\DevelopmentReport;

use App\Http\Requests\ApiFormRequest;

class StoreDevelopmentReportRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
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
