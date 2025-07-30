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
use App\Http\Controllers\EtudiantMatiereDroppedController;
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

// Route de test pour les notifications
Route::get('/test-notifications', function () {
    return view('test-notifications');
})->name('test-notifications');

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
        Route::get('/etudiants/{etudiant}/attribuer-parents', [EtudiantController::class, 'attribuerParents'])->name('etudiants.attribuer-parents');
        Route::post('/etudiants/{etudiant}/store-parents', [EtudiantController::class, 'storeParents'])->name('etudiants.store-parents');

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
        Route::resource('sessions-de-cours', SessionDeCoursController::class)->parameters(['sessions-de-cours' => 'sessionDeCour']);
        Route::get('/sessions-de-cours/historique', [SessionDeCoursController::class, 'historique'])->name('sessions-de-cours.historique');
        Route::get('/sessions-de-cours/{session}/appel', [SessionDeCoursController::class, 'appel'])->name('sessions-de-cours.appel');
        Route::get('/api/sessions-de-cours/{session}', [SessionDeCoursController::class, 'getSessionJson'])->name('api.sessions-de-cours.show');
        Route::resource('emplois-du-temps', EmploiDuTempsController::class);
        Route::get('/justifications', [JustificationAbsenceController::class, 'index'])->name('justifications.index');
        Route::get('/justifications/create/{presenceId}', [JustificationAbsenceController::class, 'create'])->name('justifications.create');
        Route::post('/justifications/store/{presenceId}', [JustificationAbsenceController::class, 'store'])->name('justifications.store');
        Route::get('/justifications/{justificationId}', [JustificationAbsenceController::class, 'show'])->name('justifications.show');
        Route::get('/justifications/{justificationId}/edit', [JustificationAbsenceController::class, 'edit'])->name('justifications.edit');
        Route::put('/justifications/{justificationId}', [JustificationAbsenceController::class, 'update'])->name('justifications.update');
        Route::delete('/justifications/{justificationId}', [JustificationAbsenceController::class, 'destroy'])->name('justifications.destroy');
        Route::get('/statistiques', [StatistiquesController::class, 'index'])->name('statistiques.index');
        Route::get('/graphiques', [StatistiquesController::class, 'graphiques'])->name('graphiques');

        // Routes pour la gestion des présences
        Route::get('/presences', [PresenceController::class, 'index'])->name('presences.index');
        Route::post('/presences/store', [PresenceController::class, 'store'])->name('presences.store');
        Route::post('/presences/workshop-elearning', [PresenceController::class, 'storeWorkshopElearning'])->name('presences.workshop-elearning');
        Route::put('/presences/{presence}', [PresenceController::class, 'update'])->name('presences.update');

        // Nouvelles routes pour le coordinateur
        Route::get('/coordinateur/emplois-du-temps', [CoordinateurController::class, 'emploisDuTemps'])->name('coordinateur.emplois-du-temps');
        Route::post('/coordinateur/creer-session', [CoordinateurController::class, 'creerSession'])->name('coordinateur.creer-session');
        Route::put('/coordinateur/session/{session}', [CoordinateurController::class, 'modifierSession'])->name('coordinateur.modifier-session');
        Route::post('/coordinateur/session/{session}/presence', [CoordinateurController::class, 'prisePresence'])->name('coordinateur.prise-presence');
        Route::get('/coordinateur/session/{session}/etudiants', [CoordinateurController::class, 'getEtudiantsClasse'])->name('coordinateur.get-etudiants');
        Route::get('/coordinateur/session/{session}/presences', [CoordinateurController::class, 'getPresencesSession'])->name('coordinateur.get-presences');
        Route::get('/coordinateur/sessions-presentiel', [CoordinateurController::class, 'getSessionsPresentiel'])->name('coordinateur.sessions-presentiel');

        // Routes pour gérer les étudiants qui ont abandonné une matière
        Route::resource('etudiant-matiere-dropped', EtudiantMatiereDroppedController::class);
        Route::get('/etudiant-matiere-dropped-statistics', [EtudiantMatiereDroppedController::class, 'statistics'])->name('etudiant-matiere-dropped.statistics');
        Route::post('/etudiant-matiere-dropped-filter', [EtudiantMatiereDroppedController::class, 'filter'])->name('etudiant-matiere-dropped.filter');
    });

    // Routes pour l'enseignant
    Route::middleware(['role:enseignant'])->group(function () {
        Route::get('/emplois-du-temps/enseignant', [EmploiDuTempsController::class, 'enseignant'])->name('emplois-du-temps.enseignant');
        Route::post('/presences/presentiel', [PresenceController::class, 'storePresentiel'])->name('presences.presentiel');

        // Routes pour les sessions de cours (enseignants) - avec des noms différents pour éviter les conflits
        Route::get('/enseignant/sessions-de-cours', [SessionDeCoursController::class, 'index'])->name('enseignant.sessions-de-cours.index');
        Route::get('/enseignant/sessions-de-cours/{sessionId}', [SessionDeCoursController::class, 'show'])->name('enseignant.sessions-de-cours.show');
        Route::get('/enseignant/sessions-de-cours/{sessionId}/appel', [SessionDeCoursController::class, 'appel'])->name('enseignant.sessions-de-cours.appel');
        Route::post('/enseignant/sessions-de-cours/{sessionId}/presences', [SessionDeCoursController::class, 'enregistrerPresences'])->name('enseignant.sessions-de-cours.enregistrer-presences');
        Route::get('/enseignant/sessions-de-cours/{sessionDeCour}/edit', [SessionDeCoursController::class, 'edit'])->name('enseignant.sessions-de-cours.edit');
        Route::put('/enseignant/sessions-de-cours/{sessionDeCour}', [SessionDeCoursController::class, 'update'])->name('enseignant.sessions-de-cours.update');
        Route::delete('/enseignant/sessions-de-cours/{sessionDeCour}', [SessionDeCoursController::class, 'destroy'])->name('enseignant.sessions-de-cours.destroy');

        // Routes pour la modification des présences
        Route::get('/enseignant/presences', [PresenceController::class, 'index'])->name('enseignant.presences.index');
        Route::put('/enseignant/presences/{presence}', [PresenceController::class, 'update'])->name('enseignant.presences.update');

        // Nouvelles routes pour la prise de présence des enseignants
        Route::post('/enseignant/session/{session}/presence', [EnseignantController::class, 'prisePresence'])->name('enseignant.prise-presence');
        Route::get('/enseignant/session/{session}/etudiants', [EnseignantController::class, 'getEtudiantsClasse'])->name('enseignant.get-etudiants');
        Route::get('/enseignant/sessions-presentiel', [EnseignantController::class, 'getSessionsPresentiel'])->name('enseignant.sessions-presentiel');
    });

    // Routes pour l'étudiant
    Route::middleware(['role:etudiant'])->group(function () {
        Route::get('/dashboard/etudiant', [DashboardController::class, 'etudiantDashboard'])->name('dashboard.etudiant');
        Route::get('/emplois-du-temps/etudiant', [EmploiDuTempsController::class, 'etudiant'])->name('emplois-du-temps.etudiant');
        Route::get('/presences/etudiant', [PresenceController::class, 'etudiant'])->name('presences.etudiant');
        Route::get('/cours/etudiant', [SessionDeCoursController::class, 'etudiant'])->name('cours.etudiant');
    });

        // Route temporaire pour tester sans middleware
    Route::get('/test-parents', function() {
        return 'Route de test fonctionne !';
    });

    // Route pour vérifier le rôle de l'utilisateur
    Route::get('/check-role', function() {
        $user = \Illuminate\Support\Facades\Auth::user();
        $role = $user->roles->first();
        return response()->json([
            'user_id' => $user->id,
            'user_email' => $user->email,
            'role_code' => $role ? $role->code : 'aucun',
            'role_name' => $role ? $role->nom : 'aucun'
        ]);
    })->name('check-role');

    // Routes communes pour tous les rôles (sans middleware de rôle spécifique)
    Route::get('/emplois-du-temps', [EmploiDuTempsController::class, 'index'])->name('emplois-du-temps.index');
    Route::get('/presences', [PresenceController::class, 'index'])->name('presences.index');

    // Routes pour le parent
    Route::middleware(['role:parent'])->group(function () {
        Route::get('/parents/mes-enfants', [ParentEtudiantController::class, 'mesEnfants'])->name('parents.mes-enfants');
        Route::get('/emplois-du-temps/parent', [EmploiDuTempsController::class, 'parent'])->name('emplois-du-temps.parent');
        Route::get('/presences/parent', [PresenceController::class, 'parent'])->name('presences.parent');
        Route::get('/presences/enfants', [PresenceController::class, 'presencesEnfants'])->name('presences.enfants');
        Route::get('/emplois-du-temps/enfants', [EmploiDuTempsController::class, 'emploisDuTempsEnfants'])->name('emplois-du-temps.enfants');
    });

    // Routes pour les notifications (accessibles à tous les utilisateurs authentifiés)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/marquer-lue/{notification}', [NotificationController::class, 'marquerLue'])->name('notifications.marquer-lue');
    Route::post('/notifications/marquer-toutes-lues', [NotificationController::class, 'marquerToutesLues'])->name('notifications.marquer-toutes-lues');
});



require __DIR__.'/auth.php';
