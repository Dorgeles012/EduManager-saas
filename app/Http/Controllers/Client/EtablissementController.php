<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use App\Services\SchoolSubscriptionLimitService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EtablissementController extends Controller
{
    public function index(Request $request, SchoolSubscriptionLimitService $schoolLimits): View
    {
        $user = $request->user();
        $schools = $schoolLimits->schoolsForClient($user);
        $plan = $schoolLimits->currentPlanForClient($user);

        return view('client.etablissements', [
            'schools' => $schools,
            'plan' => $plan,
            'usedSchools' => $schools->count(),
            'remainingSchools' => $schoolLimits->remainingSchools($user, $plan),
        ]);
    }

    public function store(Request $request, SchoolSubscriptionLimitService $schoolLimits): RedirectResponse
    {
        $user = $request->user();
        $schoolLimits->ensureCanCreateSchool($user);

        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'acronyme' => ['nullable', 'string', 'max:100'],
            'type_etablissement' => ['required', 'string', 'in:primaire,college,lycee,universite,grande_ecole'],
            'email' => ['nullable', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'adresse' => ['nullable', 'string'],
        ]);

        $school = Etablissement::query()->create([
            ...$validated,
            'tenant_id' => $user->tenant_id ?? 1,
            'statut' => 'active',
        ]);

        if (! $user->etablissement_id) {
            $user->forceFill(['etablissement_id' => $school->id])->save();
        }

        return redirect()
            ->route('client.etablissements.index')
            ->with('success', 'Etablissement cree avec succes.');
    }

    public function switch(Request $request, SchoolSubscriptionLimitService $schoolLimits): RedirectResponse
    {
        $user = $request->user();
        $schoolIds = $schoolLimits->schoolsForClient($user)->pluck('id')->all();

        $validated = $request->validate([
            'etablissement_id' => ['required', 'integer', Rule::in($schoolIds)],
        ]);

        $user->forceFill(['etablissement_id' => (int) $validated['etablissement_id']])->save();

        return back()->with('success', 'Etablissement actif mis a jour.');
    }
}
