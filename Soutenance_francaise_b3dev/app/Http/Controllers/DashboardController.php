<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Rediriger vers le dashboard approprié selon le rôle
        if ($user->role && $user->role->nom === 'admin') {
            return view('dashboard');
        } elseif ($user->etudiant) {
            return view('dashboard.etudiant');
        } elseif ($user->enseignant) {
            return view('dashboard.enseignant');
        } elseif ($user->parent) {
            return view('dashboard.parent');
        } elseif ($user->coordinateur) {
            return view('dashboard.coordinateur');
        }

        // Par défaut, retourner le dashboard admin
        return view('dashboard');
    }
}
