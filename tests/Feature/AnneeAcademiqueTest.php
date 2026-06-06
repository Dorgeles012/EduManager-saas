<?php

use App\Models\AnneeAcademique;
use App\Models\Etablissement;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * NOTE:
 * Les tests doivent créer les enregistrements tenant_id / etablissement_id requis,
 * sinon la création de User échoue à cause des contraintes FOREIGN KEY.
 */
it('peut créer une année académique', function () {
    $tenant = Tenant::factory()->create();
    $etablissement = Etablissement::factory()->create([
        'tenant_id' => $tenant->id,
    ]);

    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'etablissement_id' => $etablissement->id,
        'role' => 'CLIENT',
    ]);

    $this->actingAs($user);

    $payload = [
        'libelle' => '2026-2027',
        'date_debut' => '2026-09-01',
        'date_fin' => '2027-06-30',
        'statut' => 'inactive',
    ];

    $response = $this->post(route('client.annee.store'), $payload);

    $response->assertRedirect(route('client.annee.index'));
    expect(AnneeAcademique::query()->where('tenant_id', $tenant->id)->where('etablissement_id', $etablissement->id)->where('libelle', '2026-2027')->exists())->toBeTrue();
});

it('peut modifier une année académique', function () {
    $tenant = Tenant::factory()->create();
    $etablissement = Etablissement::factory()->create([
        'tenant_id' => $tenant->id,
    ]);

    $annee = AnneeAcademique::create([
        'tenant_id' => $tenant->id,
        'etablissement_id' => $etablissement->id,
        'libelle' => '2025-2026',
        'date_debut' => '2025-09-01',
        'date_fin' => '2026-06-30',
        'statut' => 'inactive',
    ]);

    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'etablissement_id' => $etablissement->id,
        'role' => 'CLIENT',
    ]);

    $this->actingAs($user);

    $payload = [
        'libelle' => '2025-2026-UPDATED',
        'date_debut' => '2025-10-01',
        'date_fin' => '2026-06-30',
        'statut' => 'inactive',
    ];

    $response = $this->put(route('client.annee.update', $annee->id), $payload);
    $response->assertRedirect(route('client.annee.index'));

    $annee->refresh();
    expect($annee->libelle)->toBe('2025-2026-UPDATED');
});

it('empêche les doublons de libellé', function () {
    $tenant = Tenant::factory()->create();
    $etablissement = Etablissement::factory()->create([
        'tenant_id' => $tenant->id,
    ]);

    AnneeAcademique::create([
        'tenant_id' => $tenant->id,
        'etablissement_id' => $etablissement->id,
        'libelle' => '2026-2027',
        'date_debut' => '2026-09-01',
        'date_fin' => '2027-06-30',
        'statut' => 'inactive',
    ]);

    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'etablissement_id' => $etablissement->id,
        'role' => 'CLIENT',
    ]);

    $this->actingAs($user);

    $response = $this->post(route('client.annee.store'), [
        'libelle' => '2026-2027',
        'date_debut' => '2026-09-01',
        'date_fin' => '2027-06-30',
        'statut' => 'inactive',
    ]);

    $response->assertSessionHasErrors(['libelle']);
});

it('ne laisse qu’une seule année active à la fois', function () {
    $tenant = Tenant::factory()->create();
    $etablissement = Etablissement::factory()->create([
        'tenant_id' => $tenant->id,
    ]);

    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'etablissement_id' => $etablissement->id,
        'role' => 'CLIENT',
    ]);

    $this->actingAs($user);

    $annee1 = AnneeAcademique::create([
        'tenant_id' => $tenant->id,
        'etablissement_id' => $etablissement->id,
        'libelle' => '2024-2025',
        'date_debut' => '2024-09-01',
        'date_fin' => '2025-06-30',
        'statut' => 'active',
    ]);

    $this->put(route('client.annee.update', $annee1->id), [
        'libelle' => '2024-2025',
        'date_debut' => '2024-09-01',
        'date_fin' => '2025-06-30',
        'statut' => 'inactive',
    ])->assertRedirect(route('client.annee.index'));

    AnneeAcademique::create([
        'tenant_id' => $tenant->id,
        'etablissement_id' => $etablissement->id,
        'libelle' => '2026-2027',
        'date_debut' => '2026-09-01',
        'date_fin' => '2027-06-30',
        'statut' => 'inactive',
    ]);

    $annee2 = AnneeAcademique::query()
        ->where('tenant_id', $tenant->id)
        ->where('etablissement_id', $etablissement->id)
        ->where('libelle', '2026-2027')
        ->first();

    $this->put(route('client.annee.update', $annee2->id), [
        'libelle' => '2026-2027',
        'date_debut' => '2026-09-01',
        'date_fin' => '2027-06-30',
        'statut' => 'active',
    ])->assertRedirect(route('client.annee.index'));

    $activeCount = AnneeAcademique::query()
        ->where('tenant_id', $tenant->id)
        ->where('etablissement_id', $etablissement->id)
        ->where('statut', 'active')
        ->count();

    expect($activeCount)->toBe(1);
});


