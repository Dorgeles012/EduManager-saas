<?php

use App\Models\Matiere;
use App\Models\Series;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('associe une matière à une série avec un coefficient propre', function () {
    $tenant = Tenant::factory()->create();
    $serie = Series::create(['tenant_id' => $tenant->id, 'nom_serie' => 'C']);
    $matiere = Matiere::create([
        'tenant_id' => $tenant->id,
        'nom' => 'Mathématiques',
        'coefficient' => 2,
    ]);

    $serie->matieres()->attach($matiere->id, ['coefficient' => 5]);

    expect($serie->fresh()->matieres)->toHaveCount(1)
        ->and($serie->fresh()->matieres->first()->pivot->coefficient)->toBe(5)
        ->and($matiere->fresh()->series)->toHaveCount(1);
});
