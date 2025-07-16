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
        $users = User::whereHas('role', function($q) {
            $q->where('nom', 'Enseignant');
        })->orderBy('nom')->get();
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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'matieres' => 'array',
            'matieres.*' => 'exists:matieres,id',
        ]);

        $enseignant = Enseignant::create([
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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'matieres' => 'array',
            'matieres.*' => 'exists:matieres,id',
        ]);

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
