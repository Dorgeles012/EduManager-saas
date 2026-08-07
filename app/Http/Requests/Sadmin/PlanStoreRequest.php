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

            // Durée du plan en mois (colonne `duree` de la table `plans`).
            'duree' => ['nullable', 'integer', 'min:1'],

            // Le type est rendu plus souple : on ne bloque plus la création si
            // le type n'existe pas encore dans `subscription_types`. Le contrôleur
            // le créera automatiquement au besoin (firstOrCreate).
            'type' => ['nullable', 'string', 'max:255'],
            'statut' => ['required', 'in:active,inactive'],
        ];
    }

}

