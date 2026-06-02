<?php

namespace App\Http\Requests\Sadmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $client = $this->route('client');
        $clientId = is_object($client) ? $client->id : $client;

        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $clientId],
            'adresse' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:actif,bloqué'],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
            'ville' => ['nullable', 'string', 'max:100'],
            'etablissement_id' => [
                'required',
                Rule::exists('etablissements', 'id')->where('tenant_id', $this->user()?->tenant_id ?? 1),
            ],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'etablissement_id.required' => 'Veuillez sélectionner un établissement.',
            'etablissement_id.exists' => "L'établissement sélectionné est invalide.",
        ];
    }
}
