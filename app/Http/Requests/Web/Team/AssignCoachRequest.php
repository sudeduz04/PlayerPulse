<?php

namespace App\Http\Requests\Web\Team;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class AssignCoachRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $validator->errors()->has('user_id')) {
                $user = User::find($this->user_id);
                if ($user && ! $user->isRole('coach')) {
                    $validator->errors()->add('user_id', 'Seçilen kullanıcı antrenör rolüne sahip olmalıdır.');
                }
            }
        });
    }
}
