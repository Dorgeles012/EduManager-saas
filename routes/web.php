<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Sadmin\AbonnementController;
use App\Http\Controllers\Sadmin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});
Route::middleware(['auth', 'role:SADMIN'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/sadmin/dashboard', [DashboardController::class, 'index'])->name('sadmin.dashboard');

    // Garde la route historique utilisée par la sidebar : /sadmin/etablissement
    Route::get('/sadmin/etablissement', [\App\Http\Controllers\Sadmin\EtablissementController::class, 'index'])
        ->name('sadmin.etablissement');

    // (Optionnel) CRUD complet sur /sadmin/etablissements/... 
    Route::resource('etablissements', \App\Http\Controllers\Sadmin\EtablissementCrudController::class)
        ->names('sadmin.etablissements');

    Route::get('/sadmin/abonnement', [AbonnementController::class, 'index'])
        ->name('sadmin.abonnement');

    Route::resource('clients', \App\Http\Controllers\Sadmin\ClientController::class)
        ->names('sadmin.clients');

    Route::patch('sadmin/clients/{client}/block', [\App\Http\Controllers\Sadmin\ClientController::class, 'block'])
        ->name('sadmin.clients.block');

    Route::patch('sadmin/clients/{client}/unblock', [\App\Http\Controllers\Sadmin\ClientController::class, 'unblock'])
        ->name('sadmin.clients.unblock');



    Route::get('/sadmin/notifications', function () {
        return view('sadmin.notifications');
    })->name('sadmin.notifications');

    Route::get('/sadmin/parametres', function () {
        return view('sadmin.parametres');
    })->name('sadmin.parametres');

    Route::put('/sadmin/parametres/password', [\App\Http\Controllers\Sadmin\ComptePasswordController::class, 'update'])
        ->name('sadmin.parametres.password.update');

    Route::get('/sadmin/parametres/passwordchange', function () {
        return view('sadmin.passwordchange');
    })->name('sadmin.passwordchange');



    Route::get('/sadmin/compte', [\App\Http\Controllers\Sadmin\CompteController::class, 'index'])
        ->name('sadmin.compte');

    Route::put('/sadmin/compte', [\App\Http\Controllers\Sadmin\CompteController::class, 'update'])
        ->name('sadmin.compte.update');


    Route::get('/sadmin/notifications/historique', function () {
        return view('sadmin.historique');
    })->name('sadmin.notifications.historique');
});

Route::middleware(['auth', 'role:CLIENT'])->group(function () {
    Route::get('/client/dashboard', fn () => view('dashboards.role', [
        'title' => 'Dashboard Client',
    ]))->name('client.dashboard');
});

Route::middleware(['auth', 'role:PERSONNEL'])->group(function () {
    Route::get('/personnel/dashboard', fn () => view('dashboards.role', [
        'title' => 'Dashboard Personnel',
    ]))->name('personnel.dashboard');
});

Route::middleware(['auth', 'role:ENSEIGNANT'])->group(function () {
    Route::get('/enseignant/dashboard', fn () => view('dashboards.role', [
        'title' => 'Dashboard Enseignant',
    ]))->name('enseignant.dashboard');
});

Route::middleware(['auth', 'role:PARENT'])->group(function () {
    Route::get('/parent/dashboard', fn () => view('dashboards.role', [
        'title' => 'Dashboard Parent',
    ]))->name('parent.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
