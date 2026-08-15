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
            'description' => ['nullable', 'string'],
            'features' => ['sometimes', 'array'],
            'features.*' => ['string', 'max:255'],
            'prix' => ['required', 'integer', 'min:0'],
            'duration_type' => ['required', 'in:monthly,annual'],
            'school_limit' => ['nullable', 'string', 'in:1,3,unlimited'],
            'max_schools' => ['nullable', 'integer', 'min:1'],
            'is_unlimited' => ['nullable', 'boolean'],
            'type' => ['nullable', 'string', 'max:255'],
            'statut' => ['required', 'in:active,inactive'],
        ];
    }
}
