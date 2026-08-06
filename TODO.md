# TODO - Appréciation automatique des notes

## Étapes
- [x] Analyser l'architecture existante (modèle, contrôleur, vues, logique d'appréciation)
- [x] 1. Ajouter `noteAppreciation()` comme source unique dans `app/Services/BulletinService.php`
- [x] 2. Mettre à jour `EnseignantNoteController` :
  - [x] `store()` : recalculer l'appréciation depuis la note
  - [x] `update()` : recalculer l'appréciation depuis la nouvelle note
  - [x] `index()` : afficher l'appréciation recalculée (jamais une ancienne valeur)
  - [x] `edit()` : renvoyer l'appréciation recalculée
- [x] 3. Modifier `resources/views/enseignant/notes/index.blade.php` :
  - [x] Supprimer le `<textarea name="appreciation">` éditable
  - [x] Retirer les références à `appreciationInput` (resetForm, editGrade)
  - [x] Conserver l'aperçu "Appréciation suggérée" en lecture seule

## Vérification
- [x] PHP syntax OK (EnseignantNoteController, BulletinService)
- [ ] Tester l'ajout d'une note (aucun champ Appréciation)
- [ ] Tester la modification d'une note (recalcul automatique)
- [ ] Vérifier que le tableau affiche toujours l'appréciation correspondant à la note
