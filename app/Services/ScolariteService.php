<?php

namespace App\Services;

use App\Models\AnneeAcademique;
use App\Models\Eleve;
use App\Models\FraisScolarite;
use App\Models\Niveau;
use App\Models\Scolarite;
use App\Models\Versement;
use Illuminate\Database\Eloquent\Builder;

/**
 * Service central pour le calcul de la situation de scolarité d'un élève.
 *
 * Chaîne logique :
 *   Élève → Niveau (niveau_id ou classe.niveau) → Frais configurés (tenant + établissement + niveau + année)
 *   → Scolarité (payé / reste) + Versements (historique réel)
 *
 * Respecte les règles tenant_id / etablissement_id et ne crée jamais de nouvelle
 * table : il s'appuie sur frais_scolarite, scolarites et versements.
 */
class ScolariteService
{
    /**
     * Détermine le niveau d'un élève.
     */
    protected function niveauPour(Eleve $eleve): ?Niveau
    {
        if ($eleve->relationLoaded('niveau') && $eleve->niveau) {
            return $eleve->niveau;
        }

        if ($eleve->niveau_id) {
            $niveau = $eleve->niveau()->first();
            if ($niveau) {
                return $niveau;
            }
        }

        return $eleve->classe?->niveau;
    }

    /**
     * Résout l'année académique active pour un tenant (et établissement).
     */
    protected function anneeActive(int $tenantId, ?int $etablissementId = null): ?AnneeAcademique
    {
        return AnneeAcademique::query()
            ->where('tenant_id', $tenantId)
            ->when($etablissementId, fn (Builder $q) => $q->where('etablissement_id', $etablissementId))
            ->where('statut', 'active')
            ->orderByDesc('date_debut')
            ->first();
    }

    /**
     * Récupère les frais de scolarité configurés pour un élève.
     */
    public function fraisPour(Eleve $eleve, ?int $anneeAcademiqueId = null): ?FraisScolarite
    {
        $niveau = $this->niveauPour($eleve);
        if (! $niveau) {
            return null;
        }

        $tenantId = $eleve->tenant_id;
        $etablissementId = $eleve->etablissement_id;

        $anneeId = $anneeAcademiqueId;
        if (! $anneeId) {
            $annee = $this->anneeActive($tenantId, $etablissementId);
            $anneeId = $annee?->id;
        }

        return FraisScolarite::query()
            ->where('tenant_id', $tenantId)
            ->where('niveau_id', $niveau->id)
            ->when($etablissementId, fn (Builder $q) => $q->where('etablissement_id', $etablissementId))
            ->when($anneeId, fn (Builder $q) => $q->where('annee_academique_id', $anneeId))
            ->latest()
            ->first();
    }

    /**
     * Obtient (sans créer) la scolarité de l'élève pour une année scolaire donnée.
     */
    public function scolaritePour(Eleve $eleve, ?string $anneeScolaire = null): ?Scolarite
    {
        $query = Scolarite::query()
            ->where('tenant_id', $eleve->tenant_id)
            ->where('eleve_id', $eleve->id);

        if ($anneeScolaire) {
            $query->where('annee_scolaire', $anneeScolaire);
        }

        return $query->latest()->first();
    }

    /**
     * Calcule la situation complète de scolarité d'un élève.
     *
     * @param  Eleve  $eleve  Élève
     * @param  string|null  $anneeScolaire  Année scolaire ciblée (ex: "2026-2027")
     * @return array{
     *   eleve: Eleve, niveau: ?Niveau, classe: mixed, serie: mixed, etablissement: mixed,
     *   annee: ?AnneeAcademique, frais: ?FraisScolarite, montant_total: int,
     *   montant_paye: int, reste: int, statut: string, pourcentage: int,
     *   versements: \Illuminate\Support\Collection
     * }
     */
    public function situation(Eleve $eleve, ?string $anneeScolaire = null): array
    {
        $niveau = $this->niveauPour($eleve);
        $annee = $this->anneeActive($eleve->tenant_id, $eleve->etablissement_id);
        $frais = $this->fraisPour($eleve, $annee?->id);

        $anneeScolaire = $anneeScolaire ?? $annee?->libelle ?? now()->year . '-' . now()->addYear()->year;

        $montantTotal = $frais ? (int) $frais->montant_total : 0;

        $scolarite = $this->scolaritePour($eleve, $anneeScolaire);

        // Total des versements réellement enregistrés pour cette scolarité
        $versements = $scolarite
            ? Versement::with('scolarite')
                ->where('tenant_id', $eleve->tenant_id)
                ->where('scolarite_id', $scolarite->id)
                ->latest()
                ->get()
            : collect();

        $montantPaye = $versements->sum('montant');

        $reste = max($montantTotal - $montantPaye, 0);

        $statut = match (true) {
            $montantPaye <= 0                => 'impaye',
            $montantPaye < $montantTotal     => 'partiel',
            default                          => 'paye',
        };

        $pourcentage = $montantTotal > 0
            ? (int) round(\min($montantPaye / $montantTotal, 1) * 100)
            : 0;

        return [
            'eleve' => $eleve,
            'niveau' => $niveau,
            'classe' => $eleve->classe,
            'serie' => $eleve->serie,
            'etablissement' => $eleve->etablissement,
            'annee' => $annee,
            'annee_scolaire' => $anneeScolaire,
            'frais' => $frais,
            'montant_total' => $montantTotal,
            'montant_paye' => $montantPaye,
            'reste' => $reste,
            'statut' => $statut,
            'pourcentage' => $pourcentage,
            'versements' => $versements,
        ];
    }
}

