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
        $recentLogins = User::whereNotNull('last_login_at')
                           ->orderBy('last_login_at', 'desc')
                           ->take(5)
                           ->get();

        return view('dashboard.admin', compact(
            'totalUsers',
            'totalAnnees',
            'totalSemestres',
            'totalCoordinateurs',
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
                ]
            ]);
        }

        // Statistiques pour la promotion du coordinateur
        $classes = $promotion->classes;
        $total_etudiants = $classes->sum(function($classe) {
            return $classe->etudiants->count();
        });

        $total_sessions = \App\Models\SessionDeCours::whereHas('classe', function($query) use ($promotion) {
            $query->where('promotion_id', $promotion->id);
        })->count();

        $justifications_en_attente = \App\Models\JustificationAbsence::whereHas('presence.etudiant.classe', function($query) use ($promotion) {
            $query->where('promotion_id', $promotion->id);
        })->count();

        // Calcul du taux de présence (exemple simplifié)
        $total_presences = \App\Models\Presence::whereHas('sessionDeCours.classe', function($query) use ($promotion) {
            $query->where('promotion_id', $promotion->id);
        })->count();

        $presences_presentes = \App\Models\Presence::whereHas('sessionDeCours.classe', function($query) use ($promotion) {
            $query->where('promotion_id', $promotion->id);
        })->where('statut_presence_id', 1)->count(); // 1 = présent

        $taux_presence = $total_presences > 0 ? round(($presences_presentes / $total_presences) * 100, 1) : 0;

        $stats = [
            'total_etudiants' => $total_etudiants,
            'total_classes' => $classes->count(),
            'total_sessions' => $total_sessions,
            'justifications_en_attente' => $justifications_en_attente,
            'taux_presence' => $taux_presence
        ];

        return view('dashboard.coordinateur', compact(
            'coordinateur',
            'promotion',
            'classes',
            'stats'
        ));
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
