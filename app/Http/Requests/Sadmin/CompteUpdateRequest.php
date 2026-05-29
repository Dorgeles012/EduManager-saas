<?php

namespace App\Http\Requests\Sadmin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompteUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($userId),
            ],
            'telephone' => ['nullable', 'string', 'max:50'],


            // Profile image upload
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],

            // Password update
            'password' => ['nullable', 'string', 'min:8', 'max:255', 'confirmed'],
            'password_confirmation' => ['nullable', 'string', 'min:8', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // If password is empty in the form, confirmed will fail unless we null it.
        if ($this->filled('password') === false) {
            $this->merge([
                'password' => null,
            ]);
        }
    }
}

