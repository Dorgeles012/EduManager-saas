<?php

use App\Http\Controllers\Parent\ParentDashboardController;
use App\Http\Controllers\Parent\ParentEnfantController;
use App\Http\Controllers\Parent\ParentEnfantScolariteController;
use App\Http\Controllers\Parent\ParentEnfantNotesController;
use App\Http\Controllers\Parent\ParentEnfantBulletinsController;
use App\Http\Controllers\Parent\ParentEnfantEmploiTempsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'status', 'role:parent'])
    ->prefix('parent')
    ->name('parent.')
    ->group(function () {

        // Déconnexion (POST requis par le layout)
        Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

        // Dashboard
        Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');

        // Mes enfants
        Route::get('/enfants', [ParentEnfantController::class, 'index'])->name('enfants');

        // Scolarité d'un enfant
        Route::get('/enfant/{eleve}/scolarite', [ParentEnfantScolariteController::class, 'show'])->name('enfant.scolarite');

        // Notes d'un enfant
        Route::get('/enfant/{eleve}/notes', [ParentEnfantNotesController::class, 'index'])->name('enfant.notes');

        // Bulletins d'un enfant
Route::get('/enfant/{eleve}/bulletins', [ParentEnfantBulletinsController::class, 'index'])->name('enfant.bulletins');
        Route::get('/enfant/{eleve}/bulletins/{bulletin}', [ParentEnfantBulletinsController::class, 'show'])->name('enfant.bulletins.show');
        Route::get('/enfant/{eleve}/bulletins/{bulletin}/print', [ParentEnfantBulletinsController::class, 'print'])->name('enfant.bulletins.print');
        Route::get('/enfant/{eleve}/bulletins/{bulletin}/download-pdf', [ParentEnfantBulletinsController::class, 'downloadPdf'])->name('enfant.bulletins.download-pdf');

        // Emploi du temps d'un enfant
        Route::get('/enfant/{eleve}/emploi-temps', [ParentEnfantEmploiTempsController::class, 'index'])->name('enfant.emploi-temps');
        Route::get('/enfant/{eleve}/emploi-temps/print', [ParentEnfantEmploiTempsController::class, 'print'])->name('enfant.emploi-temps.print');
        Route::get('/enfant/{eleve}/emploi-temps/pdf', [ParentEnfantEmploiTempsController::class, 'pdf'])->name('enfant.emploi-temps.pdf');
    });
