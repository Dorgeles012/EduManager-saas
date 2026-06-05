<?php

namespace App\Http\Requests\Sadmin;

use Illuminate\Foundation\Http\FormRequest;

class PlanStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],

            // Description générale (textarea côté admin) conservée, mais non utilisée dans la nouvelle logique features.
            'description' => ['nullable', 'string'],

            'features' => ['sometimes', 'array'],
            'features.*' => ['string', 'max:255'],

            'prix' => ['required', 'integer', 'min:0'],

            'type' => ['required', 'string', 'max:255', 'exists:subscription_types,type'],
            'statut' => ['required', 'in:active,inactive'],
        ];
    }

}

