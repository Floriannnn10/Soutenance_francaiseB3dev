<?php

namespace App\Http\Controllers;

use App\Models\Enseignant;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Matiere;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class EnseignantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $enseignants = Enseignant::with(['matieres'])
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        return view('enseignants.index', compact('enseignants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $matieres = Matiere::orderBy('nom')->get();
        $users = User::whereHas('roles', function($q) {
            $q->where('nom', 'Enseignant');
        })->orderBy('name')->get();
        return view('enseignants.create', compact('matieres', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'matieres' => 'array',
            'matieres.*' => 'exists:matieres,id',
        ]);

        // Créer l'utilisateur
        $user = \App\Models\User::create([
            'name' => $request->prenom . ' ' . $request->nom,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Attacher le rôle enseignant
        $roleEnseignant = \App\Models\Role::where('nom', 'Enseignant')->first();
        if ($roleEnseignant) {
            $user->roles()->attach($roleEnseignant->id);
        }

        $enseignant = Enseignant::create([
            'user_id' => $user->id,
            'nom' => $request->nom,
            'prenom' => $request->prenom,
        ]);

        // Gérer l'upload de la photo
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('enseignants', 'public');
            $enseignant->update(['photo' => $photoPath]);
        }

        // Affecter les matières sélectionnées à cet enseignant
        if ($request->filled('matieres')) {
            $enseignant->matieres()->attach($request->matieres);
        }

        return redirect()->route('enseignants.index')
            ->with('success', 'Enseignant créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Enseignant $enseignant): View
    {
        $enseignant->load(['sessionsDeCours.matiere', 'sessionsDeCours.classe', 'sessionsDeCours.semestre']);
        return view('enseignants.show', compact('enseignant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Enseignant $enseignant): View
    {
        $matieres = Matiere::orderBy('nom')->get();
        return view('enseignants.edit', compact('enseignant', 'matieres'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Enseignant $enseignant): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $enseignant->user_id,
            'password' => 'nullable|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'matieres' => 'array',
            'matieres.*' => 'exists:matieres,id',
        ]);

        // Mettre à jour l'utilisateur
        $user = $enseignant->user;
        $user->update([
            'name' => $request->prenom . ' ' . $request->nom,
            'email' => $request->email,
        ]);

        // Mettre à jour le mot de passe si fourni
        if ($request->filled('password')) {
            $user->update(['password' => bcrypt($request->password)]);
        }

        $enseignant->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
        ]);

        // Gérer l'upload de la photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($enseignant->photo) {
                Storage::disk('public')->delete($enseignant->photo);
            }
            $photoPath = $request->file('photo')->store('enseignants', 'public');
            $enseignant->update(['photo' => $photoPath]);
        }

        // Synchroniser les matières avec cet enseignant
        $enseignant->matieres()->sync($request->matieres ?? []);

        return redirect()->route('enseignants.index')
            ->with('success', 'Enseignant mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enseignant $enseignant): RedirectResponse
    {
        if ($enseignant->sessionsDeCours()->count() > 0) {
            return redirect()->route('enseignants.index')
                ->with('error', 'Impossible de supprimer cet enseignant car il a des sessions de cours programmées.');
        }

        $enseignant->delete();

        return redirect()->route('enseignants.index')
            ->with('success', 'Enseignant supprimé avec succès.');
    }
}
