<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\{AnneeAcademique, Classe, EmploiTemps, EmploiTempsSlot, Enseignant, Etablissement};
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Gestion de l'emploi du temps PAR ENSEIGNANT pour le module Personnel.
 * Même logique que Client\EmploiTempsController, adapté au format JSON
 * envoyé par la vue personnel/enseignants/emploi-temps-create.blade.php.
 *
 * Format JSON attendu :
 *   slots : [ {key, start, end}, ... ]
 *   cells : [ {day, slot_key, classe_id, matiere_id, salle}, ... ]
 */
class PersonnelEnseignantEmploiTempsController extends Controller
{
    private const DAYS = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];

    private const DEFAULT_SLOTS = [
        ['key' => 'slot-1', 'start' => '07:00', 'end' => '07:55'],
        ['key' => 'slot-2', 'start' => '07:55', 'end' => '08:50'],
        ['key' => 'slot-3', 'start' => '08:50', 'end' => '09:45'],
        ['break' => 'Récréation'],
        ['key' => 'slot-4', 'start' => '10:00', 'end' => '10:55'],
        ['key' => 'slot-5', 'start' => '10:55', 'end' => '11:50'],
        ['break' => 'Interclasse'],
        ['key' => 'slot-6', 'start' => '14:00', 'end' => '15:00'],
        ['key' => 'slot-7', 'start' => '15:00', 'end' => '16:00'],
        ['key' => 'slot-8', 'start' => '16:00', 'end' => '17:00'],
        ['key' => 'slot-9', 'start' => '17:00', 'end' => '18:00'],
    ];

    // ─── Actions publiques ───────────────────────────────────────────────────

    public function create(Request $request, Enseignant $enseignant)
    {
        $this->guardTeacher($enseignant, $request);
        abort_if($this->hasSchedule($enseignant, $request), 409, 'Cet enseignant possède déjà un emploi du temps.');
        return $this->form($request, $enseignant, false);
    }

    public function edit(Request $request, Enseignant $enseignant)
    {
        $this->guardTeacher($enseignant, $request);
        return $this->form($request, $enseignant, true);
    }

    public function show(Request $request, Enseignant $enseignant)
    {
        $this->guardTeacher($enseignant, $request);
        return view('personnel.enseignants.emploi-temps-show', $this->scheduleData($request, $enseignant));
    }

    public function exists(Request $request, Enseignant $enseignant)
    {
        $this->guardTeacher($enseignant, $request);
        return response()->json(['exists' => $this->hasSchedule($enseignant, $request)]);
    }

    public function storeTeacherSchedule(Request $request, Enseignant $enseignant)
    {
        $this->guardTeacher($enseignant, $request);
        if ($this->hasSchedule($enseignant, $request)) {
            throw ValidationException::withMessages(['schedule' => 'Cet enseignant possède déjà un emploi du temps. Utilisez la modification.']);
        }
        $this->saveSchedule($request, $enseignant);
        return response()->json([
            'message'  => 'Emploi du temps créé avec succès.',
            'redirect' => route('personnel.enseignants.emploi-temps.show', $enseignant),
        ]);
    }

    public function updateTeacherSchedule(Request $request, Enseignant $enseignant)
    {
        $this->guardTeacher($enseignant, $request);
        $this->saveSchedule($request, $enseignant);
        return response()->json([
            'message'  => 'Emploi du temps mis à jour avec succès.',
            'redirect' => route('personnel.enseignants.emploi-temps.show', $enseignant),
        ]);
    }

    public function destroyTeacherSchedule(Request $request, Enseignant $enseignant)
    {
        $this->guardTeacher($enseignant, $request);
        $deleted = DB::transaction(function () use ($request, $enseignant) {
            EmploiTempsSlot::where('tenant_id', $request->user()->tenant_id)
                ->where('enseignant_id', $enseignant->id)
                ->delete();
            return EmploiTemps::where('tenant_id', $request->user()->tenant_id)
                ->where('enseignant_id', $enseignant->id)
                ->delete();
        });

        if (! $deleted) {
            return response()->json(['message' => 'Aucun emploi du temps à supprimer.'], 404);
        }
        return response()->json(['message' => 'Emploi du temps supprimé avec succès.']);
    }

    public function printTeacher(Request $request, Enseignant $enseignant)
    {
        $this->guardTeacher($enseignant, $request);
        return view('personnel.enseignants.emploi-temps-print', $this->scheduleData($request, $enseignant));
    }

    public function pdfTeacher(Request $request, Enseignant $enseignant)
    {
        $this->guardTeacher($enseignant, $request);
        return Pdf::loadView('personnel.enseignants.emploi-temps-print', $this->scheduleData($request, $enseignant))
            ->setPaper('a4', 'landscape')
            ->download('emploi-temps-'.$enseignant->id.'.pdf');
    }

    // ─── Données partagées ───────────────────────────────────────────────────

    private function scheduleData(Request $request, Enseignant $enseignant): array
    {
        $user = $request->user();
        $enseignant->loadMissing(['matieres', 'classes']);

        $entries = EmploiTemps::with(['classe', 'serie', 'matiere'])
            ->where('tenant_id', $user->tenant_id)
            ->where('enseignant_id', $enseignant->id)
            ->get();

        $savedSlots = EmploiTempsSlot::where('tenant_id', $user->tenant_id)
            ->where('enseignant_id', $enseignant->id)
            ->get()
            ->keyBy('slot_key');

        $slots = $this->slotsFor($entries, $savedSlots);

        $grid = [];
        foreach ($entries as $entry) {
            $grid[$entry->jour][$this->slotKeyFor($entry)] = $entry;
        }

        $years  = AnneeAcademique::where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->orderByDesc('date_debut')
            ->get();

        $yearId = $entries->pluck('annee_academique_id')->filter()->first();

        // Données pour la vue "form" (existingEntries au format attendu par le JS)
        $existingEntries = $entries->map(fn ($e) => [
            'day'        => strtolower($e->jour),
            'slot_key'   => $this->slotKeyFor($e),
            'classe_id'  => $e->classe_id,
            'matiere_id' => $e->matiere_id,
            'salle'      => $e->salle,
        ])->values()->all();

        return [
            'enseignant'      => $enseignant,
            'classes'         => Classe::where('tenant_id', $user->tenant_id)
                ->when($user->etablissement_id, fn ($q) => $q->where('etablissement_id', $user->etablissement_id))
                ->orderBy('nom')
                ->get(),
            'series'          => $enseignant->series()->orderBy('nom_serie')->get(),
            'subjects'        => $enseignant->matieres()->orderBy('nom')->get(),
            'years'           => $years,
            'academicYears'   => $years,  // alias pour la vue create
            'year'            => $yearId ? $years->firstWhere('id', $yearId) : null,
            'entries'         => $entries,
            'existingEntries' => $existingEntries,
            'grid'            => $grid,
            'days'            => self::DAYS,
            'slots'           => $slots,
            'establishmentId' => $user->etablissement_id ?? $enseignant->etablissement_id,
            'school'          => Etablissement::find($user->etablissement_id ?? $enseignant->etablissement_id),
            'totalSeances'    => $entries->count(),
        ];
    }

    private function form(Request $request, Enseignant $enseignant, bool $editing): \Illuminate\View\View
    {
        return view(
            'personnel.enseignants.emploi-temps-create',
            $this->scheduleData($request, $enseignant) + ['isEdit' => $editing, 'editing' => $editing]
        );
    }

    // ─── Enregistrement ─────────────────────────────────────────────────────
    //
    // La vue envoie du JSON avec ce format :
    //   slots : [ {key:"slot-1", start:"07:00", end:"07:55"}, ... ]
    //   cells : [ {day:"lundi", slot_key:"slot-1", classe_id:1, matiere_id:2, salle:""}, ... ]

    private function saveSchedule(Request $request, Enseignant $enseignant): void
    {
        $user = $request->user();
        $enseignant->loadMissing(['matieres', 'classes']);

        // Validation du payload JSON
        $data = $request->validate([
            'annee_academique_id'    => ['nullable', 'integer'],
            'etablissement_id'       => ['nullable', 'integer'],
            'slots'                  => ['required', 'array', 'min:1'],
            'slots.*.key'            => ['required', 'string'],
            'slots.*.start'          => ['required', 'date_format:H:i'],
            'slots.*.end'            => ['required', 'date_format:H:i'],
            'cells'                  => ['nullable', 'array'],
            'cells.*.day'            => ['required', 'string', 'in:lundi,mardi,mercredi,jeudi,vendredi'],
            'cells.*.slot_key'       => ['required', 'string'],
            'cells.*.classe_id'      => ['required', 'integer'],
            'cells.*.matiere_id'     => ['required', 'integer'],
            'cells.*.salle'          => ['nullable', 'string', 'max:100'],
        ]);

        $establishmentId = (int) ($data['etablissement_id'] ?? $user->etablissement_id ?? $enseignant->etablissement_id);

        // Construire le dictionnaire des créneaux : slot_key => {heure_debut, heure_fin}
        $slotDefinitions = [];
        foreach ($data['slots'] as $slot) {
            $key  = $slot['key'];
            $deb  = $slot['start'];
            $fin  = $slot['end'];

            if (! $this->timeAt($deb)->lt($this->timeAt($fin))) {
                throw ValidationException::withMessages(['slots' => "Créneau $key : l'heure de fin doit être supérieure à l'heure de début."]);
            }
            $slotDefinitions[$key] = ['heure_debut' => $deb, 'heure_fin' => $fin];
        }

        // Interdire les doublons d'horaires
        $seen = [];
        foreach ($slotDefinitions as $key => $slot) {
            $sig = $slot['heure_debut'].'-'.$slot['heure_fin'];
            if (isset($seen[$sig])) {
                throw ValidationException::withMessages(['slots' => 'Deux créneaux identiques ne sont pas autorisés.']);
            }
            $seen[$sig] = $key;
        }

        $allowedSubjects = $enseignant->matieres->pluck('id')->all();
        $allowedClasses  = $enseignant->classes->pluck('id')->all();

        $entries = [];

        foreach ($data['cells'] ?? [] as $cell) {
            $slotKey    = $cell['slot_key'];
            $day        = $cell['day'];
            $classeId   = (int) $cell['classe_id'];
            $matiereId  = (int) $cell['matiere_id'];

            if (! isset($slotDefinitions[$slotKey])) {
                throw ValidationException::withMessages(['cells' => "Créneau $slotKey non défini dans les créneaux soumis."]);
            }
            if (! in_array($classeId, $allowedClasses, true)) {
                throw ValidationException::withMessages(['cells' => "La classe sélectionnée n'est pas affiliée à cet enseignant."]);
            }
            if (! in_array($matiereId, $allowedSubjects, true)) {
                throw ValidationException::withMessages(['cells' => "La matière sélectionnée n'est pas affiliée à cet enseignant."]);
            }

            $slot      = $slotDefinitions[$slotKey];
            $entries[] = [
                'jour'               => $day,
                'heure_debut'        => $this->timeAt($slot['heure_debut'])->format('H:i:s'),
                'heure_fin'          => $this->timeAt($slot['heure_fin'])->format('H:i:s'),
                'slot_key'           => $slotKey,
                'classe_id'          => $classeId,
                'serie_id'           => null,
                'matiere_id'         => $matiereId,
                'enseignant_id'      => $enseignant->id,
                'salle'              => filled($cell['salle'] ?? null) ? trim($cell['salle']) : null,
                'etablissement_id'   => $establishmentId ?: null,
                'annee_academique_id' => $data['annee_academique_id'] ?? null,
            ];
        }

        // Vérifications de conflits internes (doublons dans la même soumission)
        $this->validateInternalConflicts($entries);

        // Vérifications de conflits externes (en base, hors l'enseignant en cours de mise à jour)
        foreach ($entries as $entry) {
            $this->validateExternalConflicts($request, $entry, $enseignant->id);
        }

        // Persistance dans une transaction
        DB::transaction(function () use ($request, $enseignant, $entries, $slotDefinitions) {
            $tenantId = $request->user()->tenant_id;

            // Supprimer les anciens enregistrements
            EmploiTemps::where('tenant_id', $tenantId)
                ->where('enseignant_id', $enseignant->id)
                ->delete();
            EmploiTempsSlot::where('tenant_id', $tenantId)
                ->where('enseignant_id', $enseignant->id)
                ->delete();

            // Insérer les nouveaux créneaux (table emploi_temps_slots)
            $slotRecords = [];
            foreach ($slotDefinitions as $key => $slot) {
                $slotRecords[] = [
                    'tenant_id'    => $tenantId,
                    'enseignant_id' => $enseignant->id,
                    'slot_key'     => $key,
                    'heure_debut'  => $this->timeAt($slot['heure_debut'])->format('H:i:s'),
                    'heure_fin'    => $this->timeAt($slot['heure_fin'])->format('H:i:s'),
                ];
            }
            if ($slotRecords) {
                EmploiTempsSlot::insert($slotRecords);
            }

            // Insérer les cours
            foreach ($entries as $entry) {
                EmploiTemps::create($entry + ['tenant_id' => $tenantId]);
            }
        });
    }

    // ─── Validations ─────────────────────────────────────────────────────────

    private function validateInternalConflicts(array $entries): void
    {
        foreach ($entries as $i => $a) {
            foreach ($entries as $j => $b) {
                if ($i >= $j || $a['jour'] !== $b['jour'] || ! $this->overlaps($a, $b)) {
                    continue;
                }
                if ($a['classe_id'] === $b['classe_id']) {
                    throw ValidationException::withMessages(['cells' => 'Cette classe est déjà occupée sur ce créneau.']);
                }
                if ($a['salle'] && $a['salle'] === $b['salle']) {
                    throw ValidationException::withMessages(['cells' => 'Cette salle est déjà occupée sur ce créneau.']);
                }
            }
        }
    }

    private function validateExternalConflicts(Request $request, array $entry, ?int $replacedTeacherId): void
    {
        $base = EmploiTemps::query()
            ->where('emploi_temps.tenant_id', $request->user()->tenant_id)
            ->where('emploi_temps.jour', $entry['jour'])
            ->where('emploi_temps.heure_debut', '<', $entry['heure_fin'])
            ->where('emploi_temps.heure_fin', '>', $entry['heure_debut']);

        if ($replacedTeacherId) {
            $base->where('emploi_temps.enseignant_id', '<>', $replacedTeacherId);
        }

        // Conflit enseignant sur un autre créneau (normalement impossible puisque chaque slot_key est unique, mais sécurité)
        if ((clone $base)->where('emploi_temps.enseignant_id', $entry['enseignant_id'])->exists()) {
            throw ValidationException::withMessages(['cells' => 'Cet enseignant enseigne déjà sur ce créneau dans une autre classe.']);
        }

        // Conflit salle
        if ($entry['salle'] && (clone $base)->where('emploi_temps.salle', $entry['salle'])->exists()) {
            throw ValidationException::withMessages(['cells' => 'Cette salle est déjà occupée sur ce créneau.']);
        }
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function guardTeacher(Enseignant $enseignant, Request $request): void
    {
        abort_unless((int) $enseignant->tenant_id === (int) $request->user()->tenant_id, 404);
    }

    private function hasSchedule(Enseignant $enseignant, Request $request): bool
    {
        return EmploiTemps::where('tenant_id', $request->user()->tenant_id)
            ->where('enseignant_id', $enseignant->id)
            ->exists();
    }

    private function slotsFor($entries, $savedSlots): array
    {
        $savedTimes = [];
        foreach ($entries as $entry) {
            $savedTimes[$this->slotKeyFor($entry)] = [
                'start' => $this->time($entry->heure_debut),
                'end'   => $this->time($entry->heure_fin),
            ];
        }

        $built = array_map(function (array $slot) use ($savedTimes, $savedSlots) {
            if (isset($slot['break'])) {
                return $slot;
            }
            $stored = $savedSlots->get($slot['key']);
            $times  = $stored
                ? ['start' => $this->time($stored->heure_debut), 'end' => $this->time($stored->heure_fin)]
                : ($savedTimes[$slot['key']] ?? []);
            $slot = array_replace($slot, $times);
            return $slot + ['color' => $this->colorForSlot($slot['start'] ?? '00:00')];
        }, self::DEFAULT_SLOTS);

        $nonBreak = array_filter($built, fn ($s) => ! isset($s['break']));
        uasort($nonBreak, fn ($a, $b) =>
            $this->timeAt($a['start'])->timestamp <=> $this->timeAt($b['start'])->timestamp
        );

        $sorted = [];
        $idx    = 0;
        $nbv    = array_values($nonBreak);
        foreach ($built as $s) {
            if (isset($s['break'])) {
                $sorted[] = $s;
                continue;
            }
            $sorted[] = $nbv[$idx++];
        }
        return $sorted;
    }

    private function slotKeyFor(EmploiTemps $entry): string
    {
        if ($entry->slot_key) {
            return $entry->slot_key;
        }
        foreach (self::DEFAULT_SLOTS as $slot) {
            if (! isset($slot['break'])
                && $slot['start'] === $this->time($entry->heure_debut)
                && $slot['end']   === $this->time($entry->heure_fin)
            ) {
                return $slot['key'];
            }
        }
        return 'legacy-'.$entry->id;
    }

    private function colorForSlot(string $start): string
    {
        $minutes = (int) substr($start, 0, 2) * 60 + (int) substr($start, 3, 2);
        return match (true) {
            $minutes < 8 * 60 + 30  => 'slot-color-1',
            $minutes < 9 * 60 + 30  => 'slot-color-2',
            $minutes < 10 * 60 + 45 => 'slot-color-3',
            $minutes < 11 * 60 + 45 => 'slot-color-4',
            $minutes < 12 * 60 + 45 => 'slot-color-5',
            $minutes < 15 * 60      => 'slot-color-6',
            $minutes < 16 * 60      => 'slot-color-7',
            default                 => 'slot-color-8',
        };
    }

    private function overlaps(array $a, array $b): bool
    {
        return $this->timeAt($a['heure_debut'])->lt($this->timeAt($b['heure_fin']))
            && $this->timeAt($a['heure_fin'])->gt($this->timeAt($b['heure_debut']));
    }

    private function timeAt(string $time): CarbonImmutable
    {
        $time = trim($time);
        return CarbonImmutable::createFromFormat('!H:i:s', strlen($time) === 5 ? $time.':00' : $time);
    }

    private function time($value): string
    {
        return substr((string) $value, 0, 5);
    }
}
