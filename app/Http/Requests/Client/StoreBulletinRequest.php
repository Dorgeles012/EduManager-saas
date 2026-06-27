<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulletinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'eleve_id' => ['required', 'integer'],
            'etablissement_id' => ['required', 'integer'],
            'annee_academique_id' => ['required', 'integer'],
            'classe_id' => ['nullable', 'integer'],
            'trimestre' => ['required', 'string', 'max:10'],

            'total_heures' => ['nullable', 'numeric', 'min:0'],
            'absences' => ['nullable', 'integer', 'min:0'],
            'rang' => ['nullable', 'integer', 'min:0'],

            'resultat_classe' => ['nullable', 'string', 'max:255'],
            'decision' => ['nullable', 'string', 'max:255'],
            'observation_conseil' => ['nullable', 'string', 'max:1000'],
            'date' => ['nullable', 'date'],

            'signature_professeur_principal' => ['nullable', 'string', 'max:255'],
            'signature_directeur' => ['nullable', 'string', 'max:255'],
            'distinctions' => ['nullable', 'array'],
            'distinctions.*' => ['in:honneur,encouragement,felicitations,avertissement,blame'],

            'disciplines' => ['required', 'array', 'min:1'],
            'disciplines.*.discipline' => ['required', 'string', 'max:255'],
            'disciplines.*.moyenne' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'disciplines.*.coefficient' => ['required', 'numeric', 'min:0.1', 'max:10'],
            'disciplines.*.moyenne_coefficient' => ['nullable', 'numeric'],
            'disciplines.*.rang' => ['nullable', 'integer', 'min:0'],
            'disciplines.*.appréciation' => ['nullable', 'string', 'max:255'],
            'disciplines.*.professeur' => ['nullable', 'string', 'max:255'],
            'disciplines.*.signature' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'eleve_id.required' => 'Veuillez sélectionner un élève.',
            'annee_academique_id.required' => 'Veuillez sélectionner une année académique.',
            'trimestre.required' => 'Veuillez sélectionner une période.',
            'disciplines.required' => 'Veuillez renseigner au moins une discipline.',
        ];
    }
}

