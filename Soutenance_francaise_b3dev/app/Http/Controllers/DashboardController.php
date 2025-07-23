<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                return view('dashboard.utilisateurs');
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
