<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AnneeAcademique;
use App\Models\Semestre;
use App\Models\Coordinateur;
use Illuminate\Support\Facades\Auth;

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
                return $this->etudiantDashboard();
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

        // Récupérer les sessions en présentiel pour l'emploi du temps
        $sessionsPresentiel = \App\Models\SessionDeCours::with(['classe', 'matiere', 'typeCours'])
            ->where('enseignant_id', $enseignant->id)
            ->whereHas('typeCours', function($q) {
                $q->where('nom', 'Présentiel');
            })
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
                $dateJour = now()->startOfWeek()->next($jourAnglais);
                $heureDebut = $dateJour->copy()->setTimeFromTimeString($heure);
                $heureFin = $heureDebut->copy()->addHours(2);

                // Chercher une session pour ce créneau (dans les 4 prochaines semaines)
                $session = $sessionsPresentiel->where('start_time', '>=', $heureDebut)
                    ->where('start_time', '<', $heureFin)
                    ->where('start_time', '<=', now()->addWeeks(4))
                    ->first();

                if ($session) {
                    $emploiDuTemps[$horaire][$jourFrancais] = [
                        'matiere' => $session->matiere->nom,
                        'classe' => $session->classe->nom,
                        'type' => 'presentiel',
                        'date' => $session->start_time->format('d/m/Y')
                    ];
                }
            }
        }

        return view('dashboard.enseignant', compact('sessions', 'emploiDuTemps'));
    }

    private function etudiantDashboard()
    {
        $user = Auth::user();
        $etudiant = $user->etudiant;

        if (!$etudiant) {
            return redirect()->route('login')->with('error', 'Profil étudiant non trouvé.');
        }

        // Calculer le taux de présence
        $totalPresences = $etudiant->presences()->count();
        $presencesPresent = $etudiant->presences()->whereHas('statutPresence', function($q) {
            $q->where('nom', 'Présent');
        })->count();

        $tauxPresence = $totalPresences > 0 ? round(($presencesPresent / $totalPresences) * 100, 1) : 0;

        // Récupérer les absences (présences avec statut "Absent")
        $absences = $etudiant->presences()->whereHas('statutPresence', function($q) {
            $q->where('nom', 'Absent');
        })->with(['sessionDeCours.matiere', 'sessionDeCours.classe', 'justification'])->get();

        // Calculer l'évolution de la présence (pour le graphique)
        $evolutionPresence = [];
        $anneesAcademiques = \App\Models\AnneeAcademique::orderBy('date_debut', 'desc')->take(3)->get();

        foreach ($anneesAcademiques as $annee) {
            $presencesAnnee = $etudiant->presences()->whereHas('sessionDeCours', function($q) use ($annee) {
                $q->where('annee_academique_id', $annee->id);
            });

            $totalAnnee = $presencesAnnee->count();
            $presentsAnnee = $presencesAnnee->whereHas('statutPresence', function($q) {
                $q->where('nom', 'Présent');
            })->count();

            $tauxAnnee = $totalAnnee > 0 ? round(($presentsAnnee / $totalAnnee) * 100, 1) : 0;

            $evolutionPresence[] = [
                'annee' => $annee->nom,
                'taux' => $tauxAnnee
            ];
        }

        return view('dashboard.etudiant', compact(
            'tauxPresence',
            'absences',
            'evolutionPresence'
        ));
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
