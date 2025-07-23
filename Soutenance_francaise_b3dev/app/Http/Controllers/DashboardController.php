<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->role) {
            return view('dashboard');
        }

        $role = strtolower($user->role->nom);

        switch ($role) {
            case 'admin':
                $nbAnnees = \App\Models\AnneeAcademique::count();
                $nbSemestres = \App\Models\Semestre::count();
                $nbCoordinateurs = \App\Models\Coordinateur::count();
                $nbUtilisateurs = \App\Models\User::count();
                $recentUsers = \App\Models\User::with('role')
                    ->orderByDesc(DB::raw('COALESCE(last_login_at, updated_at)'))
                    ->take(5)
                    ->get();
                return view('dashboard.utilisateurs', compact('nbAnnees', 'nbSemestres', 'nbCoordinateurs', 'nbUtilisateurs', 'recentUsers', 'user'));
            case 'coordinateur':
                return redirect()->route('dashboard.coordinateur');
            case 'enseignant':
                return view('dashboard.enseignant');
            case 'etudiant':
                return view('dashboard.etudiant');
            case 'parent':
                return view('dashboard.parent');
            default:
                return view('dashboard');
        }
    }
}
