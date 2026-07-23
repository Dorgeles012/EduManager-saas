<?php

use App\Http\Controllers\Personnel\PersonnelDashboardController;
use App\Http\Controllers\Personnel\PersonnelAnneeAcademiqueController;
use App\Http\Controllers\Personnel\PersonnelSeriesController;
use App\Http\Controllers\Personnel\PersonnelClasseController;
use App\Http\Controllers\Personnel\PersonnelEleveController;
use App\Http\Controllers\Personnel\PersonnelMatiereController;
use App\Http\Controllers\Personnel\PersonnelComptabiliteController;
use App\Http\Controllers\Personnel\PersonnelBulletinController;
use App\Http\Controllers\Personnel\PersonnelParametreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'status', 'role:personnel'])
    ->prefix('personnel')
    ->name('personnel.')
->group(function () {

        // Déconnexion (POST requis par le layout)
        Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

        // Dashboard
        Route::get('/dashboard', [PersonnelDashboardController::class, 'index'])->name('dashboard');

        // Année académique
        Route::resource('annee-academique', PersonnelAnneeAcademiqueController::class)
            ->names('annee-academique');

        // Séries
        Route::get('/series/{series}/disciplines', [PersonnelSeriesController::class, 'disciplines'])->name('series.disciplines');
        Route::post('/series/{series}/disciplines', [PersonnelSeriesController::class, 'storeDiscipline'])->name('series.disciplines.store');
        Route::put('/series/{series}/disciplines/{matiere}', [PersonnelSeriesController::class, 'updateDiscipline'])->name('series.disciplines.update');
        Route::delete('/series/{series}/disciplines/{matiere}', [PersonnelSeriesController::class, 'destroyDiscipline'])->name('series.disciplines.destroy');
        Route::resource('series', PersonnelSeriesController::class)->names('series');

        // Classes
        Route::get('/classes', [PersonnelClasseController::class, 'index'])->name('classes.index');
        Route::post('/classes', [PersonnelClasseController::class, 'store'])->name('classes.store');
        Route::put('/classes/{classe}', [PersonnelClasseController::class, 'update'])->name('classes.update');
        Route::delete('/classes/{classe}', [PersonnelClasseController::class, 'destroy'])->name('classes.destroy');

        // Élèves
        Route::get('/eleves', [PersonnelEleveController::class, 'index'])->name('eleves.index');
        Route::get('/eleves/{eleve}/photo', [PersonnelEleveController::class, 'photo'])->name('eleves.photo');
        Route::post('/eleves', [PersonnelEleveController::class, 'store'])->name('eleves.store');
        Route::put('/eleves/{eleve}', [PersonnelEleveController::class, 'update'])->name('eleves.update');
        Route::delete('/eleves/{eleve}', [PersonnelEleveController::class, 'destroy'])->name('eleves.destroy');

        // Matières
        Route::get('/matieres/by-serie/{serieId}', [PersonnelMatiereController::class, 'getBySerie'])->name('matieres.by-serie');
        Route::get('/matieres/all', [PersonnelMatiereController::class, 'getAll'])->name('matieres.all');
        Route::get('/matieres', [PersonnelMatiereController::class, 'index'])->name('matieres.index');
        Route::post('/matieres', [PersonnelMatiereController::class, 'store'])->name('matieres.store');
        Route::put('/matieres/{matiere}', [PersonnelMatiereController::class, 'update'])->name('matieres.update');
        Route::delete('/matieres/{matiere}', [PersonnelMatiereController::class, 'destroy'])->name('matieres.destroy');

        // Comptabilité
        Route::get('/comptabilite', [PersonnelComptabiliteController::class, 'index'])->name('comptabilite.index');
        Route::post('/comptabilite/scolarite', [PersonnelComptabiliteController::class, 'storeScolarite'])->name('comptabilite.scolarite.store');
        Route::post('/comptabilite/depense', [PersonnelComptabiliteController::class, 'storeDepense'])->name('comptabilite.depense.store');
        Route::put('/comptabilite/depense/{depense}', [PersonnelComptabiliteController::class, 'updateDepense'])->name('comptabilite.depense.update');
        Route::delete('/comptabilite/depense/{depense}', [PersonnelComptabiliteController::class, 'destroyDepense'])->name('comptabilite.depense.destroy');

        // Bulletin
        Route::prefix('bulletin')->name('bulletin.')->group(function () {
            Route::get('/', [PersonnelBulletinController::class, 'index'])->name('index');
            Route::get('/create', [PersonnelBulletinController::class, 'create'])->name('create');
            Route::get('/student-data', [PersonnelBulletinController::class, 'studentData'])->name('student-data');
            Route::post('/', [PersonnelBulletinController::class, 'store'])->name('store');
            Route::get('/{bulletin}', [PersonnelBulletinController::class, 'show'])->name('show');
            Route::get('/{bulletin}/edit', [PersonnelBulletinController::class, 'edit'])->name('edit');
            Route::match(['put', 'patch'], '/{bulletin}', [PersonnelBulletinController::class, 'update'])->name('update');
            Route::delete('/{bulletin}', [PersonnelBulletinController::class, 'destroy'])->name('destroy');
            Route::get('/{bulletin}/print', [PersonnelBulletinController::class, 'print'])->name('print');
            Route::get('/{bulletin}/download-pdf', [PersonnelBulletinController::class, 'downloadPdf'])->name('download-pdf');
            Route::get('/{bulletin}/download', [PersonnelBulletinController::class, 'download'])->name('download');
        });

        // Paramètres
        Route::get('/parametres', [PersonnelParametreController::class, 'index'])->name('parametres.index');
        Route::put('/parametres', [PersonnelParametreController::class, 'update'])->name('parametres.update');
        Route::put('/parametres/password', [PersonnelParametreController::class, 'updatePassword'])->name('parametres.password');
        Route::put('/parametres/photo', [PersonnelParametreController::class, 'updatePhoto'])->name('parametres.photo');
    });

