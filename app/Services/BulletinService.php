<?php

namespace App\Services;

use App\Models\AnneeAcademique;
use App\Models\Bulletin;
use App\Models\BulletinDiscipline;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Enseignant;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BulletinService
{
    /**
     * Source unique de vérité pour l'appréciation d'une note (sur 20).
     */
    public static function noteAppreciation(?float $note): string
    {
        if ($note === null) {
            return '—';
        }

        return match (true) {
            $note >= 16 => 'Très Bien',
            $note >= 14 => 'Bien',
            $note >= 12 => 'Assez Bien',
            $note >= 10 => 'Passable',
            default     => 'Insuffisant',
        };
    }

    /**
     * Returns the official mention and council observation for an average out of 20.
     */
    public function evaluation(?float $moyenne): array
    {
        if ($moyenne === null) {
            return ['mention' => null, 'observation' => null];
        }

        return match (true) {
            $moyenne >= 19 => ['mention' => 'Excellent', 'observation' => 'Travail exceptionnel. Résultats remarquables. Félicitations du jury. Continuez ainsi.'],
            $moyenne >= 18 => ['mention' => 'Excellent', 'observation' => 'Excellent travail. Très grande maîtrise des apprentissages. Toutes nos félicitations.'],
            $moyenne >= 17 => ['mention' => 'Très Bien', 'observation' => 'Très bon travail. Élève sérieux, appliqué et régulier. Félicitations.'],
            $moyenne >= 16 => ['mention' => 'Très Bien', 'observation' => 'Très bons résultats. Continuez vos efforts.'],
            $moyenne >= 15 => ['mention' => 'Bien', 'observation' => 'Bon travail. Ensemble satisfaisant. Encourageant pour la suite.'],
            $moyenne >= 14 => ['mention' => 'Assez Bien', 'observation' => 'Bons résultats. Quelques efforts supplémentaires permettront de progresser davantage.'],
            $moyenne >= 13 => ['mention' => 'Assez Bien', 'observation' => 'Travail satisfaisant. Continuez avec plus de régularité.'],
            $moyenne >= 12 => ['mention' => 'Passable', 'observation' => 'Résultats corrects. Des efforts sont encore attendus.'],
            $moyenne >= 11 => ['mention' => 'Passable', 'observation' => 'Ensemble acceptable mais irrégulier. Il faut travailler davantage.'],
            $moyenne >= 10 => ['mention' => 'Passable', 'observation' => 'Moyenne acquise de justesse. Les efforts doivent être poursuivis.'],
            $moyenne >= 9 => ['mention' => 'Insuffisant', 'observation' => 'Résultats insuffisants. Un travail plus sérieux est indispensable.'],
            $moyenne >= 8 => ['mention' => 'Faible', 'observation' => 'Travail faible. Il est nécessaire de fournir davantage d’efforts.'],
            $moyenne >= 7 => ['mention' => 'Très Faible', 'observation' => 'Résultats très insuffisants. Réaction rapide attendue.'],
            $moyenne >= 5 => ['mention' => 'Très Faible', 'observation' => 'Grandes difficultés. Travail insuffisant. Beaucoup plus d’investissement est nécessaire.'],
            default => ['mention' => 'Très Insuffisant', 'observation' => 'Résultats très préoccupants. Une remise au travail est indispensable.'],
        };
    }

    public function decision(?float $moyenne): ?string
    {
        return $moyenne === null ? null : ($moyenne >= 10 ? 'Admis(e)' : 'Refusé(e)');
    }

    /**
     * Calcule la moyenne des notes d'un élève pour une matière sur une période donnée.
     */
    public function calculerMoyenneMatiere(
        int $eleveId,
        int $matiereId,
        string $periode,
        ?int $anneeId = null,
        ?string $onlyStatus = Note::STATUT_PUBLIE
    ): ?float {
        $query = Note::query()
            ->where('eleve_id', $eleveId)
            ->where('matiere_id', $matiereId)
            ->where('periode', $periode);

        if ($anneeId) {
            $query->where('annee_academique_id', $anneeId);
        }

        if ($onlyStatus) {
            $query->where('statut', $onlyStatus);
        }

        $notes = $query->pluck('note');

        if ($notes->isEmpty()) {
            return null;
        }

        return round((float) $notes->avg(), 2);
    }

    /**
     * Calcule le bilan complet d'un élève (moyennes par matière + moyenne générale pondérée).
     */
    public function calculerBilanEleve(
        Eleve|int $eleve,
        string $periode,
        ?int $anneeId = null,
        ?string $onlyStatus = Note::STATUT_PUBLIE
    ): array {
        if (is_int($eleve)) {
            $eleve = Eleve::with(['classe', 'serie.matieres'])->findOrFail($eleve);
        } else {
            $eleve->loadMissing(['classe', 'serie.matieres']);
        }

        $tenantId = $eleve->tenant_id;
        $classeId = $eleve->classe_id;

        // Récupérer les matières configurées pour cet élève (via sa série ou sa classe)
        $configuredMatieres = collect();
        if ($eleve->serie) {
            $configuredMatieres = $eleve->serie->matieres()
                ->where('matieres.tenant_id', $tenantId)
                ->orderBy('matieres.nom')
                ->get();
        }

        if ($configuredMatieres->isEmpty()) {
            $configuredMatieres = Matiere::where('tenant_id', $tenantId)->orderBy('nom')->get();
        }

        // Récupérer toutes les notes de l'élève pour la période
        $notesQuery = Note::query()
            ->with('enseignant')
            ->where('tenant_id', $tenantId)
            ->where('eleve_id', $eleve->id)
            ->where('periode', $periode);

        if ($anneeId) {
            $notesQuery->where('annee_academique_id', $anneeId);
        }

        if ($onlyStatus) {
            $notesQuery->where('statut', $onlyStatus);
        }

        $allNotes = $notesQuery->orderBy('created_at')->get()->groupBy('matiere_id');

        $disciplines = [];
        $totalCoef = 0.0;
        $totalPoints = 0.0;

        foreach ($configuredMatieres as $matiere) {
            $notesForMatiere = $allNotes->get($matiere->id, collect());
            $coef = (float) ($matiere->pivot->coefficient ?? $matiere->coefficient ?? 1);

            $moyenne = $notesForMatiere->isNotEmpty()
                ? round((float) $notesForMatiere->avg('note'), 2)
                : null;

            $moyenneCoef = null;
            if ($moyenne !== null && $coef > 0) {
                $moyenneCoef = round($moyenne * $coef, 2);
                $totalCoef += $coef;
                $totalPoints += $moyenneCoef;
            }

            // Déterminer le nom de l'enseignant (priorité à l'enseignant ayant noté)
            $firstNote = $notesForMatiere->first();
            $enseignant = $firstNote?->enseignant ?? $matiere->enseignants()->first();
            $enseignantName = $enseignant
                ? trim(($enseignant->prenoms ?? '').' '.($enseignant->nom ?? ''))
                : null;

            // Extraire les évaluations individuelles (interrogations, devoirs, compositions)
            $interros = $notesForMatiere->where('type_evaluation', Note::TYPE_INTERROGATION)->pluck('note');
            $devoirs = $notesForMatiere->where('type_evaluation', Note::TYPE_DEVOIR)->pluck('note');
            $compositions = $notesForMatiere->where('type_evaluation', Note::TYPE_COMPOSITION)->pluck('note');

            $disciplines[] = [
                'matiere_id' => $matiere->id,
                'discipline' => $matiere->nom,
                'interrogation' => $interros->isNotEmpty() ? round((float) $interros->avg(), 2) : null,
                'devoir' => $devoirs->isNotEmpty() ? round((float) $devoirs->avg(), 2) : null,
                'composition' => $compositions->isNotEmpty() ? round((float) $compositions->avg(), 2) : null,
                'notes_list' => $notesForMatiere->map(fn (Note $n) => [
                    'id' => $n->id,
                    'titre' => $n->titre_evaluation ?: (Note::TYPES[$n->type_evaluation] ?? 'Note'),
                    'type' => $n->type_evaluation,
                    'note' => (float) $n->note,
                    'statut' => $n->statut,
                ])->values()->all(),
                'moyenne' => $moyenne,
                'coefficient' => $coef,
                'moyenne_coefficient' => $moyenneCoef,
                'rang' => 0,
                'mention' => $this->evaluation($moyenne)['mention'],
                'professeur' => $enseignantName,
                'signature' => null,
            ];
        }

        $moyenneGenerale = $totalCoef > 0 ? round($totalPoints / $totalCoef, 2) : null;
        $eval = $this->evaluation($moyenneGenerale);

        return [
            'eleve' => $eleve,
            'disciplines' => $disciplines,
            'total_coefficients' => round($totalCoef, 2),
            'total_points' => round($totalPoints, 2),
            'moyenne_generale' => $moyenneGenerale,
            'mention' => $eval['mention'],
            'decision' => $this->decision($moyenneGenerale),
            'observation_conseil' => $eval['observation'],
        ];
    }

    /**
     * Calcule le rang de chaque élève d'une classe pour une période donnée.
     * Retourne un tableau associatif [eleve_id => rang].
     */
    public function calculerRangsClasse(
        int $classeId,
        string $periode,
        ?int $anneeId = null,
        ?string $onlyStatus = Note::STATUT_PUBLIE
    ): array {
        $eleves = Eleve::where('classe_id', $classeId)->get();
        $bilans = [];

        foreach ($eleves as $eleve) {
            $bilan = $this->calculerBilanEleve($eleve, $periode, $anneeId, $onlyStatus);
            if ($bilan['moyenne_generale'] !== null) {
                $bilans[$eleve->id] = (float) $bilan['moyenne_generale'];
            }
        }

        // Tri décroissant des moyennes
        arsort($bilans, SORT_NUMERIC);

        $rangs = [];
        $currentRank = 0;
        $previousMoyenne = null;
        $index = 0;

        foreach ($bilans as $eleveId => $moyenne) {
            $index++;
            if ($previousMoyenne === null || $moyenne !== $previousMoyenne) {
                $currentRank = $index;
                $previousMoyenne = $moyenne;
            }
            $rangs[$eleveId] = $currentRank;
        }

        return $rangs;
    }

    /**
     * Synchronise et publie automatiquement les bulletins scolaires d'une classe
     * à partir des notes validées et publiées.
     */
    public function synchroniserEtPublierBulletins(
        int $tenantId,
        int $classeId,
        string $periode,
        int $anneeId,
        ?int $userId = null
    ): int {
        $classe = Classe::with(['eleves'])->where('tenant_id', $tenantId)->findOrFail($classeId);
        $rangs = $this->calculerRangsClasse($classeId, $periode, $anneeId, Note::STATUT_PUBLIE);
        $publishedCount = 0;

        foreach ($classe->eleves as $eleve) {
            $bilan = $this->calculerBilanEleve($eleve, $periode, $anneeId, Note::STATUT_PUBLIE);

            // Si aucune note ou moyenne n'est calculée pour cet élève, on passe
            if ($bilan['moyenne_generale'] === null && empty($bilan['disciplines'])) {
                continue;
            }

            $rang = $rangs[$eleve->id] ?? 0;

            $bulletin = Bulletin::updateOrCreate([
                'tenant_id' => $tenantId,
                'eleve_id' => $eleve->id,
                'annee_academique_id' => $anneeId,
                'trimestre' => $periode,
            ], [
                'etablissement_id' => $eleve->etablissement_id,
                'classe_id' => $classeId,
                'total_heures' => 0,
                'absences' => 0,
                'rang' => $rang,
                'moyenne_generale' => $bilan['moyenne_generale'],
                'mention' => $bilan['mention'],
                'total_coefficients' => $bilan['total_coefficients'],
                'total_points' => $bilan['total_points'],
                'decision' => $bilan['decision'],
                'observation_conseil' => $bilan['observation_conseil'],
                'statut' => Bulletin::STATUT_PUBLIE,
                'publie_le' => Carbon::now(),
                'publie_par_id' => $userId,
                'date' => Carbon::today(),
                'signature_directeur' => 'La Direction',
            ]);

            // Synchronisation des disciplines
            $bulletin->disciplines()->delete();

            foreach ($bilan['disciplines'] as $disc) {
                if ($disc['moyenne'] !== null || !empty($disc['notes_list'])) {
                    BulletinDiscipline::create([
                        'tenant_id' => $tenantId,
                        'bulletin_id' => $bulletin->id,
                        'matiere_id' => $disc['matiere_id'],
                        'discipline' => $disc['discipline'],
                        'interrogation' => $disc['interrogation'],
                        'devoir' => $disc['devoir'],
                        'composition' => $disc['composition'],
                        'moyenne' => $disc['moyenne'],
                        'coefficient' => $disc['coefficient'],
                        'moyenne_coefficient' => $disc['moyenne_coefficient'],
                        'rang' => $disc['rang'] ?? 0,
                        'mention' => $disc['mention'],
                        'professeur' => $disc['professeur'],
                        'signature' => $disc['signature'],
                    ]);
                }
            }

            $publishedCount++;
        }

        return $publishedCount;
    }

    public function recalculerBulletinEnAttente(
        int $tenantId,
        int $classeId,
        int $eleveId,
        string $periode,
        ?int $anneeId
    ): ?Bulletin {
        if (!$anneeId) {
            return null;
        }

        $eleve = Eleve::with(['classe', 'serie.matieres'])
            ->where('tenant_id', $tenantId)
            ->where('classe_id', $classeId)
            ->find($eleveId);

        if (!$eleve) {
            return null;
        }

        $bilan = $this->calculerBilanEleve($eleve, $periode, $anneeId, Note::STATUT_PUBLIE);
        $rangs = $this->calculerRangsClasse($classeId, $periode, $anneeId, Note::STATUT_PUBLIE);
        $bulletin = Bulletin::updateOrCreate([
            'tenant_id' => $tenantId,
            'eleve_id' => $eleveId,
            'annee_academique_id' => $anneeId,
            'trimestre' => $periode,
        ], [
            'etablissement_id' => $eleve->etablissement_id,
            'classe_id' => $classeId,
            'total_heures' => 0,
            'absences' => 0,
            'rang' => $rangs[$eleveId] ?? 0,
            'moyenne_generale' => $bilan['moyenne_generale'],
            'mention' => $bilan['mention'],
            'total_coefficients' => $bilan['total_coefficients'],
            'total_points' => $bilan['total_points'],
            'decision' => $bilan['decision'],
            'observation_conseil' => $bilan['observation_conseil'],
            'statut' => Bulletin::STATUT_EN_ATTENTE,
            'publie_le' => null,
            'publie_par_id' => null,
            'date' => Carbon::today(),
        ]);

        $bulletin->disciplines()->delete();
        foreach ($bilan['disciplines'] as $discipline) {
            if ($discipline['moyenne'] !== null || !empty($discipline['notes_list'])) {
                BulletinDiscipline::create([
                    'tenant_id' => $tenantId,
                    'bulletin_id' => $bulletin->id,
                    'matiere_id' => $discipline['matiere_id'],
                    'discipline' => $discipline['discipline'],
                    'interrogation' => $discipline['interrogation'],
                    'devoir' => $discipline['devoir'],
                    'composition' => $discipline['composition'],
                    'moyenne' => $discipline['moyenne'],
                    'coefficient' => $discipline['coefficient'],
                    'moyenne_coefficient' => $discipline['moyenne_coefficient'],
                    'rang' => 0,
                    'mention' => $discipline['mention'],
                    'professeur' => $discipline['professeur'],
                    'signature' => null,
                ]);
            }
        }

        return $bulletin;
    }
}
