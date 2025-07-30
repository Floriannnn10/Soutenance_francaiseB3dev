<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AnneeAcademique;
use App\Models\Semestre;
use App\Models\Coordinateur;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\View\View;
use App\Models\SessionDeCours;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->roles->first();

        if (!$role) {
            return redirect()->route('login')->with('error', 'Aucun rôle attribué.');
        }

        switch ($role->code) {
            case 'admin':
                return $this->adminDashboard();
            case 'coordinateur':
                return $this->coordinateurDashboard();
            case 'enseignant':
                return $this->enseignantDashboard();
            case 'etudiant':
                return redirect()->route('dashboard.etudiant');
            case 'parent':
                return $this->parentDashboard();
            default:
                return redirect()->route('login')->with('error', 'Rôle non reconnu.');
        }
    }

    private function adminDashboard()
    {
        $totalUsers = User::count();
        $totalAnnees = AnneeAcademique::count();
        $totalSemestres = Semestre::count();
        $totalCoordinateurs = Coordinateur::count();
        $totalParents = \App\Models\ParentEtudiant::count();
        $recentLogins = User::whereNotNull('last_login_at')
                           ->orderBy('last_login_at', 'desc')
                           ->take(5)
                           ->get();

        return view('dashboard.admin', compact(
            'totalUsers',
            'totalAnnees',
            'totalSemestres',
            'totalCoordinateurs',
            'totalParents',
            'recentLogins'
        ));
    }

    private function coordinateurDashboard()
    {
        $user = Auth::user();
        $coordinateur = $user->coordinateur;
        $promotion = $coordinateur?->promotion;

        if (!$promotion) {
            return view('dashboard.coordinateur', [
                'coordinateur' => $coordinateur,
                'promotion' => null,
                'classes' => collect(),
                'stats' => [
                    'total_etudiants' => 0,
                    'total_classes' => 0,
                    'total_sessions' => 0,
                    'justifications_en_attente' => 0,
                    'taux_presence' => 0
                ],
                'anneesAcademiques' => AnneeAcademique::orderBy('date_debut', 'desc')->get(),
                'anneeActive' => null
            ]);
        }

        // Rediriger vers le contrôleur du coordinateur pour une gestion complète
        $coordinateurController = new \App\Http\Controllers\CoordinateurController();
        return $coordinateurController->dashboard(request());
    }

    private function enseignantDashboard()
    {
        $user = Auth::user();
        $enseignant = $user->enseignant;

        if (!$enseignant) {
            return redirect()->route('login')->with('error', 'Profil enseignant non trouvé.');
        }

        // Récupérer les sessions de l'enseignant
        $sessions = \App\Models\SessionDeCours::with(['classe', 'matiere', 'typeCours', 'statutSession'])
            ->where('enseignant_id', $enseignant->id)
            ->orderBy('start_time', 'desc')
            ->take(10)
            ->get();

        // Récupérer les sessions en présentiel pour l'emploi du temps (semaine actuelle + 2 semaines)
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfPeriod = $now->copy()->addWeeks(2)->endOfWeek();

        $sessionsPresentiel = \App\Models\SessionDeCours::with(['classe', 'matiere', 'typeCours'])
            ->where('enseignant_id', $enseignant->id)
            ->whereHas('typeCours', function($q) {
                $q->where('nom', 'Présentiel');
            })
            ->where('start_time', '>=', $startOfWeek)
            ->where('start_time', '<=', $endOfPeriod)
            ->orderBy('start_time')
            ->get();

        // Créer l'emploi du temps simplifié
        $emploiDuTemps = [];
        $creneaux = [
            '08:00-10:00' => '8:00',
            '10:00-12:00' => '10:00',
            '14:00-16:00' => '14:00',
            '16:00-18:00' => '16:00'
        ];

        // Vérifier s'il y a des sessions pour cette période
        $hasSessions = $sessionsPresentiel->count() > 0;

        if ($hasSessions) {
            foreach ($creneaux as $horaire => $heure) {
                $emploiDuTemps[$horaire] = [
                    'horaire' => $horaire,
                    'lundi' => null,
                    'mardi' => null,
                    'mercredi' => null,
                    'jeudi' => null,
                    'vendredi' => null,
                    'samedi' => null
                ];

                // Utiliser les noms de jours en anglais pour Carbon
                $joursAnglais = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                $joursFrancais = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];

                foreach ($joursAnglais as $index => $jourAnglais) {
                    $jourFrancais = $joursFrancais[$index];

                    // Calculer la date du prochain jour de la semaine
                    $dateJour = $now->copy()->startOfWeek()->next($jourAnglais);
                    $heureDebut = $dateJour->copy()->setTimeFromTimeString($heure);
                    $heureFin = $heureDebut->copy()->addHours(2);

                    // Chercher une session pour ce créneau (plus flexible)
                    $session = $sessionsPresentiel->where('start_time', '>=', $heureDebut->copy()->subMinutes(30))
                        ->where('start_time', '<', $heureFin->copy()->addMinutes(30))
                        ->first();

                    if ($session) {
                        $emploiDuTemps[$horaire][$jourFrancais] = [
                            'matiere' => $session->matiere->nom,
                            'classe' => $session->classe->nom,
                            'type' => 'presentiel',
                            'date' => $session->start_time->format('d/m/Y'),
                            'heure_debut' => $session->start_time->format('H:i'),
                            'heure_fin' => $session->end_time->format('H:i'),
                            'salle' => $session->lieu ?: 'Non spécifiée',
                            'session_id' => $session->id
                        ];
                    }
                }
            }
        }

        return view('dashboard.enseignant', compact('sessions', 'emploiDuTemps', 'hasSessions'));
    }

    /**
     * Dashboard pour les étudiants
     */
    public function etudiantDashboard(): View
    {
        $user = Auth::user();
        $etudiant = $user->etudiant;

        if (!$etudiant) {
            abort(404, 'Profil étudiant non trouvé');
        }

        // Récupérer les sessions de cours de l'étudiant pour cette semaine
        $sessions = SessionDeCours::with(['matiere', 'enseignant', 'classe'])
            ->where('classe_id', $etudiant->classe_id)
            ->where('annee_academique_id', $etudiant->classe->promotion->annee_academique_id)
            ->whereBetween('start_time', [
                Carbon::now()->startOfWeek()->copy(),
                Carbon::now()->endOfWeek()->copy()
            ])
            ->orderBy('start_time')
            ->orderBy('start_time')
            ->get();

        // Récupérer les matières droppées de l'étudiant
        $matieresDropped = $etudiant->matieresDropped()
            ->with(['matiere', 'anneeAcademique', 'semestre'])
            ->where('date_drop', '>=', Carbon::now()->subDays(30)) // Seulement les abandons récents
            ->get();

        return view('dashboard.etudiant', compact('etudiant', 'sessions', 'matieresDropped'));
    }

    private function parentDashboard()
    {
        $user = Auth::user();
        $parent = $user->parent;

        if (!$parent) {
            return redirect()->route('login')->with('error', 'Profil parent non trouvé.');
        }

        // Charger les étudiants avec leurs relations
        $parent->load(['etudiants.presences.statutPresence', 'etudiants.presences.sessionDeCours.matiere', 'etudiants.presences.sessionDeCours.typeCours', 'etudiants.classe']);

        return view('dashboard.parent', compact('parent'));
    }
}
