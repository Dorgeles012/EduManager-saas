<?php

namespace App\Http\Requests\Sadmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSystemNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return strtolower((string) $this->user()?->role) === 'sadmin';
    }

    public function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'audience' => ['required', Rule::in(['all', 'clients', 'parents', 'personnel', 'enseignants', 'users'])],
            'recipient_ids' => ['nullable', 'array'],
            'recipient_ids.*' => ['integer', 'exists:users,id'],
            'category' => ['nullable', 'string', 'max:100'],
            'priority' => ['required', Rule::in(['low', 'normal', 'urgent'])],
        ];
    }

    public function after(): array
    {
        return [function ($validator): void {
            if ($this->input('audience') === 'users' && empty($this->input('recipient_ids', []))) {
                $validator->errors()->add('recipient_ids', 'Sélectionnez au moins un destinataire.');
            }
        }];
    }
}
