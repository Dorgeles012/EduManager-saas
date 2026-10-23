<?php

namespace App\Console\Commands;

use App\Models\Eleve;
use App\Models\Enseignant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class AuditLegacySchoolUsers extends Command
{
    protected $signature = 'users:audit-legacy-school-accounts';
    protected $description = 'Audite les anciens comptes élèves et enseignants sans supprimer ni modifier de données';

    public function handle(): int
    {
        $headers = ['User ID', 'Rôle', 'E-mail', 'Correspondance', 'Statut'];
        $rows = [];

        $legacyUsers = User::query()
            ->whereRaw('LOWER(role) IN (?, ?)', ['eleve', 'enseignant'])
            ->orderBy('id')
            ->get();

        foreach ($legacyUsers as $legacyUser) {
            $role = strtolower((string) $legacyUser->role);

            if ($role === 'eleve') {
                [$match, $status] = $this->matchEleve($legacyUser);
            } else {
                [$match, $status] = $this->matchEnseignant($legacyUser);
            }

            $rows[] = [$legacyUser->id, $role, $legacyUser->email, $match, $status];
        }

        $this->table($headers, $rows);
        $this->info("{$legacyUsers->count()} ancien(s) compte(s) inspecté(s). Aucune donnée n’a été modifiée.");

        return self::SUCCESS;
    }

    private function matchEleve(User $user): array
    {
        if (Schema::hasColumn('users', 'eleve_id') && $user->eleve_id) {
            $eleve = Eleve::query()
                ->where('tenant_id', $user->tenant_id)
                ->whereKey($user->eleve_id)
                ->first();

            return $eleve
                ? ["eleves.id={$eleve->id}", 'Correspondance exacte']
                : ["eleves.id={$user->eleve_id}", 'Référence sans élève correspondant'];
        }

        $matricule = str_ends_with((string) $user->email, '@eleve.local')
            ? substr((string) $user->email, 0, -strlen('@eleve.local'))
            : null;

        if ($matricule) {
            $candidates = Eleve::query()
                ->where('tenant_id', $user->tenant_id)
                ->where('matricule', $matricule)
                ->get(['id']);

            if ($candidates->count() === 1) {
                return ["matricule={$matricule}", 'Correspondance probable — vérifier manuellement'];
            }
            if ($candidates->count() > 1) {
                return ["matricule={$matricule}", 'Ambiguë — vérifier manuellement'];
            }
        }

        return ['—', 'Aucune correspondance confirmée'];
    }

    private function matchEnseignant(User $user): array
    {
        $candidates = Enseignant::query()
            ->where('tenant_id', $user->tenant_id)
            ->where(function ($query) use ($user) {
                if ($user->email) {
                    $query->where('email', $user->email);
                }
                if (Schema::hasColumn('enseignants', 'user_id')) {
                    $query->orWhere('user_id', $user->id);
                }
            })
            ->get(['id', 'user_id']);

        if ($candidates->count() === 1) {
            $teacher = $candidates->first();
            $exact = (int) $teacher->user_id === (int) $user->id;

            return ["enseignants.id={$teacher->id}", $exact ? 'Correspondance exacte' : 'Correspondance probable — vérifier manuellement'];
        }

        if ($candidates->count() > 1) {
            return [$candidates->pluck('id')->implode(', '), 'Ambiguë — vérifier manuellement'];
        }

        return ['—', 'Aucune correspondance confirmée'];
    }
}
