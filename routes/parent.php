<?php

use App\Http\Controllers\Parent\ParentDashboardController;
use App\Http\Controllers\Parent\ParentEnfantController;
use App\Http\Controllers\Parent\ParentEnfantScolariteController;
use App\Http\Controllers\Parent\ParentEnfantNotesController;
use App\Http\Controllers\Parent\ParentEnfantBulletinsController;
use App\Http\Controllers\Parent\ParentEnfantEmploiTempsController;
use App\Http\Controllers\Parent\ParentNotificationController;
use App\Http\Controllers\Parent\ParentMessageController;
use App\Http\Controllers\Parent\ParentProfilController;
use App\Http\Controllers\Parent\ParentPasswordController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'status', 'role:parent', 'must.change.password', 'subscription.active'])
    ->prefix('parent')
    ->name('parent.')
    ->group(function () {

        // Déconnexion (POST requis par le layout)
        Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

        // Changement obligatoire de mot de passe (première connexion)
        Route::get('/password/change', [ParentPasswordController::class, 'showChangeForm'])->name('password.change');
        Route::post('/password/change', [ParentPasswordController::class, 'update'])->name('password.change.update');

        // Dashboard
        Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');

        // Mes enfants
        Route::get('/enfants', [ParentEnfantController::class, 'index'])->name('enfants');
        Route::get('/enfants/{eleve}', [ParentEnfantController::class, 'show'])->name('enfants.show');

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

        // Notifications
        Route::get('/notifications', [ParentNotificationController::class, 'index'])->name('notifications');
        Route::post('/notifications/{notificationRecipient}/read', [ParentNotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [ParentNotificationController::class, 'markAllRead'])->name('notifications.read-all');
        Route::delete('/notifications/{notificationRecipient}', [ParentNotificationController::class, 'destroy'])->name('notifications.destroy');

        // Messages
        Route::get('/messages', [ParentMessageController::class, 'index'])->name('messages');
        Route::get('/messages/compose', [ParentMessageController::class, 'create'])->name('messages.create');
        Route::post('/messages', [ParentMessageController::class, 'store'])->name('messages.store');
        Route::post('/messages/{message}/read', [ParentMessageController::class, 'markRead'])->name('messages.read');

        // Profil
        Route::get('/profil', [ParentProfilController::class, 'index'])->name('profil');
        Route::put('/profil', [ParentProfilController::class, 'update'])->name('profil.update');
        Route::put('/profil/password', [ParentProfilController::class, 'updatePassword'])->name('profil.password');
        Route::post('/profil/photo', [ParentProfilController::class, 'updatePhoto'])->name('profil.photo');
    });
