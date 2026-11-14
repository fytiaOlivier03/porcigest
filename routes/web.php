<?php

use App\Http\Controllers\ActiviteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampagneController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistributionAlimentController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\IntrantController;
use App\Http\Controllers\MouvementStockController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SuiviController;
use App\Http\Controllers\UniteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('unites', UniteController::class)->only(['index', 'store', 'destroy']);

    Route::resource('campagnes', CampagneController::class)->only(['index', 'store', 'show']);
    Route::post('campagnes/{campagne}/terminer', [CampagneController::class, 'terminer'])->name('campagnes.terminer');
    Route::post('campagnes/{campagne}/rapport', [CampagneController::class, 'genererRapport'])->name('campagnes.rapport');
    Route::get('campagnes/{campagne}/rapport', [CampagneController::class, 'telechargerRapport'])->name('campagnes.rapport.telecharger');
    Route::post('campagnes/{campagne}/pesees', [SuiviController::class, 'pesee'])->name('campagnes.pesees.store');
    Route::post('campagnes/{campagne}/mortalites', [SuiviController::class, 'mortalite'])->name('campagnes.mortalites.store');
    Route::post('campagnes/{campagne}/productions', [SuiviController::class, 'production'])->name('campagnes.productions.store');

    Route::resource('intrants', IntrantController::class)->only(['index', 'store', 'show']);
    Route::post('intrants/{intrant}/mouvements', [MouvementStockController::class, 'store'])->name('intrants.mouvements.store');

    Route::resource('distributions', DistributionAlimentController::class)->only(['index', 'store']);
    Route::resource('activites', ActiviteController::class)->only(['index', 'store']);

    Route::get('finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::post('finance/depenses', [FinanceController::class, 'storeDepense'])->name('finance.depenses.store');
    Route::post('finance/recettes', [FinanceController::class, 'storeRecette'])->name('finance.recettes.store');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/lire', [NotificationController::class, 'lireTout'])->name('notifications.lire');

    // Middleware de rôle ET Gate dans le contrôleur (double protection côté serveur)
    Route::middleware('role:admin')->group(function () {
        Route::get('utilisateurs', [UserController::class, 'index'])->name('users.index');
        Route::post('utilisateurs', [UserController::class, 'store'])->name('users.store');
        Route::post('utilisateurs/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
    });
});
