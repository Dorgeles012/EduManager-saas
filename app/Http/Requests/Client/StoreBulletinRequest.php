<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBulletinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'eleve_id' => ['required', Rule::exists('eleves', 'id')->where('tenant_id', $this->user()->tenant_id)],
            'etablissement_id' => ['required', Rule::exists('etablissements', 'id')->where('tenant_id', $this->user()->tenant_id)],
            'annee_academique_id' => ['required', Rule::exists('annee_academique', 'id')->where('tenant_id', $this->user()->tenant_id)],
            'classe_id' => ['nullable', Rule::exists('classes', 'id')->where('tenant_id', $this->user()->tenant_id)],
            'serie_id' => ['nullable', Rule::exists('series', 'id')->where('tenant_id', $this->user()->tenant_id)],
            'trimestre' => ['required', Rule::in(['t1', 't2', 't3', 's1', 's2', 'an'])],

            'total_heures' => ['nullable', 'numeric', 'min:0'],
            'absences' => ['nullable', 'integer', 'min:0'],
            'rang' => ['nullable', 'integer', 'min:0'],

            'resultat_classe' => ['nullable', 'string', 'max:255'],
            'signature_professeur_principal' => ['nullable', 'string', 'max:255'],
            'distinctions' => ['nullable', 'array'],
            'distinctions.*' => ['in:honneur,encouragement,felicitations,avertissement,blame'],

            'disciplines' => ['required', 'array', 'min:1'],
            'disciplines.*.matiere_id' => ['nullable', 'integer'],
            'disciplines.*.discipline' => ['required', 'string', 'max:255'],
            'disciplines.*.interrogation' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'disciplines.*.devoir' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'disciplines.*.composition' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'disciplines.*.moyenne' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'disciplines.*.coefficient' => ['required', 'numeric', 'min:1', 'max:100'],
            'disciplines.*.moyenne_coefficient' => ['nullable', 'numeric'],
            'disciplines.*.rang' => ['nullable', 'integer', 'min:0'],
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

