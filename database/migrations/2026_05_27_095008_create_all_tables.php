<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TABLES SANS DÉPENDANCES
        
        // Tenants (clients)
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('nom_entreprise');
            $table->string('nom_responsable')->nullable();
            $table->string('prenom_responsable')->nullable();
            $table->string('email')->unique();
            $table->string('telephone', 50)->nullable();
            $table->text('adresse')->nullable();
            $table->enum('statut', ['active', 'suspended', 'blocked'])->default('active');
            $table->timestamps();
        });

        // Sadmins
        Schema::create('sadmins', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('telephone', 50)->nullable();
            $table->text('image')->nullable();
            $table->enum('statut', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Plans
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->integer('prix');
            $table->integer('max_ecoles')->default(1);
            $table->integer('max_users')->default(10);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. TABLES AVEC tenant_id (SANS FOREIGN KEY)
        
        // Users
        Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('tenant_id')->default(1);
        $table->string('nom');
        $table->string('prenom')->nullable();
        $table->string('email')->unique();
        $table->string('telephone', 50)->nullable();
        $table->string('password');
        $table->text('image')->nullable();
        $table->enum('role', ['SADMIN', 'CLIENT', 'PERSONNEL', 'ENSEIGNANT', 'PARENT']);
        $table->enum('statut', ['active', 'inactive', 'blocked'])->default('active');
        $table->timestamps();
});

        // Etablissements
        Schema::create('etablissements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('nom');
            $table->string('acronyme', 100)->nullable();
            $table->enum('type_etablissement', ['primaire', 'college', 'lycee', 'universite', 'grande_ecole']);
            $table->string('email')->nullable();
            $table->string('telephone', 50)->nullable();
            $table->text('adresse')->nullable();
            $table->text('logo')->nullable();
            $table->enum('statut', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Subscriptions
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('plan_id');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->enum('statut', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamps();
        });

        // Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('subscription_id');
            $table->integer('montant');
            $table->string('methode_paiement', 100)->nullable();
            $table->string('reference_paiement')->nullable();
            $table->date('date_paiement');
            $table->timestamps();
        });

        // 3. MATIERES
        
        Schema::create('matieres', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('nom');
            $table->integer('coefficient')->default(1);
            $table->timestamps();
        });

        // 4. NIVEAUX ET FILIERES
        
        Schema::create('niveaux', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('etablissement_id');
            $table->string('nom');
            $table->timestamps();
        });

        Schema::create('filieres', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('etablissement_id');
            $table->string('nom');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 5. CLASSES
        
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('etablissement_id');
            $table->unsignedBigInteger('niveau_id');
            $table->unsignedBigInteger('filiere_id')->nullable();
            $table->string('nom');
            $table->integer('capacite')->default(50);
            $table->timestamps();
        });

        // 6. ENSEIGNANTS
        
        Schema::create('enseignants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('etablissement_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nom');
            $table->string('prenoms')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone', 50)->nullable();
            $table->string('password')->nullable();
            $table->string('specialite')->nullable();
            $table->enum('statut', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Enseignant Matiere (pivot)
        Schema::create('enseignant_matiere', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enseignant_id');
            $table->unsignedBigInteger('matiere_id');
        });

        // 7. PERSONNEL
        
        Schema::create('personnel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('etablissement_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nom')->nullable();
            $table->string('prenom')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone', 50)->nullable();
            $table->string('password')->nullable();
            $table->string('fonction')->nullable();
            $table->enum('statut', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // 8. PARENTS ET ELEVES
        
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nom')->nullable();
            $table->string('prenom')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone', 50)->nullable();
            $table->text('adresse')->nullable();
            $table->timestamps();
        });

        Schema::create('eleves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('etablissement_id');
            $table->unsignedBigInteger('classe_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('matricule')->unique();
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->enum('sexe', ['M', 'F'])->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance')->nullable();
            $table->string('ancien_etablissement')->nullable();
            $table->text('photo')->nullable();
            $table->enum('statut', ['actif', 'suspendu', 'transfert'])->default('actif');
            $table->timestamps();
        });

        // 9. NOTES ET BULLETINS
        
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('eleve_id');
            $table->unsignedBigInteger('classe_id');
            $table->unsignedBigInteger('matiere_id');
            $table->decimal('note', 5, 2);
            $table->string('periode', 100)->nullable();
            $table->text('appreciation')->nullable();
            $table->timestamps();
        });

        Schema::create('bulletins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('eleve_id');
            $table->unsignedBigInteger('classe_id');
            $table->decimal('moyenne', 5, 2)->nullable();
            $table->integer('rang')->nullable();
            $table->text('appreciation')->nullable();
            $table->string('trimestre', 100)->nullable();
            $table->timestamps();
        });

        // 10. SCOLARITE ET VERSEMENTS
        
        Schema::create('scolarites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('eleve_id');
            $table->integer('montant_total')->default(0);
            $table->integer('montant_paye')->default(0);
            $table->integer('reste')->default(0);
            $table->string('annee_scolaire', 100)->nullable();
            $table->enum('statut', ['paye', 'partiel', 'impaye'])->default('impaye');
            $table->timestamps();
        });

        Schema::create('versements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('scolarite_id');
            $table->integer('montant');
            $table->date('date_versement')->nullable();
            $table->string('methode', 100)->nullable();
            $table->timestamps();
        });

        // 11. AUTRES TABLES
        
        Schema::create('emploi_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('classe_id');
            $table->unsignedBigInteger('matiere_id');
            $table->unsignedBigInteger('enseignant_id');
            $table->enum('jour', ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi']);
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->string('salle', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->enum('type', ['income', 'expense']);
            $table->integer('montant');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('titre')->nullable();
            $table->text('message')->nullable();
            $table->enum('statut', ['unread', 'read'])->default('unread');
            $table->timestamps();
        });

        Schema::create('annee_academique', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('etablissement_id');
            $table->string('libelle');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->enum('statut', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('code', 10);
            $table->dateTime('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $tables = [
            'password_resets', 'annee_academique', 'notifications', 'messages',
            'transactions', 'emploi_temps', 'versements', 'scolarites',
            'bulletins', 'notes', 'eleves', 'parents', 'personnel',
            'enseignant_matiere', 'enseignants', 'classes', 'filieres',
            'niveaux', 'matieres', 'payments', 'subscriptions',
            'etablissements', 'users', 'plans', 'sadmins', 'tenants'
        ];
        
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};