<?php

namespace App\Http\Requests\Team;

use App\Http\Requests\ApiFormRequest;
use App\Models\User;

class AssignCoachRequest extends ApiFormRequest
{
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
                    $validator->errors()->add('user_id', 'The selected user must have the coach role.');
                }
            }
        });
    }
}
