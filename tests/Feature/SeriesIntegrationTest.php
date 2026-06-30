<?php

use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Etablissement;
use App\Models\Niveau;
use App\Models\Series;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('relie les séries à plusieurs classes via pivot (classe_serie)', function () {
    $tenant = Tenant::factory()->create();
    $school = Etablissement::factory()->create(['tenant_id' => $tenant->id]);
    $level = Niveau::create(['tenant_id' => $tenant->id, 'etablissement_id' => $school->id, 'nom' => 'Terminale']);

    $classA = Classe::create([
        'tenant_id' => $tenant->id,
        'etablissement_id' => $school->id,
        'niveau_id' => $level->id,
        'nom' => 'Terminale A'
    ]);

    $classB = Classe::create([
        'tenant_id' => $tenant->id,
        'etablissement_id' => $school->id,
        'niveau_id' => $level->id,
        'nom' => 'Terminale B'
    ]);

    $serie = Series::create([
        'tenant_id' => $tenant->id,
        // compat 1ère classe
        'id_classe' => $classA->id,
        'nom_serie' => 'D'
    ]);

    $serie->classes()->sync([$classA->id, $classB->id]);

    expect($classA->series->pluck('id')->contains($serie->id))->toBeTrue();
    expect($classB->series->pluck('id')->contains($serie->id))->toBeTrue();
});


it('crée une série attribuée à plusieurs classes depuis le formulaire client', function () {
    $tenant = Tenant::factory()->create();
    $school = Etablissement::factory()->create(['tenant_id' => $tenant->id]);
    $user = User::factory()->create(['tenant_id' => $tenant->id, 'etablissement_id' => $school->id, 'role' => 'CLIENT']);
    $level = Niveau::create(['tenant_id' => $tenant->id, 'etablissement_id' => $school->id, 'nom' => 'Terminale']);
    $classes = collect(['A', 'B'])->map(fn ($suffix) => Classe::create([
        'tenant_id' => $tenant->id,
        'etablissement_id' => $school->id,
        'niveau_id' => $level->id,
        'nom' => "Terminale {$suffix}",
    ]));

    $this->actingAs($user)->post(route('client.series.store'), [
        'nom_serie' => 'C',
        'id_classes' => $classes->pluck('id')->all(),
    ])->assertSessionHasNoErrors();

    $serie = Series::where('tenant_id', $tenant->id)->where('nom_serie', 'C')->firstOrFail();
    expect($serie->classes()->count())->toBe(2);
});

it('ne retourne que les séries de la classe et du tenant authentifié', function () {
    $tenant = Tenant::factory()->create();
    $school = Etablissement::factory()->create(['tenant_id' => $tenant->id]);
    $user = User::factory()->create(['tenant_id' => $tenant->id, 'etablissement_id' => $school->id, 'role' => 'CLIENT']);
    $level = Niveau::create(['tenant_id' => $tenant->id, 'etablissement_id' => $school->id, 'nom' => 'Terminale']);
    $class = Classe::create(['tenant_id' => $tenant->id, 'etablissement_id' => $school->id, 'niveau_id' => $level->id, 'nom' => 'Terminale A']);
    $serie = Series::create(['tenant_id' => $tenant->id, 'id_classe' => $class->id, 'nom_serie' => 'D']);
    $serie->classes()->sync([$class->id]);
    Series::create(['tenant_id' => $tenant->id, 'id_classe' => null, 'nom_serie' => 'Historique']);

    $this->actingAs($user)
        ->getJson(route('client.series.by-classe', $class))
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.nom_serie', 'D');
});

it('affiche la photo stockée dans la fiche détaillée de l’élève', function () {
    Storage::fake('public');
    Storage::disk('public')->put('eleves/photo-test.jpg', 'image-content');

    $tenant = Tenant::factory()->create();
    $school = Etablissement::factory()->create(['tenant_id' => $tenant->id]);
    $user = User::factory()->create(['tenant_id' => $tenant->id, 'etablissement_id' => $school->id, 'role' => 'CLIENT']);
    $student = Eleve::create([
        'tenant_id' => $tenant->id,
        'etablissement_id' => $school->id,
        'matricule' => 'PHOTO-001',
        'nom' => 'Photo',
        'photo' => 'eleves/photo-test.jpg',
    ]);

    $this->actingAs($user)
        ->get(route('client.eleve.photo', $student))
        ->assertOk();
});
