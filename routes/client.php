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
use App\Http\Controllers\Client\SeriesController;
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

        // Bulletin (module complet)


        Route::patch('/personnel/{personnel}/block', [PersonnelController::class, 'block'])
            ->name('personnel.block');
        Route::patch('/personnel/{personnel}/unblock', [PersonnelController::class, 'unblock'])
            ->name('personnel.unblock');


        Route::get('/comptabilite', [ComptabiliteController::class, 'index'])->name('comptabilite');
        Route::post('/comptabilite/scolarite', [ComptabiliteController::class, 'storeScolarite'])->name('comptabilite.scolarite.store');
        Route::post('/comptabilite/depense', [ComptabiliteController::class, 'storeDepense'])->name('comptabilite.depense.store');
        Route::put('/comptabilite/depense/{depense}', [ComptabiliteController::class, 'updateDepense'])->name('comptabilite.depense.update');
        Route::delete('/comptabilite/depense/{depense}', [ComptabiliteController::class, 'destroyDepense'])->name('comptabilite.depense.destroy');

        Route::get('/classe', [ClasseController::class, 'index'])->name('classe');
        Route::post('/classe', [ClasseController::class, 'store'])->name('classe.store');
        Route::put('/classe/{classe}', [ClasseController::class, 'update'])->name('classe.update');
        Route::delete('/classe/{classe}', [ClasseController::class, 'destroy'])->name('classe.destroy');

        Route::get('/eleve', [EleveController::class, 'index'])->name('eleve');
        Route::get('/eleve/{eleve}/photo', [EleveController::class, 'photo'])->name('eleve.photo');
        Route::post('/eleve', [EleveController::class, 'store'])->name('eleve.store');
        Route::put('/eleve/{eleve}', [EleveController::class, 'update'])->name('eleve.update');
        Route::delete('/eleve/{eleve}', [EleveController::class, 'destroy'])->name('eleve.destroy');

        Route::get('/enseignant', [EnseignantController::class, 'index'])->name('enseignant');
        Route::post('/enseignant', [EnseignantController::class, 'store'])->name('enseignant.store');
        Route::put('/enseignant/{enseignant}', [EnseignantController::class, 'update'])->name('enseignant.update');
        Route::delete('/enseignant/{enseignant}', [EnseignantController::class, 'destroy'])->name('enseignant.destroy');

        Route::get('/matiere', [MatiereController::class, 'index'])->name('matiere');
        Route::get('/matiere/by-serie/{serieId}', [MatiereController::class, 'getBySerie'])->name('matiere.bySerie');
        Route::get('/matiere/all', [MatiereController::class, 'getAll'])->name('matiere.all');
        Route::post('/matiere', [MatiereController::class, 'store'])->name('matiere.store');
        Route::put('/matiere/{matiere}', [MatiereController::class, 'update'])->name('matiere.update');
        Route::delete('/matiere/{matiere}', [MatiereController::class, 'destroy'])->name('matiere.destroy');


        Route::get('/series/by-classe/{classe}', [SeriesController::class, 'byClasse'])->name('series.by-classe');
        Route::get('/series/{series}/disciplines', [SeriesController::class, 'disciplines'])->name('series.disciplines');
        Route::post('/series/{series}/disciplines', [SeriesController::class, 'storeDiscipline'])->name('series.disciplines.store');
        Route::put('/series/{series}/disciplines/{matiere}', [SeriesController::class, 'updateDiscipline'])->name('series.disciplines.update');
        Route::delete('/series/{series}/disciplines/{matiere}', [SeriesController::class, 'destroyDiscipline'])->name('series.disciplines.destroy');
        Route::resource('series', SeriesController::class)->except(['show', 'create', 'edit']);

        Route::get('/niveaux', [NiveauxController::class, 'index'])->name('niveaux');
        Route::post('/niveaux', [NiveauxController::class, 'store'])->name('niveaux.store');
        Route::put('/niveaux/{niveau}', [NiveauxController::class, 'update'])->name('niveaux.update');
        Route::delete('/niveaux/{niveau}', [NiveauxController::class, 'destroy'])->name('niveaux.destroy');
        Route::get('/note', [NoteController::class, 'index'])->name('note');

        Route::prefix('bulletin')->name('bulletin.')->group(function () {
            Route::get('/', [BulletinController::class, 'index'])->name('index');
            Route::get('/create', [BulletinController::class, 'create'])->name('create');
            Route::get('/student-data', [BulletinController::class, 'studentData'])->name('student-data');
            Route::post('/', [BulletinController::class, 'store'])->name('store');
            Route::get('/{bulletin}', [BulletinController::class, 'show'])->name('show');
            Route::get('/{bulletin}/edit', [BulletinController::class, 'edit'])->name('edit');
            Route::match(['put', 'patch'], '/{bulletin}', [BulletinController::class, 'update'])->name('update');
            Route::delete('/{bulletin}', [BulletinController::class, 'destroy'])->name('destroy');
            Route::get('/{bulletin}/download', [BulletinController::class, 'download'])->name('download');

        });

        Route::get('/parametres', [ParametresController::class, 'index'])->name('parametres');
    });
