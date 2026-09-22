<?php

namespace Tests\Feature;

use App\Models\AnneeAcademique;
use App\Models\Bulletin;
use App\Models\BulletinDiscipline;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Enseignant;
use App\Models\Etablissement;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\User;
use App\Services\BulletinService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NotesAndBulletinsWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected User $clientUser;
    protected User $personnelUser;
    protected User $enseignantUser;
    protected User $eleveUser;
    protected User $parentUser;

    protected Etablissement $etablissement;
    protected AnneeAcademique $anneeAcademique;
    protected Classe $classe;
    protected Matiere $matiereMath;
    protected Matiere $matiereFr;
    protected Enseignant $enseignant;
    protected Eleve $eleve;

    protected function setUp(): void
    {
        parent::setUp();

        $tenantId = 99999;

        // Créer l'établissement
        $this->etablissement = Etablissement::create([
            'tenant_id' => $tenantId,
            'nom' => 'École Test Excellence',
            'code' => 'ETE-99',
            'email' => 'contact@test-ecole.com',
            'telephone' => '0102030405',
            'adresse' => 'Abidjan',
            'statut' => 'actif',
        ]);

        // Année académique
        $this->anneeAcademique = AnneeAcademique::create([
            'tenant_id' => $tenantId,
            'etablissement_id' => $this->etablissement->id,
            'libelle' => '2026-2027',
            'date_debut' => '2026-09-01',
            'date_fin' => '2027-06-30',
            'is_current' => true,
        ]);

        // Classe
        $this->classe = Classe::create([
            'tenant_id' => $tenantId,
            'etablissement_id' => $this->etablissement->id,
            'nom' => 'Terminale C1',
            'niveau' => 'Terminale',
            'statut' => 'actif',
        ]);

        // Matières (Math coef 4, Français coef 2)
        $this->matiereMath = Matiere::create([
            'tenant_id' => $tenantId,
            'etablissement_id' => $this->etablissement->id,
            'nom' => 'Mathématiques',
            'code' => 'MATH',
            'coefficient' => 4,
            'statut' => 'actif',
        ]);

        $this->matiereFr = Matiere::create([
            'tenant_id' => $tenantId,
            'etablissement_id' => $this->etablissement->id,
            'nom' => 'Français',
            'code' => 'FR',
            'coefficient' => 2,
            'statut' => 'actif',
        ]);

        // Utilisateurs
        $this->clientUser = User::create([
            'tenant_id' => $tenantId,
            'etablissement_id' => $this->etablissement->id,
            'name' => 'Directeur Client',
            'email' => 'client-test@test.com',
            'password' => bcrypt('password'),
            'role' => 'client',
            'statut' => 'actif',
        ]);

        $this->personnelUser = User::create([
            'tenant_id' => $tenantId,
            'etablissement_id' => $this->etablissement->id,
            'name' => 'Agent Personnel',
            'email' => 'personnel-test@test.com',
            'password' => bcrypt('password'),
            'role' => 'personnel',
            'statut' => 'actif',
        ]);

        $this->enseignantUser = User::create([
            'tenant_id' => $tenantId,
            'etablissement_id' => $this->etablissement->id,
            'name' => 'Professeur Diop',
            'email' => 'enseignant-test@test.com',
            'password' => bcrypt('password'),
            'role' => 'enseignant',
            'statut' => 'actif',
        ]);

        $this->parentUser = User::create([
            'tenant_id' => $tenantId,
            'etablissement_id' => $this->etablissement->id,
            'name' => 'Parent Koffi',
            'email' => 'parent-test@test.com',
            'password' => bcrypt('password'),
            'role' => 'parent',
            'statut' => 'actif',
        ]);

        // Enseignant
        $this->enseignant = Enseignant::create([
            'tenant_id' => $tenantId,
            'etablissement_id' => $this->etablissement->id,
            'user_id' => $this->enseignantUser->id,
            'nom' => 'Diop',
            'prenoms' => 'Mamadou',
            'email' => $this->enseignantUser->email,
            'statut' => 'actif',
        ]);

        $this->enseignant->classes()->attach($this->classe->id);
        $this->enseignant->matieres()->attach($this->matiereMath->id);

        // Élève rattaché au parent
        $this->eleve = Eleve::create([
            'tenant_id' => $tenantId,
            'etablissement_id' => $this->etablissement->id,
            'classe_id' => $this->classe->id,
            'parent_id' => $this->parentUser->id,
            'matricule' => 'MAT-TEST-001',
            'nom' => 'Koffi',
            'prenom' => 'Aya',
            'date_naissance' => '2008-05-15',
            'sexe' => 'F',
            'statut' => 'actif',
        ]);

        $this->eleveUser = User::create([
            'tenant_id' => $tenantId,
            'etablissement_id' => $this->etablissement->id,
            'eleve_id' => $this->eleve->id,
            'name' => 'Aya Koffi',
            'email' => 'eleve-test@test.com',
            'password' => bcrypt('password'),
            'role' => 'eleve',
            'statut' => 'actif',
        ]);
    }

    /**
     * Teste le calcul automatique des moyennes pondérées par BulletinService.
     */
    public function test_bulletin_service_calculates_subject_averages_and_weighted_overall_average(): void
    {
        $service = app(BulletinService::class);

        // Créer 2 notes en Maths (14 et 16 => moyenne 15)
        Note::create([
            'tenant_id' => $this->etablissement->tenant_id,
            'etablissement_id' => $this->etablissement->id,
            'eleve_id' => $this->eleve->id,
            'classe_id' => $this->classe->id,
            'matiere_id' => $this->matiereMath->id,
            'enseignant_id' => $this->enseignant->id,
            'annee_academique_id' => $this->anneeAcademique->id,
            'titre_evaluation' => 'Interro 1',
            'type_evaluation' => Note::TYPE_INTERROGATION,
            'note' => 14.0,
            'periode' => 't1',
            'statut' => Note::STATUT_PUBLIE,
        ]);

        Note::create([
            'tenant_id' => $this->etablissement->tenant_id,
            'etablissement_id' => $this->etablissement->id,
            'eleve_id' => $this->eleve->id,
            'classe_id' => $this->classe->id,
            'matiere_id' => $this->matiereMath->id,
            'enseignant_id' => $this->enseignant->id,
            'annee_academique_id' => $this->anneeAcademique->id,
            'titre_evaluation' => 'Devoir sur table',
            'type_evaluation' => Note::TYPE_DEVOIR,
            'note' => 16.0,
            'periode' => 't1',
            'statut' => Note::STATUT_PUBLIE,
        ]);

        // Créer 1 note en Français (12.0 => moyenne 12)
        Note::create([
            'tenant_id' => $this->etablissement->tenant_id,
            'etablissement_id' => $this->etablissement->id,
            'eleve_id' => $this->eleve->id,
            'classe_id' => $this->classe->id,
            'matiere_id' => $this->matiereFr->id,
            'annee_academique_id' => $this->anneeAcademique->id,
            'titre_evaluation' => 'Composition',
            'type_evaluation' => Note::TYPE_COMPOSITION,
            'note' => 12.0,
            'periode' => 't1',
            'statut' => Note::STATUT_PUBLIE,
        ]);

        // Moyenne Math = 15.0 (coef 4 => 60 pts)
        // Moyenne Fr = 12.0 (coef 2 => 24 pts)
        // Total points = 84, Total coef = 6 => Moyenne générale = 14.00
        $bilan = $service->calculerBilanEleve($this->eleve, 't1', $this->anneeAcademique->id, Note::STATUT_PUBLIE);

        $this->assertEquals(6.0, $bilan['total_coefficients']);
        $this->assertEquals(84.0, $bilan['total_points']);
        $this->assertEquals(14.0, $bilan['moyenne_generale']);
        $this->assertEquals('Assez Bien', $bilan['mention']);
        $this->assertEquals('Admis(e)', $bilan['decision']);
    }

    /**
     * Teste le workflow complet de validation :
     * Saisie (brouillon) -> Soumission (soumis) -> Rejet personnel -> Correction ->
     * Approbation personnel -> Validation finale Client & Publication -> Génération Bulletin.
     */
    public function test_complete_validation_workflow_from_draft_to_publication(): void
    {
        // 1. Saisie Enseignant (Brouillon)
        $this->actingAs($this->enseignantUser);

        $note = Note::create([
            'tenant_id' => $this->etablissement->tenant_id,
            'etablissement_id' => $this->etablissement->id,
            'eleve_id' => $this->eleve->id,
            'classe_id' => $this->classe->id,
            'matiere_id' => $this->matiereMath->id,
            'enseignant_id' => $this->enseignant->id,
            'annee_academique_id' => $this->anneeAcademique->id,
            'titre_evaluation' => 'Devoir 1',
            'type_evaluation' => Note::TYPE_DEVOIR,
            'note' => 15.5,
            'periode' => 't1',
            'statut' => Note::STATUT_BROUILLON,
        ]);

        $this->assertEquals(Note::STATUT_BROUILLON, $note->fresh()->statut);
        $this->assertTrue($note->isModifiableParEnseignant());

        // 2. Soumission Enseignant
        $response = $this->post(route('enseignant.notes.soumettre'), [
            'classe_id' => $this->classe->id,
            'matiere_id' => $this->matiereMath->id,
            'periode' => 't1',
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertEquals(Note::STATUT_SOUMIS, $note->fresh()->statut);
        $this->assertNotNull($note->fresh()->soumis_le);

        // 3. Rejet par le Personnel avec motif
        $this->actingAs($this->personnelUser);
        $rejetResponse = $this->post(route('personnel.notes.rejeter'), [
            'classe_id' => $this->classe->id,
            'matiere_id' => $this->matiereMath->id,
            'periode' => 't1',
            'rejet_motif' => 'Veuillez corriger le barème des questions 3 et 4.',
        ]);
        $rejetResponse->assertSessionHasNoErrors();
        $note->refresh();
        $this->assertEquals(Note::STATUT_REJETE_PERSONNEL, $note->statut);
        $this->assertEquals('Veuillez corriger le barème des questions 3 et 4.', $note->rejet_motif);
        $this->assertTrue($note->isModifiableParEnseignant());

        // 4. Correction et Resoumission par l'Enseignant
        $this->actingAs($this->enseignantUser);
        $note->update(['note' => 16.0]);
        $resubmitResponse = $this->post(route('enseignant.notes.soumettre'), [
            'classe_id' => $this->classe->id,
            'matiere_id' => $this->matiereMath->id,
            'periode' => 't1',
        ]);
        $resubmitResponse->assertSessionHasNoErrors();
        $this->assertEquals(Note::STATUT_SOUMIS, $note->fresh()->statut);

        // 5. Approbation par le Personnel
        $this->actingAs($this->personnelUser);
        $approveResponse = $this->post(route('personnel.notes.approuver'), [
            'classe_id' => $this->classe->id,
            'matiere_id' => $this->matiereMath->id,
            'periode' => 't1',
        ]);
        $approveResponse->assertSessionHasNoErrors();
        $this->assertEquals(Note::STATUT_APPROUVE_PERSONNEL, $note->fresh()->statut);

        // 6. Validation finale et Publication par le Client
        $this->actingAs($this->clientUser);
        $publishResponse = $this->post(route('client.notes.publier'), [
            'classe_id' => $this->classe->id,
            'matiere_id' => $this->matiereMath->id,
            'periode' => 't1',
            'annee_academique_id' => $this->anneeAcademique->id,
        ]);
        $publishResponse->assertSessionHasNoErrors();
        $this->assertEquals(Note::STATUT_PUBLIE, $note->fresh()->statut);
        $this->assertNotNull($note->fresh()->publie_le);

        // Vérifier que le bulletin scolaire a été généré automatiquement
        $bulletin = Bulletin::where('tenant_id', $this->etablissement->tenant_id)
            ->where('eleve_id', $this->eleve->id)
            ->where('trimestre', 't1')
            ->first();

        $this->assertNotNull($bulletin, 'Le bulletin scolaire aurait dû être généré automatiquement.');
        $this->assertEquals(Bulletin::STATUT_PUBLIE, $bulletin->statut);
        $this->assertEquals(16.0, $bulletin->moyenne_generale);
    }

    /**
     * Vérifie que les élèves et parents ne peuvent voir les notes et bulletins QUE s'ils sont publiés.
     */
    public function test_visibility_restriction_for_student_and_parent(): void
    {
        // Créer une note en statut brouillon
        $note = Note::create([
            'tenant_id' => $this->etablissement->tenant_id,
            'etablissement_id' => $this->etablissement->id,
            'eleve_id' => $this->eleve->id,
            'classe_id' => $this->classe->id,
            'matiere_id' => $this->matiereMath->id,
            'annee_academique_id' => $this->anneeAcademique->id,
            'titre_evaluation' => 'Interro confidentielle',
            'type_evaluation' => Note::TYPE_INTERROGATION,
            'note' => 18.0,
            'periode' => 't1',
            'statut' => Note::STATUT_BROUILLON,
        ]);

        // L'élève consulte ses notes
        $this->actingAs($this->eleveUser);
        $responseEleve = $this->get(route('eleve.notes'));
        $responseEleve->assertOk();
        $responseEleve->assertDontSee('Interro confidentielle');

        // Le parent consulte les notes de son enfant
        $this->actingAs($this->parentUser);
        $responseParent = $this->get(route('parent.enfant.notes', $this->eleve));
        $responseParent->assertOk();
        $responseParent->assertDontSee('Interro confidentielle');

        // Passer la note à publié
        $note->update(['statut' => Note::STATUT_PUBLIE, 'publie_le' => Carbon::now()]);

        // Maintenant l'élève et le parent doivent la voir
        $this->actingAs($this->eleveUser);
        $this->get(route('eleve.notes'))->assertSee('Interro confidentielle');

        $this->actingAs($this->parentUser);
        $this->get(route('parent.enfant.notes', $this->eleve))->assertSee('Interro confidentielle');
    }

    /**
     * Teste la règle stricte : 1 matière = 1 enseignant responsable.
     * M. Diop (Mathématiques) NE PEUT PAS saisir, modifier ou soumettre des notes de Français.
     */
    public function test_teacher_cannot_enter_modify_or_submit_notes_for_unassigned_subject(): void
    {
        // Créer l'enseignant de Français (M. Yao)
        $yaoUser = User::create([
            'tenant_id' => $this->etablissement->tenant_id,
            'etablissement_id' => $this->etablissement->id,
            'name' => 'Professeur Yao',
            'email' => 'yao-test@test.com',
            'password' => bcrypt('password'),
            'role' => 'enseignant',
            'statut' => 'actif',
        ]);

        $enseignantYao = Enseignant::create([
            'tenant_id' => $this->etablissement->tenant_id,
            'etablissement_id' => $this->etablissement->id,
            'user_id' => $yaoUser->id,
            'nom' => 'Yao',
            'prenoms' => 'Kouassi',
            'email' => $yaoUser->email,
            'statut' => 'actif',
        ]);
        $enseignantYao->classes()->attach($this->classe->id);
        $enseignantYao->matieres()->attach($this->matiereFr->id);

        // 1. M. Diop (Maths) tente de saisir une note en Français
        $this->actingAs($this->enseignantUser);

        $response = $this->post(route('enseignant.notes.store'), [
            'classe_id' => $this->classe->id,
            'matiere_id' => $this->matiereFr->id, // Matière interdite pour Diop
            'eleve_id' => $this->eleve->id,
            'note' => 15.0,
            'periode' => 't1',
            'titre_evaluation' => 'Essai non autorisé',
            'type_evaluation' => Note::TYPE_DEVOIR,
        ]);

        $response->assertSessionHasErrors('matiere_id');
        $this->assertDatabaseMissing('notes', [
            'titre_evaluation' => 'Essai non autorisé',
        ]);

        // 2. M. Diop tente une saisie groupée en Français
        $bulkResponse = $this->post(route('enseignant.notes.bulk-store'), [
            'classe_id' => $this->classe->id,
            'matiere_id' => $this->matiereFr->id,
            'periode' => 't1',
            'titre_evaluation' => 'Contrôle collectif interdit',
            'type_evaluation' => Note::TYPE_DEVOIR,
            'notes' => [$this->eleve->id => 14.0],
        ]);

        $bulkResponse->assertSessionHas('error');
        $this->assertDatabaseMissing('notes', [
            'titre_evaluation' => 'Contrôle collectif interdit',
        ]);

        // 3. M. Diop tente de soumettre des notes de Français
        $soumettreResponse = $this->post(route('enseignant.notes.soumettre'), [
            'classe_id' => $this->classe->id,
            'matiere_id' => $this->matiereFr->id,
            'periode' => 't1',
        ]);
        $soumettreResponse->assertForbidden();

        // 4. M. Yao (Français) saisit sa propre note avec succès
        $this->actingAs($yaoUser);
        $yaoStoreResponse = $this->post(route('enseignant.notes.store'), [
            'classe_id' => $this->classe->id,
            'matiere_id' => $this->matiereFr->id,
            'eleve_id' => $this->eleve->id,
            'note' => 16.5,
            'periode' => 't1',
            'titre_evaluation' => 'Dissertation 1',
            'type_evaluation' => Note::TYPE_DEVOIR,
        ]);
        $yaoStoreResponse->assertSessionHasNoErrors();

        $noteFr = Note::where('titre_evaluation', 'Dissertation 1')->first();
        $this->assertNotNull($noteFr);
        $this->assertEquals($enseignantYao->id, $noteFr->enseignant_id);
        $this->assertEquals($this->matiereFr->id, $noteFr->matiere_id);

        // 5. M. Diop tente de modifier la note de Français de M. Yao
        $this->actingAs($this->enseignantUser);
        $editResponse = $this->getJson(route('enseignant.notes.edit', $noteFr->id));
        $editResponse->assertForbidden();
    }

    /**
     * Teste la liaison automatique de la matière et attribution stricte de l'enseignant_id.
     */
    public function test_automatic_subject_binding_and_multiple_notes_per_student(): void
    {
        $this->actingAs($this->enseignantUser);

        // 1. Saisie de la 1ère note (Interrogation) sans spécifier matiere_id (liaison auto)
        $response1 = $this->post(route('enseignant.notes.store'), [
            'classe_id' => $this->classe->id,
            'eleve_id' => $this->eleve->id,
            'note' => 13.5,
            'periode' => 't1',
            'titre_evaluation' => 'Interro 1',
            'type_evaluation' => Note::TYPE_INTERROGATION,
        ]);
        $response1->assertSessionHasNoErrors();

        // 2. Saisie de la 2ème note (Devoir surveillé) pour le MÊME élève dans la MÊME matière
        $response2 = $this->post(route('enseignant.notes.store'), [
            'classe_id' => $this->classe->id,
            'eleve_id' => $this->eleve->id,
            'note' => 17.5,
            'periode' => 't1',
            'titre_evaluation' => 'Devoir surveillé 1',
            'type_evaluation' => Note::TYPE_DEVOIR,
        ]);
        $response2->assertSessionHasNoErrors();

        // Vérification en base
        $notes = Note::where('eleve_id', $this->eleve->id)
            ->where('periode', 't1')
            ->where('matiere_id', $this->matiereMath->id)
            ->get();

        $this->assertCount(2, $notes);
        foreach ($notes as $n) {
            $this->assertEquals($this->enseignant->id, $n->enseignant_id, 'enseignant_id doit être lié à l\'enseignant connecté.');
            $this->assertEquals($this->matiereMath->id, $n->matiere_id, 'matiere_id doit être lié à la matière assignée.');
            $this->assertEquals(Note::STATUT_BROUILLON, $n->statut);
        }

        // Moyenne arithmétique calculée : (13.5 + 17.5) / 2 = 15.5
        $service = app(BulletinService::class);
        $moyenne = $service->calculerMoyenneMatiere($this->eleve->id, $this->matiereMath->id, 't1', $this->anneeAcademique->id, Note::STATUT_BROUILLON);
        $this->assertEquals(15.5, $moyenne);
    }

    public function test_teacher_can_edit_published_note_and_restart_validation(): void
    {
        $note = Note::create([
            'tenant_id' => $this->etablissement->tenant_id,
            'etablissement_id' => $this->etablissement->id,
            'eleve_id' => $this->eleve->id,
            'classe_id' => $this->classe->id,
            'matiere_id' => $this->matiereMath->id,
            'enseignant_id' => $this->enseignant->id,
            'annee_academique_id' => $this->anneeAcademique->id,
            'titre_evaluation' => 'Devoir publié',
            'type_evaluation' => Note::TYPE_DEVOIR,
            'note' => 14,
            'periode' => 't1',
            'statut' => Note::STATUT_PUBLIE,
            'approuve_personnel_le' => Carbon::now(),
            'approuve_personnel_id' => $this->personnelUser->id,
            'publie_le' => Carbon::now(),
            'publie_par_id' => $this->clientUser->id,
        ]);

        app(BulletinService::class)->synchroniserEtPublierBulletins(
            $this->etablissement->tenant_id,
            $this->classe->id,
            't1',
            $this->anneeAcademique->id,
            $this->clientUser->id
        );

        $this->actingAs($this->enseignantUser);
        $response = $this->putJson(route('enseignant.notes.update', $note), [
            'eleve_id' => $this->eleve->id,
            'classe_id' => $this->classe->id,
            'matiere_id' => $this->matiereMath->id,
            'titre_evaluation' => 'Devoir publié corrigé',
            'type_evaluation' => Note::TYPE_DEVOIR,
            'note' => 18,
            'periode' => 't1',
        ]);

        $response->assertOk();
        $updated = $note->fresh();
        $this->assertEquals(18.0, $updated->note);
        $this->assertEquals(Note::STATUT_SOUMIS, $updated->statut);
        $this->assertNull($updated->approuve_personnel_le);
        $this->assertNull($updated->publie_le);
        $this->assertDatabaseHas('note_historiques', [
            'note_id' => $note->id,
            'action' => 'modification',
        ]);
        $this->assertEquals(Bulletin::STATUT_EN_ATTENTE, Bulletin::first()->statut);
    }

    public function test_teacher_can_delete_published_note_and_recalculate_bulletin(): void
    {
        $note = Note::create([
            'tenant_id' => $this->etablissement->tenant_id,
            'etablissement_id' => $this->etablissement->id,
            'eleve_id' => $this->eleve->id,
            'classe_id' => $this->classe->id,
            'matiere_id' => $this->matiereMath->id,
            'enseignant_id' => $this->enseignant->id,
            'annee_academique_id' => $this->anneeAcademique->id,
            'titre_evaluation' => 'Note à supprimer',
            'type_evaluation' => Note::TYPE_DEVOIR,
            'note' => 12,
            'periode' => 't1',
            'statut' => Note::STATUT_PUBLIE,
        ]);

        app(BulletinService::class)->synchroniserEtPublierBulletins(
            $this->etablissement->tenant_id,
            $this->classe->id,
            't1',
            $this->anneeAcademique->id,
            $this->clientUser->id
        );

        $this->actingAs($this->enseignantUser);
        $response = $this->deleteJson(route('enseignant.notes.destroy', $note));

        $response->assertOk();
        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
        $this->assertDatabaseHas('note_historiques', [
            'note_id' => $note->id,
            'action' => 'suppression',
        ]);
        $this->assertEquals(Bulletin::STATUT_EN_ATTENTE, Bulletin::first()->statut);
    }
}
