<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        // Helper: map statut
        $mapStatut = function (?string $statut): string {
            return match (strtolower((string) $statut)) {
                'actif', 'active' => 'active',
                'bloqué', 'bloque', 'blocked' => 'blocked',
                'inactive', 'inactif' => 'inactive',
                default => 'active',
            };
        };

        // 1) clients -> users (role=CLIENT)
        if (Schema::hasTable('clients')) {
            $rows = DB::table('clients')->get([
                'tenant_id',
                'nom',
                'prenom',
                'email',
                'telephone',
                'password',
                'photo as image',
                'status as statut',
                'created_at',
                'updated_at',
            ]);

            foreach ($rows as $row) {
                DB::table('users')->updateOrInsert(
                    ['email' => $row->email],
                    [
                        'tenant_id' => $row->tenant_id ?? 1,
                        'nom' => $row->nom,
                        'prenom' => $row->prenom,
                        'telephone' => $row->telephone,
                        'password' => $row->password,
                        'image' => $row->image,
                        'role' => 'CLIENT',
                        'statut' => $mapStatut($row->statut),
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ]
                );
            }
        }

        // 2) personnel -> users (role=PERSONNEL)
        if (Schema::hasTable('personnel')) {
            $rows = DB::table('personnel')->whereNotNull('email')->get([
                'tenant_id',
                'nom',
                'prenom',
                'email',
                'telephone',
                'password',
                DB::raw('NULL as image'),
                'statut as statut',
                'created_at',
                'updated_at',
            ]);

            foreach ($rows as $row) {
                DB::table('users')->updateOrInsert(
                    ['email' => $row->email],
                    [
                        'tenant_id' => $row->tenant_id ?? 1,
                        'nom' => $row->nom,
                        'prenom' => $row->prenom,
                        'telephone' => $row->telephone,
                        'password' => $row->password,
                        'image' => $row->image,
                        'role' => 'PERSONNEL',
                        'statut' => $mapStatut($row->statut),
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ]
                );
            }
        }

        // 3) parents -> users (role=PARENT)
        if (Schema::hasTable('parents')) {
            $rows = DB::table('parents')->whereNotNull('email')->get([
                'tenant_id',
                'nom',
                'prenom',
                'email',
                'telephone',
                'created_at',
                'updated_at',
            ]);

            foreach ($rows as $row) {
                DB::table('users')->updateOrInsert(
                    ['email' => $row->email],
                    [
                        'tenant_id' => $row->tenant_id ?? 1,
                        'nom' => $row->nom,
                        'prenom' => $row->prenom,
                        'telephone' => $row->telephone,
                        // ne pas écraser avec NULL (si users existe déjà)
                        'password' => DB::raw("COALESCE((SELECT password FROM users WHERE email = '{$row->email}' LIMIT 1), '')"),
                        'image' => null,
                        'role' => 'PARENT',
                        'statut' => 'active',
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ]
                );

                // si password vide, on met une valeur temporaire (sinon login/reset peut casser)
                DB::table('users')->where('email', $row->email)
                    ->where(function ($q) {
                        $q->whereNull('password')->orWhere('password', '');
                    })
                    ->update(['password' => DB::raw("password('')")]);
            }
        }

        // 4) enseignants -> users (role=ENSEIGNANT)
        if (Schema::hasTable('enseignants')) {
            $rows = DB::table('enseignants')->whereNotNull('email')->get([
                'tenant_id',
                'nom',
                DB::raw('prenoms as prenom'),
                'email',
                'telephone',
                'password',
                DB::raw('NULL as image'),
                'statut as statut',
                'created_at',
                'updated_at',
            ]);

            foreach ($rows as $row) {
                DB::table('users')->updateOrInsert(
                    ['email' => $row->email],
                    [
                        'tenant_id' => $row->tenant_id ?? 1,
                        'nom' => $row->nom,
                        'prenom' => $row->prenom,
                        'telephone' => $row->telephone,
                        'password' => $row->password,
                        'image' => $row->image,
                        'role' => 'ENSEIGNANT',
                        'statut' => $mapStatut($row->statut),
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        // volontairement vide
    }
};

