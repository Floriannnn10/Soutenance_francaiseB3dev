<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnneeAcademiqueController;
use App\Http\Controllers\SemestreController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\SessionDeCoursController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ParentEtudiantController;
use App\Http\Controllers\CoordinateurController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes Resource pour la gestion des données
    Route::resources([
        'annees-academiques' => AnneeAcademiqueController::class,
        'semestres' => SemestreController::class,
        'classes' => ClasseController::class,
        'matieres' => MatiereController::class,
        'etudiants' => EtudiantController::class,
        'enseignants' => EnseignantController::class,
        'sessions-de-cours' => SessionDeCoursController::class,
        'presences' => PresenceController::class,
        'notifications' => NotificationController::class,
        'parents' => ParentEtudiantController::class,
        'coordinateurs' => CoordinateurController::class,
    ]);

    Route::resource('users', App\Http\Controllers\UserController::class);

    // Routes supplémentaires pour des actions spécifiques
    Route::patch('/annees-academiques/{anneeAcademique}/activate', [AnneeAcademiqueController::class, 'activate'])
        ->name('annees-academiques.activate');

    // Routes pour les sessions de cours
    Route::get('/sessions-de-cours/today', [SessionDeCoursController::class, 'today'])
        ->name('sessions-de-cours.today');
    Route::post('/sessions-de-cours/{sessionDeCour}/report', [SessionDeCoursController::class, 'report'])
        ->name('sessions-de-cours.report');

    // Routes pour les présences
    Route::get('/sessions-de-cours/{sessionDeCour}/appel', [PresenceController::class, 'appel'])
        ->name('presences.appel');
    Route::post('/sessions-de-cours/{sessionDeCour}/appel', [PresenceController::class, 'storeAppel'])
        ->name('presences.store-appel');

    // Routes pour les notifications
    Route::patch('/notifications/{notification}/marquer-lue', [NotificationController::class, 'marquerLue'])
        ->name('notifications.marquer-lue');
    Route::patch('/notifications/{notification}/envoyer', [NotificationController::class, 'envoyer'])
        ->name('notifications.envoyer');

    // Routes pour les parents
    Route::patch('/parents/{parent}/toggle-status', [ParentEtudiantController::class, 'toggleStatus'])
        ->name('parents.toggle-status');

    // Routes pour les coordinateurs
    Route::patch('/coordinateurs/{coordinateur}/toggle-status', [CoordinateurController::class, 'toggleStatus'])
        ->name('coordinateurs.toggle-status');

    // Route pour les graphiques
    Route::get('/graphiques', function () {
        return view('graphiques');
    })->name('graphiques');
});

require __DIR__.'/auth.php';
