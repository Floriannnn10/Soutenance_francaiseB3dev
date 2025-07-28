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
        return view('dashboard.etudiant');
    }

    private function parentDashboard()
    {
        return view('dashboard.parent');
    }
}
