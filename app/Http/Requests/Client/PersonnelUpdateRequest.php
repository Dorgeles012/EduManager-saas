<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class PersonnelUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // La sécurité sur le client_id est traitée dans le contrôleur (403 si mismatch).
        return true;
    }

    public function rules(): array
    {
        $personnelId = $this->route('personnel') ?? $this->route('user') ?? null;

        // La règle unique doit exclure le personnel en cours.
        // Si le route model binding fournit un objet, on récupère son id.
        if (is_object($personnelId) && isset($personnelId->id)) {
            $personnelId = $personnelId->id;
        }

        $clientId = auth()->id();

        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:50'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $personnelId . ',id,client_id,' . $clientId,
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}

