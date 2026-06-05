<?php

use App\Http\Controllers\Client\AbonnementController;
use App\Http\Controllers\Client\AnneeController;
use App\Http\Controllers\Client\BulletinController;
use App\Http\Controllers\Client\ClasseController;
use App\Http\Controllers\Client\ComptabiliteController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\EleveController;
use App\Http\Controllers\Client\EnseignantController;
use App\Http\Controllers\Client\MatiereController;
use App\Http\Controllers\Client\NiveauxController;
use App\Http\Controllers\Client\NoteController;
use App\Http\Controllers\Client\ParametresController;
use App\Http\Controllers\Client\PersonnelController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/abonnements', [AbonnementController::class, 'index'])->name('abonnements.index');
        Route::get('/abonnements/create', [AbonnementController::class, 'create'])->name('abonnements.create');
        Route::post('/abonnements', [AbonnementController::class, 'store'])->name('abonnements.store');

        // Routes historiques utilisees par la sidebar.
        Route::get('/abonnement', [AbonnementController::class, 'index'])->name('abonnement.index');
        Route::post('/abonnement', [AbonnementController::class, 'store'])->name('abonnement.store');
        Route::resource('abonnement', AbonnementController::class)->except(['index', 'store']);

        Route::resource('annee', AnneeController::class);
        Route::resource('personnel', PersonnelController::class);

        Route::get('/comptabilite', [ComptabiliteController::class, 'index'])->name('comptabilite');
        Route::get('/classe', [ClasseController::class, 'index'])->name('classe');
        Route::get('/eleve', [EleveController::class, 'index'])->name('eleve');
        Route::get('/enseignant', [EnseignantController::class, 'index'])->name('enseignant');
        Route::get('/matiere', [MatiereController::class, 'index'])->name('matiere');
        Route::get('/niveaux', [NiveauxController::class, 'index'])->name('niveaux');
        Route::get('/note', [NoteController::class, 'index'])->name('note');
        Route::get('/bulletin', [BulletinController::class, 'index'])->name('bulletin');
        Route::get('/parametres', [ParametresController::class, 'index'])->name('parametres');
    });
