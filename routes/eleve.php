<?php

use App\Http\Controllers\Eleve\EleveAuthController;
use App\Http\Controllers\Eleve\ElevePasswordController;
use App\Http\Controllers\Eleve\EleveDashboardController;
use App\Http\Controllers\Eleve\EleveScolariteController;
use App\Http\Controllers\Eleve\EleveNotesController;
use App\Http\Controllers\Eleve\EleveBulletinsController;
use App\Http\Controllers\Eleve\EleveClasseController;
use App\Http\Controllers\Eleve\EleveEmploiTempsController;
use App\Http\Controllers\Eleve\EleveProfilController;
use App\Http\Controllers\Eleve\EleveNotificationController;
use App\Http\Controllers\Eleve\EleveMessageController;
use App\Http\Controllers\Eleve\EleveParametreController;
use Illuminate\Support\Facades\Route;

// Routes publiques d'authentification élève (par matricule)
Route::middleware('guest')->prefix('eleve')->name('eleve.')
    ->group(function () {
        Route::get('/login', [EleveAuthController::class, 'create'])->name('login');
        Route::post('/login', [EleveAuthController::class, 'store'])->name('login.store');
    });

Route::middleware(['auth', 'status', 'role:eleve', 'must.change.password', 'subscription.active'])
    ->prefix('eleve')
    ->name('eleve.')
    ->group(function () {

        // Déconnexion (POST requis par le layout)
        Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

        // Changement obligatoire de mot de passe (première connexion)
        Route::get('/password/change', [ElevePasswordController::class, 'showChangeForm'])->name('password.change');
        Route::post('/password/change', [ElevePasswordController::class, 'update'])->name('password.update');

        // Dashboard
        Route::get('/dashboard', [EleveDashboardController::class, 'index'])->name('dashboard');

        // Ma scolarité
        Route::get('/scolarite', [EleveScolariteController::class, 'index'])->name('scolarite');

        // Mes notes
        Route::get('/notes', [EleveNotesController::class, 'index'])->name('notes');

        // Mes bulletins
        Route::get('/bulletins', [EleveBulletinsController::class, 'index'])->name('bulletins');
        Route::get('/bulletins/{bulletin}', [EleveBulletinsController::class, 'show'])->name('bulletins.show');
        Route::get('/bulletins/{bulletin}/print', [EleveBulletinsController::class, 'print'])->name('bulletins.print');
        Route::get('/bulletins/{bulletin}/download-pdf', [EleveBulletinsController::class, 'downloadPdf'])->name('bulletins.download-pdf');

        // Ma classe
        Route::get('/classe', [EleveClasseController::class, 'index'])->name('classe');

// Mon emploi du temps
        Route::get('/emploi-temps', [EleveEmploiTempsController::class, 'index'])->name('emploi-temps');
        Route::get('/emploi-temps/print', [EleveEmploiTempsController::class, 'print'])->name('emploi-temps.print');
        Route::get('/emploi-temps/pdf', [EleveEmploiTempsController::class, 'pdf'])->name('emploi-temps.pdf');

        // Mon profil
        Route::get('/profil', [EleveProfilController::class, 'index'])->name('profil');
        Route::put('/profil/password', [EleveProfilController::class, 'updatePassword'])->name('profil.password');

        // Mes notifications
        Route::get('/notifications', [EleveNotificationController::class, 'index'])->name('notifications');
        Route::post('/notifications/{notificationRecipient}/read', [EleveNotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [EleveNotificationController::class, 'markAllRead'])->name('notifications.read-all');
        Route::delete('/notifications/{notificationRecipient}', [EleveNotificationController::class, 'destroy'])->name('notifications.destroy');

        // Mes messages
        Route::get('/messages', [EleveMessageController::class, 'index'])->name('messages');
        Route::get('/messages/compose', [EleveMessageController::class, 'create'])->name('messages.create');
        Route::post('/messages', [EleveMessageController::class, 'store'])->name('messages.store');
        Route::post('/messages/{message}/read', [EleveMessageController::class, 'markRead'])->name('messages.read');

        // Mes paramètres
        Route::get('/parametres', [EleveParametreController::class, 'index'])->name('parametres');
        Route::put('/parametres/password', [EleveParametreController::class, 'update'])->name('parametres.update');
    });
