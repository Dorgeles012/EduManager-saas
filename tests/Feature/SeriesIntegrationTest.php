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

it('relie les séries aux classes et aux élèves avec des colonnes facultatives', function () {
    expect(Schema::hasColumn('series', 'id_classe'))->toBeTrue()
        ->and(Schema::hasColumn('eleves', 'id_serie'))->toBeTrue();

    $tenant = Tenant::factory()->create();
    $school = Etablissement::factory()->create(['tenant_id' => $tenant->id]);
    $level = Niveau::create(['tenant_id' => $tenant->id, 'etablissement_id' => $school->id, 'nom' => 'Terminale']);
    $class = Classe::create(['tenant_id' => $tenant->id, 'etablissement_id' => $school->id, 'niveau_id' => $level->id, 'nom' => 'Terminale A']);
    $serie = Series::create(['tenant_id' => $tenant->id, 'id_classe' => $class->id, 'nom_serie' => 'D']);
    $student = Eleve::create([
        'tenant_id' => $tenant->id, 'etablissement_id' => $school->id,
        'niveau_id' => $level->id, 'classe_id' => $class->id, 'id_serie' => $serie->id,
        'matricule' => 'SERIE-001', 'nom' => 'Test',
    ]);

    expect($class->series->first()->is($serie))->toBeTrue()
        ->and($serie->classe->is($class))->toBeTrue()
        ->and($serie->eleves->first()->is($student))->toBeTrue()
        ->and($student->serie->is($serie))->toBeTrue();
});

it('ne retourne que les séries de la classe et du tenant authentifié', function () {
    $tenant = Tenant::factory()->create();
    $school = Etablissement::factory()->create(['tenant_id' => $tenant->id]);
    $user = User::factory()->create(['tenant_id' => $tenant->id, 'etablissement_id' => $school->id, 'role' => 'CLIENT']);
    $level = Niveau::create(['tenant_id' => $tenant->id, 'etablissement_id' => $school->id, 'nom' => 'Terminale']);
    $class = Classe::create(['tenant_id' => $tenant->id, 'etablissement_id' => $school->id, 'niveau_id' => $level->id, 'nom' => 'Terminale A']);
    Series::create(['tenant_id' => $tenant->id, 'id_classe' => $class->id, 'nom_serie' => 'D']);
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
