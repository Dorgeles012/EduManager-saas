<?php

namespace App\Services;

use App\Models\EmploiTemps;
use App\Models\EmploiTempsSlot;
use Carbon\CarbonImmutable;

/**
 * Service partagé pour la construction des emplois du temps.
 * Encapsule les jours, créneaux par défaut, la construction de la grille
 * et la résolution des horaires afin d'être réutilisé par les modules
 * Client, Personnel, Parent et Élève (source unique).
 */
class EmploiTempsService
{
    public const DAYS = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];

    public const DEFAULT_SLOTS = [
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

    public function days(): array
    {
        return self::DAYS;
    }

    public function defaultSlots(): array
    {
        return self::DEFAULT_SLOTS;
    }

    /**
     * Construit la grille jour => slot_key => entrée pour une collection d'entrées.
     */
    public function buildGrid($entries): array
    {
        $grid = [];
        foreach ($entries as $entry) {
            $grid[$entry->jour][$this->slotKeyFor($entry)] = $entry;
        }
        return $grid;
    }

    /**
     * Construit la liste des créneaux (avec pauses) pour une collection d'entrées
     * et d'éventuels créneaux enregistrés (EmploiTempsSlot).
     */
    public function slotsFor($entries, $savedSlots = null): array
    {
        $savedTimes = [];
        foreach ($entries as $entry) {
            $savedTimes[$this->slotKeyFor($entry)] = [
                'start' => $this->time($entry->heure_debut),
                'end' => $this->time($entry->heure_fin),
            ];
        }

        $savedSlots = $savedSlots ?: collect();

        $built = array_map(function (array $slot) use ($savedTimes, $savedSlots) {
            if (isset($slot['break'])) {
                return $slot;
            }
            $stored = $savedSlots->get($slot['key']);
            $times = $stored
                ? ['start' => $this->time($stored->heure_debut), 'end' => $this->time($stored->heure_fin)]
                : ($savedTimes[$slot['key']] ?? []);
            $slot = array_replace($slot, $times);
            return $slot + ['color' => $this->colorForSlot($slot['start'])];
        }, self::DEFAULT_SLOTS);

        // Trier chronologiquement les créneaux (garder les pauses à leur place)
        $nonBreak = array_filter($built, fn ($s) => ! isset($s['break']));
        uasort($nonBreak, fn ($a, $b) =>
            $this->timeAt($a['start'])->timestamp <=> $this->timeAt($b['start'])->timestamp
        );

        $sorted = [];
        $idx = 0;
        $nonBreakValues = array_values($nonBreak);
        foreach ($built as $s) {
            if (isset($s['break'])) {
                $sorted[] = $s;
                continue;
            }
            $sorted[] = $nonBreakValues[$idx];
            $idx++;
        }
        return $sorted;
    }

    public function slotKeyFor(EmploiTemps $entry): string
    {
        if ($entry->slot_key) {
            return $entry->slot_key;
        }
        foreach (self::DEFAULT_SLOTS as $slot) {
            if (! isset($slot['break']) && $slot['start'] === $this->time($entry->heure_debut) && $slot['end'] === $this->time($entry->heure_fin)) {
                return $slot['key'];
            }
        }
        return 'legacy-'.$entry->id;
    }

    public function colorForSlot(string $start): string
    {
        $minutes = (int) substr($start, 0, 2) * 60 + (int) substr($start, 3, 2);
        return match (true) {
            $minutes < 8 * 60 + 30 => 'slot-color-1',
            $minutes < 9 * 60 + 30 => 'slot-color-2',
            $minutes < 10 * 60 + 45 => 'slot-color-3',
            $minutes < 11 * 60 + 45 => 'slot-color-4',
            $minutes < 12 * 60 + 45 => 'slot-color-5',
            $minutes < 15 * 60       => 'slot-color-6',
            $minutes < 16 * 60       => 'slot-color-7',
            default                  => 'slot-color-8',
        };
    }

    public function time($value): string
    {
        return substr((string) $value, 0, 5);
    }

    public function timeAt(string $time): CarbonImmutable
    {
        $time = trim($time);
        return CarbonImmutable::createFromFormat('!H:i:s', strlen($time) === 5 ? $time.':00' : $time);
    }

    public function overlaps(array $a, array $b): bool
    {
        return $this->timeAt($a['heure_debut'])->lt($this->timeAt($b['heure_fin']))
            && $this->timeAt($a['heure_fin'])->gt($this->timeAt($b['heure_debut']));
    }
}
