<?php

use App\Http\Controllers\Enseignant\EnseignantDashboardController;
use App\Http\Controllers\Enseignant\EnseignantAnneeAcademiqueController;
use App\Http\Controllers\Enseignant\EnseignantNoteController;
use App\Http\Controllers\Enseignant\EnseignantBulletinController;
use App\Http\Controllers\Enseignant\EnseignantEmploiTempsController;
use App\Http\Controllers\Enseignant\EnseignantParametreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'status', 'role:enseignant'])
    ->prefix('enseignant')
    ->name('enseignant.')
    ->group(function () {

        // Déconnexion (POST requis par le layout)
        Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

        // Dashboard
        Route::get('/dashboard', [EnseignantDashboardController::class, 'index'])->name('dashboard');

        // Année académique (lecture seule)
        Route::get('/annee-academique', [EnseignantAnneeAcademiqueController::class, 'index'])->name('annee-academique.index');

        // Notes
        Route::prefix('notes')->name('notes.')->group(function () {
            Route::get('/', [EnseignantNoteController::class, 'index'])->name('index');
            Route::get('/data', [EnseignantNoteController::class, 'data'])->name('data');
            Route::post('/', [EnseignantNoteController::class, 'store'])->name('store');
            Route::put('/{note}', [EnseignantNoteController::class, 'update'])->name('update');
            Route::delete('/{note}', [EnseignantNoteController::class, 'destroy'])->name('destroy');
        });

        // Bulletin
        Route::prefix('bulletin')->name('bulletin.')->group(function () {
            Route::get('/', [EnseignantBulletinController::class, 'index'])->name('index');
            Route::get('/{bulletin}', [EnseignantBulletinController::class, 'show'])->name('show');
            Route::get('/{bulletin}/print', [EnseignantBulletinController::class, 'print'])->name('print');
            Route::get('/{bulletin}/download-pdf', [EnseignantBulletinController::class, 'downloadPdf'])->name('download-pdf');
            Route::get('/{bulletin}/download', [EnseignantBulletinController::class, 'download'])->name('download');
        });

        // Emploi du temps (lecture seule)
        Route::get('/emploi-temps', [EnseignantEmploiTempsController::class, 'index'])->name('emploi-temps.index');
        Route::get('/emploi-temps/print', [EnseignantEmploiTempsController::class, 'print'])->name('emploi-temps.print');
        Route::get('/emploi-temps/pdf', [EnseignantEmploiTempsController::class, 'pdf'])->name('emploi-temps.pdf');

        // Paramètres
        Route::get('/parametres', [EnseignantParametreController::class, 'index'])->name('parametres.index');
        Route::put('/parametres', [EnseignantParametreController::class, 'update'])->name('parametres.update');
        Route::put('/parametres/password', [EnseignantParametreController::class, 'updatePassword'])->name('parametres.password');
        Route::put('/parametres/photo', [EnseignantParametreController::class, 'updatePhoto'])->name('parametres.photo');
    });

