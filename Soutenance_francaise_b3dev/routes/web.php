<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnneeAcademiqueController;
use App\Http\Controllers\SemestreController;
use App\Http\Controllers\CoordinateurController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\ParentEtudiantController;
use App\Http\Controllers\SessionDeCoursController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\StatistiquesController;
use App\Http\Controllers\EmploiDuTempsController;
use App\Http\Controllers\JustificationAbsenceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PromotionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes pour l'administrateur
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('annees-academiques', AnneeAcademiqueController::class)->parameters(['annees-academiques' => 'anneeAcademique']);
        Route::resource('semestres', SemestreController::class);
        Route::resource('coordinateurs', CoordinateurController::class);
        Route::resource('classes', ClasseController::class);
        Route::resource('matieres', MatiereController::class);
        Route::resource('enseignants', EnseignantController::class);
        Route::resource('etudiants', EtudiantController::class);
        Route::resource('parents', ParentEtudiantController::class);
        Route::resource('promotions', PromotionController::class);

        // Routes pour activer/désactiver les années académiques et semestres
        Route::patch('/annees-academiques/{anneeAcademique}/activate', [AnneeAcademiqueController::class, 'activate'])->name('annees-academiques.activate');
        Route::patch('/annees-academiques/{anneeAcademique}/deactivate', [AnneeAcademiqueController::class, 'deactivate'])->name('annees-academiques.deactivate');
        Route::patch('/semestres/{semestre}/activate', [SemestreController::class, 'activate'])->name('semestres.activate');
        Route::patch('/semestres/{semestre}/deactivate', [SemestreController::class, 'deactivate'])->name('semestres.deactivate');
    });

    // Routes pour le coordinateur
    Route::middleware(['role:coordinateur'])->group(function () {
        Route::resource('sessions-de-cours', SessionDeCoursController::class);
        Route::get('/sessions-de-cours/{session}/appel', [SessionDeCoursController::class, 'appel'])->name('sessions-de-cours.appel');
        Route::resource('emplois-du-temps', EmploiDuTempsController::class);
        Route::resource('justifications', JustificationAbsenceController::class);
        Route::get('/statistiques', [StatistiquesController::class, 'index'])->name('statistiques.index');
        Route::get('/graphiques', [StatistiquesController::class, 'graphiques'])->name('graphiques');

        // Routes pour la gestion des présences
        Route::get('/presences', [PresenceController::class, 'index'])->name('presences.index');
        Route::post('/presences/store', [PresenceController::class, 'store'])->name('presences.store');
        Route::post('/presences/workshop-elearning', [PresenceController::class, 'storeWorkshopElearning'])->name('presences.workshop-elearning');
    });

    // Routes pour l'enseignant
    Route::middleware(['role:enseignant'])->group(function () {
        Route::get('/emplois-du-temps/enseignant', [EmploiDuTempsController::class, 'enseignant'])->name('emplois-du-temps.enseignant');
        Route::post('/presences/presentiel', [PresenceController::class, 'storePresentiel'])->name('presences.presentiel');
    });

    // Routes pour l'étudiant
    Route::middleware(['role:etudiant'])->group(function () {
        Route::get('/emplois-du-temps/etudiant', [EmploiDuTempsController::class, 'etudiant'])->name('emplois-du-temps.etudiant');
        Route::get('/presences/etudiant', [PresenceController::class, 'etudiant'])->name('presences.etudiant');
    });

    // Routes pour le parent
    Route::middleware(['role:parent'])->group(function () {
        Route::get('/emplois-du-temps/parent', [EmploiDuTempsController::class, 'parent'])->name('emplois-du-temps.parent');
        Route::get('/presences/parent', [PresenceController::class, 'parent'])->name('presences.parent');
    });

    // Routes pour les notifications (accessibles à tous les utilisateurs authentifiés)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/marquer-lue/{notification}', [NotificationController::class, 'marquerLue'])->name('notifications.marquer-lue');
    Route::post('/notifications/marquer-toutes-lues', [NotificationController::class, 'marquerToutesLues'])->name('notifications.marquer-toutes-lues');
});

require __DIR__.'/auth.php';
