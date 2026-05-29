<?php

namespace App\Http\Requests\Sadmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ComptePasswordUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8', 'max:255'],

        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->user();

            if (!$user || !\Illuminate\Support\Facades\Hash::check($this->input('current_password'), $user->password)) {
                $validator->errors()->add('current_password', 'Mot de passe actuel incorrect.');
            }
        });
    }
}

