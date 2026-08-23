<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\{AnneeAcademique, Classe, EmploiTemps, EmploiTempsSlot, Enseignant, Etablissement, Matiere, Series};
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
            'message' => 'Emploi du temps créé avec succès.',
            'redirect' => route('personnel.enseignants.emploi-temps.show', $enseignant),
        ]);
    }

    public function updateTeacherSchedule(Request $request, Enseignant $enseignant)
    {
        $this->guardTeacher($enseignant, $request);
        $this->saveSchedule($request, $enseignant);
        return response()->json([
            'message' => 'Emploi du temps mis à jour avec succès.',
            'redirect' => route('personnel.enseignants.emploi-temps.show', $enseignant),
        ]);
    }

    public function destroyTeacherSchedule(Request $request, Enseignant $enseignant)
    {
        $this->guardTeacher($enseignant, $request);
        $user = $request->user();

        DB::transaction(function () use ($enseignant, $user) {
            EmploiTemps::where('tenant_id', $user->tenant_id)
                ->where('enseignant_id', $enseignant->id)
                ->delete();

            EmploiTempsSlot::where('tenant_id', $user->tenant_id)
                ->where('enseignant_id', $enseignant->id)
                ->delete();
        });

        return response()->json([
            'message' => 'Emploi du temps supprimé avec succès.',
            'redirect' => route('personnel.enseignants.index'),
        ]);
    }

    public function printTeacher(Request $request, Enseignant $enseignant)
    {
        $this->guardTeacher($enseignant, $request);
        return view('personnel.enseignants.emploi-temps-print', $this->scheduleData($request, $enseignant));
    }

    public function pdfTeacher(Request $request, Enseignant $enseignant)
    {
        $this->guardTeacher($enseignant, $request);
        $pdf = Pdf::loadView('personnel.enseignants.emploi-temps-print', $this->scheduleData($request, $enseignant))
            ->setPaper('a4', 'landscape');

        return $pdf->download("emploi-temps-{$enseignant->nom}-{$enseignant->prenoms}.pdf");
    }

    private function guardTeacher(Enseignant $teacher, Request $request): void
    {
        $user = $request->user();
        abort_unless(
            (int) $teacher->tenant_id === (int) $user->tenant_id
            && (! $user->etablissement_id || (int) $teacher->etablissement_id === (int) $user->etablissement_id),
            403,
            'Accès refusé pour cet enseignant.'
        );
    }

    private function hasSchedule(Enseignant $teacher, Request $request): bool
    {
        $user = $request->user();
        return EmploiTemps::where('tenant_id', $user->tenant_id)
            ->where('enseignant_id', $teacher->id)
            ->exists();
    }

    private function scheduleData(Request $request, Enseignant $enseignant): array
    {
        $user = $request->user();
        $enseignant->loadMissing(['matieres', 'classes']);

        $entries = EmploiTemps::with(['matiere', 'classe', 'serie'])
            ->where('tenant_id', $user->tenant_id)
            ->where('enseignant_id', $enseignant->id)
            ->get();

        $slots = EmploiTempsSlot::where('tenant_id', $user->tenant_id)
            ->where('enseignant_id', $enseignant->id)
            ->orderBy('position')
            ->get();

        $slotsArray = $slots->isNotEmpty()
            ? $slots->map(fn ($s) => $s->is_break
                ? ['break' => $s->break_name ?: 'Pause']
                : [
                    'key' => $s->slot_key,
                    'start' => substr($s->start_time, 0, 5),
                    'end' => substr($s->end_time, 0, 5),
                ])->all()
            : self::DEFAULT_SLOTS;

        $grid = [];
        foreach (self::DAYS as $day) {
            $grid[$day] = [];
            foreach ($slotsArray as $slot) {
                if (isset($slot['break'])) {
                    continue;
                }
                $grid[$day][$slot['key']] = null;
            }
        }

        foreach ($entries as $entry) {
            $day = strtolower($entry->jour);
            if (!isset($grid[$day])) {
                continue;
            }

            $slotKey = $entry->slot_key;
            if (!$slotKey) {
                $start = substr($entry->heure_debut, 0, 5);
                foreach ($slotsArray as $s) {
                    if (isset($s['key']) && $s['start'] === $start) {
                        $slotKey = $s['key'];
                        break;
                    }
                }
            }

            if ($slotKey && array_key_exists($slotKey, $grid[$day])) {
                $grid[$day][$slotKey] = [
                    'id' => $entry->id,
                    'classe' => $entry->classe?->nom ?? 'N/A',
                    'classe_id' => $entry->classe_id,
                    'matiere' => $entry->matiere?->nom ?? 'N/A',
                    'matiere_id' => $entry->matiere_id,
                    'salle' => $entry->salle,
                ];
            }
        }

        $academicYear = AnneeAcademique::where('tenant_id', $user->tenant_id)
            ->orderByDesc('date_debut')
            ->first();

        $school = Etablissement::where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn ($q) => $q->where('id', $user->etablissement_id))
            ->first();

        return [
            'enseignant' => $enseignant,
            'days' => self::DAYS,
            'slots' => $slotsArray,
            'grid' => $grid,
            'entries' => $entries,
            'academicYear' => $academicYear,
            'school' => $school,
        ];
    }

    private function form(Request $request, Enseignant $enseignant, bool $isEdit)
    {
        $user = $request->user();
        $enseignant->loadMissing(['matieres', 'classes']);

        $academicYears = AnneeAcademique::where('tenant_id', $user->tenant_id)
            ->orderByDesc('date_debut')
            ->get();

        $slots = EmploiTempsSlot::where('tenant_id', $user->tenant_id)
            ->where('enseignant_id', $enseignant->id)
            ->orderBy('position')
            ->get();

        $slotsArray = $slots->isNotEmpty()
            ? $slots->map(fn ($s) => $s->is_break
                ? ['break' => $s->break_name ?: 'Pause']
                : [
                    'key' => $s->slot_key,
                    'start' => substr($s->start_time, 0, 5),
                    'end' => substr($s->end_time, 0, 5),
                ])->all()
            : self::DEFAULT_SLOTS;

        $existingEntries = $isEdit
            ? EmploiTemps::with(['matiere', 'classe', 'serie'])
                ->where('tenant_id', $user->tenant_id)
                ->where('enseignant_id', $enseignant->id)
                ->get()
            : collect();

        $mappedEntries = [];
        foreach ($existingEntries as $entry) {
            $mappedEntries[] = [
                'id' => $entry->id,
                'day' => strtolower($entry->jour),
                'slot_key' => $entry->slot_key,
                'start_time' => substr($entry->heure_debut, 0, 5),
                'end_time' => substr($entry->heure_fin, 0, 5),
                'classe_id' => $entry->classe_id,
                'matiere_id' => $entry->matiere_id,
                'salle' => $entry->salle,
            ];
        }

        return view('personnel.enseignants.emploi-temps-create', [
            'enseignant' => $enseignant,
            'isEdit' => $isEdit,
            'days' => self::DAYS,
            'slots' => $slotsArray,
            'classes' => $enseignant->classes,
            'subjects' => $enseignant->matieres,
            'academicYears' => $academicYears,
            'existingEntries' => $mappedEntries,
        ]);
    }

    private function saveSchedule(Request $request, Enseignant $enseignant): void
    {
        $user = $request->user();
        $enseignant->loadMissing(['matieres', 'classes']);

        $allowedClasseIds = $enseignant->classes->pluck('id')->all();
        $allowedMatiereIds = $enseignant->matieres->pluck('id')->all();

        $validated = $request->validate([
            'annee_academique_id' => ['nullable', 'integer'],
            'slots' => ['required', 'array', 'min:1'],
            'cells' => ['nullable', 'array'],
            'cells.*.day' => ['required', 'string', 'in:lundi,mardi,mercredi,jeudi,vendredi'],
            'cells.*.slot_key' => ['required', 'string'],
            'cells.*.classe_id' => ['required', 'integer'],
            'cells.*.matiere_id' => ['required', 'integer'],
            'cells.*.salle' => ['nullable', 'string', 'max:50'],
        ]);

        // Validation des règles d'affiliation enseignant
        if (!empty($validated['cells'])) {
            foreach ($validated['cells'] as $cell) {
                if (!in_array((int)$cell['classe_id'], $allowedClasseIds, true)) {
                    throw ValidationException::withMessages([
                        'cells' => "La classe sélectionnée n'est pas affiliée à cet enseignant.",
                    ]);
                }
                if (!in_array((int)$cell['matiere_id'], $allowedMatiereIds, true)) {
                    throw ValidationException::withMessages([
                        'cells' => "La matière sélectionnée n'est pas affiliée à cet enseignant.",
                    ]);
                }
            }
        }

        DB::transaction(function () use ($validated, $enseignant, $user) {
            // Nettoyer les anciens créneaux et entrées
            EmploiTemps::where('tenant_id', $user->tenant_id)
                ->where('enseignant_id', $enseignant->id)
                ->delete();

            EmploiTempsSlot::where('tenant_id', $user->tenant_id)
                ->where('enseignant_id', $enseignant->id)
                ->delete();

            // Enregistrer les nouveaux créneaux
            $slotMap = [];
            foreach ($validated['slots'] as $position => $slot) {
                $isBreak = isset($slot['break']) && !empty($slot['break']);
                $slotKey = $slot['key'] ?? ('slot-' . ($position + 1));

                EmploiTempsSlot::create([
                    'tenant_id' => $user->tenant_id,
                    'etablissement_id' => $user->etablissement_id ?? $enseignant->etablissement_id,
                    'enseignant_id' => $enseignant->id,
                    'slot_key' => $slotKey,
                    'is_break' => $isBreak,
                    'break_name' => $isBreak ? $slot['break'] : null,
                    'start_time' => $isBreak ? '00:00:00' : ($slot['start'] . ':00'),
                    'end_time' => $isBreak ? '00:00:00' : ($slot['end'] . ':00'),
                    'position' => $position,
                ]);

                if (!$isBreak) {
                    $slotMap[$slotKey] = [
                        'start' => $slot['start'] . ':00',
                        'end' => $slot['end'] . ':00',
                    ];
                }
            }

            // Enregistrer les cours
            if (!empty($validated['cells'])) {
                foreach ($validated['cells'] as $cell) {
                    $slotInfo = $slotMap[$cell['slot_key']] ?? null;
                    if (!$slotInfo) {
                        continue;
                    }

                    EmploiTemps::create([
                        'tenant_id' => $user->tenant_id,
                        'etablissement_id' => $user->etablissement_id ?? $enseignant->etablissement_id,
                        'annee_academique_id' => $validated['annee_academique_id'] ?? null,
                        'enseignant_id' => $enseignant->id,
                        'classe_id' => $cell['classe_id'],
                        'matiere_id' => $cell['matiere_id'],
                        'jour' => ucfirst(strtolower($cell['day'])),
                        'heure_debut' => $slotInfo['start'],
                        'heure_fin' => $slotInfo['end'],
                        'slot_key' => $cell['slot_key'],
                        'salle' => $cell['salle'] ?? null,
                    ]);
                }
            }
        });
    }
}
