<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Sadmin\AbonnementController;
use App\Http\Controllers\Sadmin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth', 'verified', 'role:SADMIN'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/sadmin/dashboard', [DashboardController::class, 'index'])->name('sadmin.dashboard');

    Route::get('/sadmin/etablissement', [\App\Http\Controllers\Sadmin\EtablissementController::class, 'index'])
        ->name('sadmin.etablissement');

    Route::get('/sadmin/abonnement', [AbonnementController::class, 'index'])
        ->name('sadmin.abonnement');

    Route::get('/sadmin/client', function () {
        return view('sadmin.client');
    })->name('sadmin.client');

    Route::get('/sadmin/notifications', function () {
        return view('sadmin.notifications');
    })->name('sadmin.notifications');

    Route::get('/sadmin/parametres', function () {
        return view('sadmin.parametres');
    })->name('sadmin.parametres');

    Route::get('/sadmin/compte', function () {
        return view('sadmin.compte');
    })->name('sadmin.compte');

    Route::get('/sadmin/notifications/historique', function () {
        return view('sadmin.historique');
    })->name('sadmin.notifications.historique');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
