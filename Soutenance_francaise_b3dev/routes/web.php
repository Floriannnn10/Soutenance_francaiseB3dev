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
Route::get('/dashboard/coordinateur', [CoordinateurController::class, 'dashboard'])->middleware(['auth', 'verified', 'role:coordinateur'])->name('dashboard.coordinateur');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes pour l'Administrateur (accès complet)
    Route::middleware('role:admin')->group(function () {
        Route::resource('annees-academiques', AnneeAcademiqueController::class)->parameters([
            'annees-academiques' => 'anneeAcademique'
        ]);
        Route::resource('semestres', SemestreController::class);
        Route::resource('classes', ClasseController::class);
        Route::resource('matieres', MatiereController::class);
        Route::resource('etudiants', EtudiantController::class);
        Route::resource('enseignants', EnseignantController::class);
        Route::resource('coordinateurs', CoordinateurController::class);
        Route::resource('parents', ParentEtudiantController::class);
        Route::resource('users', App\Http\Controllers\UserController::class);

        // Routes supplémentaires pour des actions spécifiques
        Route::patch('/annees-academiques/{anneeAcademique}/activate', [AnneeAcademiqueController::class, 'activate'])
            ->name('annees-academiques.activate');
        Route::patch('/annees-academiques/{anneeAcademique}/deactivate', [AnneeAcademiqueController::class, 'deactivate'])
            ->name('annees-academiques.deactivate');

        // Routes pour les semestres
        Route::patch('/semestres/{semestre}/activate', [SemestreController::class, 'activate'])
            ->name('semestres.activate');
        Route::patch('/semestres/{semestre}/deactivate', [SemestreController::class, 'deactivate'])
            ->name('semestres.deactivate');

        // Routes pour les coordinateurs
        Route::patch('/coordinateurs/{coordinateur}/toggle-status', [CoordinateurController::class, 'toggleStatus'])
            ->name('coordinateurs.toggle-status');

        // Routes pour les parents
        Route::patch('/parents/{parent}/toggle-status', [ParentEtudiantController::class, 'toggleStatus'])
            ->name('parents.toggle-status');
    });

    // Routes pour le Coordinateur (gestion des emplois du temps, présences e-learning/workshops, justifications)
    Route::middleware('role:coordinateur')->group(function () {
        Route::resource('sessions-de-cours', SessionDeCoursController::class);
        Route::get('/sessions-de-cours/{session}/appel', [SessionDeCoursController::class, 'appel'])
            ->name('sessions-de-cours.appel');
        Route::post('/sessions-de-cours/{session}/presences', [SessionDeCoursController::class, 'enregistrerPresences'])
            ->name('sessions-de-cours.enregistrer-presences');
        Route::get('/presences', [PresenceController::class, 'index'])->name('presences.index');
        Route::get('/sessions-de-cours/{sessionDeCour}/appel', [PresenceController::class, 'appel'])
            ->name('presences.appel');
        Route::post('/sessions-de-cours/{sessionDeCour}/appel', [PresenceController::class, 'storeAppel'])
            ->name('presences.store-appel');
    });

    // Routes pour l'Enseignant (emploi du temps personnel, présences présentiel)
    Route::middleware('role:enseignant')->group(function () {
        Route::get('/sessions-de-cours', [SessionDeCoursController::class, 'index'])->name('sessions-de-cours.index');
        Route::get('/sessions-de-cours/{session}/appel', [SessionDeCoursController::class, 'appel'])
            ->name('sessions-de-cours.appel');
        Route::post('/sessions-de-cours/{session}/presences', [SessionDeCoursController::class, 'enregistrerPresences'])
            ->name('sessions-de-cours.enregistrer-presences');
        Route::get('/presences', [PresenceController::class, 'index'])->name('presences.index');
    });

    // Routes pour les Étudiants et Parents (consultation uniquement)
    Route::middleware('role:etudiant,parent')->group(function () {
        Route::get('/sessions-de-cours', [SessionDeCoursController::class, 'index'])->name('sessions-de-cours.index');
    });

    // Routes communes pour tous les utilisateurs authentifiés
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/marquer-lue', [NotificationController::class, 'marquerLue'])
        ->name('notifications.marquer-lue');
    Route::patch('/notifications/{notification}/envoyer', [NotificationController::class, 'envoyer'])
        ->name('notifications.envoyer');

    // Route pour les graphiques (uniquement pour les coordinateurs)
    Route::get('/graphiques', function () {
        return view('graphiques');
    })->middleware('role:coordinateur')->name('graphiques');
});

require __DIR__.'/auth.php';
