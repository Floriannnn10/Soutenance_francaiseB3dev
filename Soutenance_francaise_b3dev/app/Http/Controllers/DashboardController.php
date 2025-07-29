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
        return view('dashboard.enseignant');
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
        return view('dashboard.parent');
    }
}
