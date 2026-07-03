<?php

namespace App\Http\Requests\Sadmin;

use Illuminate\Foundation\Http\FormRequest;

class EtablissementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'acronyme' => ['nullable', 'string', 'max:100'],
            'type_etablissement' => ['required', 'string', 'in:primaire,college,lycee,universite,grande_ecole'],
            'email' => ['nullable', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'adresse' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'statut' => ['nullable', 'in:active,inactive'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.image' => 'Le logo doit être une image valide.',
            'logo.mimes' => 'Le logo doit être au format JPEG, PNG, GIF ou WebP.',
            'logo.max' => 'Le logo ne doit pas dépasser 4 Mo.',
            'type_etablissement.in' => "Le type d'établissement est invalide.",
        ];
    }
}
