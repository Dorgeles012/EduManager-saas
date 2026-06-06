<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class PersonnelStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Middleware 'client' gère déjà le rôle.
        return true;
    }

    public function rules(): array
    {
        $clientId = auth()->id();

        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:50'],
            'email' => [
                'required',
                'email',
                'max:255',
                // Un email unique par client (évite la collision entre établissements).
                'unique:users,email,NULL,id,client_id,' . $clientId,
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // Le formulaire ne doit pas permettre de changer client_id/etablissement_id.
            // On force le rôle et la vue contrôleur les paramètrera.
        ];
    }

    public function validationData(): array
    {
        // On peut aussi nettoyer ici si besoin.
        return $this->all();
    }
}

