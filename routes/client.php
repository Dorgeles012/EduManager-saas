<?php

use App\Http\Controllers\Client\AbonnementController;
use App\Http\Controllers\Client\AnneeController;
use App\Http\Controllers\Client\BulletinController;
use App\Http\Controllers\Client\ComptabiliteController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\ClasseController;
use App\Http\Controllers\Client\EleveController;
use App\Http\Controllers\Client\EnseignantController;
use App\Http\Controllers\Client\MatiereController;
use App\Http\Controllers\Client\NiveauxController;
use App\Http\Controllers\Client\NoteController;
use App\Http\Controllers\Client\PersonnelController;
use App\Http\Controllers\Client\ParametresController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // CRUDs existants
        Route::resource('abonnement', AbonnementController::class);
        Route::resource('annee', AnneeController::class);
        Route::resource('personnel', PersonnelController::class);

        // Pages (index minimal) pour compléter la sidebar
        Route::get('/comptabilite', [ComptabiliteController::class, 'index'])->name('comptabilite');
        Route::get('/classe', [ClasseController::class, 'index'])->name('classe');
        Route::get('/eleve', [EleveController::class, 'index'])->name('eleve');
        Route::get('/enseignant', [EnseignantController::class, 'index'])->name('enseignant');
        Route::get('/matiere', [MatiereController::class, 'index'])->name('matiere');
        Route::get('/niveaux', [NiveauxController::class, 'index'])->name('niveaux');
        Route::get('/note', [NoteController::class, 'index'])->name('note');
        Route::get('/bulletin', [BulletinController::class, 'index'])->name('bulletin');
        Route::get('/parametres', [ParametresController::class, 'index'])->name('parametres');

        // Remarque: les items "Etablissements" sont pour l'instant en attente
        // (aucune vue+controller dédiés côté client dans le repo fourni).
    });




