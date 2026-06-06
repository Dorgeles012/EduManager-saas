# TODO - Statut utilisateur par défaut + blocage/déblocage

- [x] Ajouter des constantes de statut + boot `creating` dans `app/Models/User.php` pour forcer `statut=actif` si vide.
- [x] Ajouter des méthodes métier `block()` / `unblock()` (et éventuellement `activate()`) dans `User`.

- [x] Corriger `app/Http/Middleware/CheckUserStatut.php` pour bloquer sur `bloqué` (au lieu de `blocked`).
- [x] Corriger `app/Http/Middleware/check.user.statut.php` si utilisé, pour cohérence `bloqué`.
- [x] Mettre à jour `app/Http/Controllers/Sadmin/SadminController.php` et `ClientController.php` pour utiliser `actif/bloqué` et appeler les méthodes `block()/unblock()`.
- [x] Corriger la vue `resources/views/sadmin/clients/index.blade.php` pour utiliser `actif/bloqué` (sinon le mauvais bouton s’affiche et l’action semble ne pas fonctionner).

- [x] Lancer tests / sanity check via artisan (migrations déjà faites) et exécuter `php artisan test` (si applicable).


