<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\{AnneeAcademique, Classe, EmploiTemps, EmploiTempsSlot, Enseignant, Etablissement, Matiere, Series};
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnseignantEmploiTempsController extends Controller
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

    /**
     * Affiche l'emploi du temps de l'enseignant connecté (lecture seule).
     */
    public function index(Request $request)
    {
        $enseignant = $this->getEnseignantConnecte();

        if (!$enseignant) {
            return view('enseignant.emploi-temps.index', [
                'enseignant' => null,
                'entries' => collect(),
                'grid' => [],
                'days' => self::DAYS,
                'slots' => [],
                'totalSeances' => 0,
                'school' => null,
                'year' => null,
                'message' => 'Aucun profil enseignant trouvé pour votre compte.',
            ]);
        }

        $enseignant->loadMissing(['matieres', 'classes']);
        $user = $request->user();

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

        $years = AnneeAcademique::where('tenant_id', $user->tenant_id)
            ->when($user->etablissement_id, fn($q) => $q->where('etablissement_id', $user->etablissement_id))
            ->orderByDesc('date_debut')
            ->get();

        $yearId = $entries->pluck('annee_academique_id')->filter()->first();

        return view('enseignant.emploi-temps.index', [
            'enseignant' => $enseignant,
            'entries' => $entries,
            'grid' => $grid,
            'days' => self::DAYS,
            'slots' => $slots,
            'totalSeances' => $entries->count(),
            'school' => Etablissement::find($user->etablissement_id ?? $enseignant->etablissement_id),
            'year' => $yearId ? $years->firstWhere('id', $yearId) : null,
            'message' => null,
        ]);
    }

    /**
     * Affiche l'emploi du temps en version imprimable.
     */
    public function print(Request $request)
    {
        $enseignant = $this->getEnseignantConnecte();

        if (!$enseignant) {
            return back()->with('error', 'Aucun profil enseignant trouvé.');
        }

        $user = $request->user();
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

        return view('enseignant.emploi-temps.index', [
            'enseignant' => $enseignant,
            'entries' => $entries,
            'grid' => $grid,
            'days' => self::DAYS,
            'slots' => $slots,
            'totalSeances' => $entries->count(),
            'school' => Etablissement::find($user->etablissement_id ?? $enseignant->etablissement_id),
            'year' => null,
            'printMode' => true,
            'message' => null,
        ]);
    }

    /**
     * Télécharge l'emploi du temps en PDF.
     */
    public function pdf(Request $request)
    {
        $enseignant = $this->getEnseignantConnecte();

        if (!$enseignant) {
            return back()->with('error', 'Aucun profil enseignant trouvé.');
        }

        $user = $request->user();
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

        $data = [
            'enseignant' => $enseignant,
            'entries' => $entries,
            'grid' => $grid,
            'days' => self::DAYS,
            'slots' => $slots,
            'totalSeances' => $entries->count(),
            'school' => Etablissement::find($user->etablissement_id ?? $enseignant->etablissement_id),
            'year' => null,
            'printMode' => true,
        ];

        return Pdf::loadView('enseignant.emploi-temps.index', $data)
            ->setPaper('a4', 'landscape')
            ->download('emploi-du-temps-' . $enseignant->id . '.pdf');
    }

    /**
     * Récupère l'enseignant connecté à partir de l'utilisateur auth.
     */
    private function getEnseignantConnecte(): ?Enseignant
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        // Chercher l'enseignant associé à cet utilisateur
        return Enseignant::where('tenant_id', $user->tenant_id)
            ->where('user_id', $user->id)
            ->first();
    }

    private function slotsFor($entries, $savedSlots): array
    {
        $savedTimes = [];
        foreach ($entries as $entry) {
            $savedTimes[$this->slotKeyFor($entry)] = [
                'start' => $this->time($entry->heure_debut),
                'end' => $this->time($entry->heure_fin)
            ];
        }

        $built = array_map(function (array $slot) use ($savedTimes, $savedSlots) {
            if (isset($slot['break'])) return $slot;
            $stored = $savedSlots->get($slot['key']);
            $times = $stored
                ? ['start' => $this->time($stored->heure_debut), 'end' => $this->time($stored->heure_fin)]
                : ($savedTimes[$slot['key']] ?? []);
            $slot = array_replace($slot, $times);
            return $slot + ['color' => $this->colorForSlot($slot['start'])];
        }, self::DEFAULT_SLOTS);

        $nonBreak = array_filter($built, fn($s) => !isset($s['break']));
        uasort($nonBreak, fn($a, $b) =>
            $this->timeAt($a['start'])->timestamp <=> $this->timeAt($b['start'])->timestamp
        );

        $sorted = [];
        $idx = 0;
        foreach ($built as $i => $s) {
            if (isset($s['break'])) {
                $sorted[] = $s;
                continue;
            }
            $sorted[] = array_values($nonBreak)[$idx];
            $idx++;
        }
        return $sorted;
    }

    private function slotKeyFor(EmploiTemps $entry): string
    {
        if ($entry->slot_key) return $entry->slot_key;
        foreach (self::DEFAULT_SLOTS as $slot) {
            if (!isset($slot['break']) && $slot['start'] === $this->time($entry->heure_debut) && $slot['end'] === $this->time($entry->heure_fin)) {
                return $slot['key'];
            }
        }
        return 'legacy-' . $entry->id;
    }

    private function colorForSlot(string $start): string
    {
        $minutes = (int)substr($start, 0, 2) * 60 + (int)substr($start, 3, 2);
        return match (true) {
            $minutes < 8 * 60 + 30 => 'slot-color-1',
            $minutes < 9 * 60 + 30 => 'slot-color-2',
            $minutes < 10 * 60 + 45 => 'slot-color-3',
            $minutes < 11 * 60 + 45 => 'slot-color-4',
            $minutes < 12 * 60 + 45 => 'slot-color-5',
            $minutes < 15 * 60 => 'slot-color-6',
            $minutes < 16 * 60 => 'slot-color-7',
            default => 'slot-color-8',
        };
    }

    private function time($value): string
    {
        return substr((string)$value, 0, 5);
    }

    private function timeAt(string $time): CarbonImmutable
    {
        $time = trim($time);
        return CarbonImmutable::createFromFormat('!H:i:s', strlen($time) === 5 ? $time . ':00' : $time);
    }
}

