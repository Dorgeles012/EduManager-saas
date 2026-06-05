# Plan: correction du systeme d'abonnements

## Objectif

Mettre en place un flux complet ou:
- les plans crees/modifies/supprimes par le Super Admin sont visibles immediatement cote client;
- la page `/client/abonnements` affiche toutes les cartes depuis la table `plans`;
- une souscription cree une ligne `subscriptions` et une ligne `payments`;
- le message SweetAlert2 de succes est declenche apres un vrai redirect Laravel.

## Constat sur le code actuel

- Admin: `routes/web.php` pointe `/sadmin/abonnement` vers `App\Http\Controllers\Sadmin\PlanController`.
- CRUD admin reel: `Route::resource('plans', PlanController::class)`.
- Client: `routes/client.php` pointe `/client/abonnements` vers `App\Http\Controllers\Client\AbonnementController@index`.
- La vue client affiche actuellement un seul `$featuredPlan`, pas une collection `$plans`.
- La souscription client est simulee en JavaScript; aucun POST backend ne cree d'abonnement.
- Le schema reel est encore majoritairement en francais:
  - `plans.nom`, `plans.prix`, `plans.statut`
  - `subscriptions.date_debut`, `subscriptions.date_fin`, `subscriptions.statut`
  - `payments.montant`, `payments.methode_paiement`, `payments.reference_paiement`, `payments.date_paiement`
- La demande utilisateur cible un schema anglais:
  - `plans.name`, `plans.price`, `plans.duration`, `plans.duration_type`, `plans.status`
  - `subscriptions.amount`, `start_date`, `end_date`, `status`
  - `payments.amount`, `payment_method`, `transaction_id`, `payment_date`, `status`

## Approche recommandee

Passer le module abonnements vers le schema anglais demande, avec une migration additive/normalisatrice et un backfill des donnees existantes. Garder les routes UI francaises existantes quand elles sont deja utilisees, surtout `client.abonnement.index`, pour ne pas casser la sidebar.

## Fichiers a modifier

- `database/migrations/*_normalize_subscription_billing_schema.php`
- `app/Models/Plan.php`
- `app/Models/Subscription.php`
- `app/Models/Payment.php` a creer
- `app/Http/Requests/Sadmin/PlanStoreRequest.php`
- `app/Http/Controllers/Sadmin/PlanController.php`
- `app/Http/Controllers/Client/AbonnementController.php`
- `app/Http/Controllers/Sadmin/DashboardController.php`
- `app/Http/Controllers/Client/DashboardController.php`
- `routes/client.php`
- `resources/views/sadmin/abonnement.blade.php`
- `resources/views/client/abonnements.blade.php`

## Etapes d'implementation

1. Creer une migration de normalisation

Ajouter les colonnes manquantes sans modifier les anciennes migrations deja creees:

- `plans`: ajouter `name`, `price`, `duration`, `duration_type`, `status` si absents.
- Backfill:
  - `name` depuis `nom`
  - `price` depuis `prix`
  - `status` depuis `statut` (`active` => true/active selon type retenu)
  - `duration` depuis une valeur par defaut ou depuis `subscription_types.default_duration` si exploitable
  - `duration_type` depuis le type d'abonnement si possible: mensuel => months, annuel => years, sinon months
- `subscriptions`: ajouter `amount`, `start_date`, `end_date`, `status` si absents.
- Backfill:
  - `start_date` depuis `date_debut`
  - `end_date` depuis `date_fin`
  - `status` depuis `statut`
- `payments`: ajouter `user_id`, `amount`, `payment_method`, `transaction_id`, `status`, `payment_date` si absents.
- Backfill:
  - `amount` depuis `montant`
  - `payment_method` depuis `methode_paiement` ou `manual`
  - `transaction_id` depuis `reference_paiement` ou UUID
  - `payment_date` depuis `date_paiement`
  - `status` depuis `paid`

Eviter de supprimer immediatement les colonnes francaises pour limiter le risque sur les autres pages.

2. Mettre a jour les modeles

`Plan`:
- `fillable`: `name`, `description`, `price`, `duration`, `duration_type`, `status`
- relations:
  - `subscriptions()`
  - optionnel: garder `subscriptionType()` si l'admin utilise encore les types.

`Subscription`:
- `fillable`: `user_id`, `plan_id`, `amount`, `start_date`, `end_date`, `status`
- casts: `start_date`, `end_date`
- relations: `user()`, `plan()`, `payments()`

`Payment`:
- creer le modele
- `fillable`: `user_id`, `subscription_id`, `amount`, `payment_method`, `status`, `transaction_id`, `payment_date`
- casts: `payment_date`
- relations: `user()`, `subscription()`

3. Adapter le Super Admin

Dans `PlanStoreRequest`:
- valider `name`, `price`, `duration`, `duration_type`, `status`, `description/features`.
- accepter temporairement les anciens champs `nom`, `prix`, `statut`, `type` si la vue n'est pas entierement migree dans la meme passe.

Dans `PlanController`:
- `index()` charge tous les plans.
- `store()` cree un plan dans les colonnes anglaises.
- `update()` met a jour les colonnes anglaises.
- `destroy()` supprime le plan; la page client reflete aussitot la suppression.
- conserver la logique de features dans `description` sous forme de lignes, deja compatible avec la vue.

Dans `resources/views/sadmin/abonnement.blade.php`:
- remplacer les noms de champs principaux par `name`, `price`, `duration`, `duration_type`, `status`.
- afficher/modifier les plans via ces proprietes.
- garder le conteneur dynamique de features.

4. Rendre la page client dynamique

Dans `Client\AbonnementController@index`:
- remplacer `$featuredPlan` par `$plans`.
- charger:
  - `Plan::where('status', true/active)->orderBy('price')->get()`
  - selon le type exact choisi dans la migration.

Dans `resources/views/client/abonnements.blade.php`:
- remplacer la carte unique par `@foreach($plans as $plan)`.
- ne garder aucun plan code en dur.
- afficher:
  - `name`
  - `description` ou features ligne par ligne
  - `price`
  - `duration` + `duration_type`
- ajouter un formulaire POST par plan vers la route de souscription.

5. Ajouter la route de souscription

Dans `routes/client.php`, dans le groupe `auth + client` existant:

```php
Route::post('/abonnements/{plan}/subscribe', [AbonnementController::class, 'subscribe'])
    ->name('abonnements.subscribe');
```

Conserver:
- `client.abonnements.index`
- `client.abonnement.index`

6. Implementer `subscribe()`

Dans `Client\AbonnementController`:
- utiliser route model binding `Plan $plan`.
- verifier que l'utilisateur est authentifie via le middleware deja present et/ou `Auth::check()`.
- refuser un plan inactif.
- calculer:
  - `start_date = now()`
  - `end_date` selon `duration_type`:
    - `days`: `addDays(duration)`
    - `months`: `addMonths(duration)`
    - `years`: `addYears(duration)`
- creer `Subscription` et `Payment` dans une `DB::transaction`.
- utiliser `Str::uuid()` pour `transaction_id`.
- rediriger vers `client.abonnements.index` avec `with('success', 'Paiement effectue')`.

7. SweetAlert2

Dans la vue client:
- supprimer le faux `setTimeout` qui simule le paiement.
- garder une confirmation SweetAlert avant soumission du formulaire si souhaite.
- apres redirect, afficher:

```blade
@if(session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Paiement effectue',
    text: 'Votre abonnement a ete active avec succes.',
    confirmButtonText: 'OK'
});
</script>
@endif
```

8. Mettre a jour les dashboards

Dans:
- `app/Http/Controllers/Sadmin/DashboardController.php`
- `app/Http/Controllers/Client/DashboardController.php`

Remplacer les sommes `payments.montant` par `payments.amount`.

Si le filtrage par `tenant_id` est encore necessaire, le garder tant que la colonne existe. Sinon, filtrer via `payments.user_id` et la table `users`.

## Verification

1. `php -l` sur les controleurs et modeles modifies.
2. `php artisan migrate`.
3. `php artisan route:list` et verifier:
   - `client.abonnements.index`
   - `client.abonnements.subscribe`
   - `client.abonnement.index`
   - routes `plans.*`
4. `php artisan view:cache`, puis `php artisan view:clear`.
5. Test manuel admin:
   - creer un plan actif;
   - modifier son prix/features/duree;
   - supprimer un plan.
6. Test manuel client:
   - ouvrir `/client/abonnements`;
   - verifier que toutes les cartes actives s'affichent;
   - souscrire a un plan;
   - verifier le SweetAlert de succes.
7. Verification base:
   - une ligne `subscriptions` avec `user_id`, `plan_id`, `amount`, `start_date`, `end_date`, `status=active`;
   - une ligne `payments` avec `user_id`, `subscription_id`, `amount`, `payment_method=manual`, `status=paid`, `transaction_id`, `payment_date`.

## Risques a surveiller

- Les anciennes colonnes francaises sont encore utilisees dans plusieurs dashboards et vues.
- `Sadmin\SubscriptionController` semble obsolete ou non route; ne pas construire le nouveau flux dessus.
- La sidebar client utilise `client.abonnement.index`; ne pas supprimer la resource existante sans ajuster la sidebar.
- `tenant_id` reste utilise dans les dashboards; ne pas le supprimer dans la premiere passe.
