<?php

namespace App\Http\Controllers;

use App\Models\ParentEtudiant;
use App\Models\Etudiant;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ParentEtudiantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = ParentEtudiant::with(['etudiants']);

        if ($request->filled('type_parent')) {
            $query->where('type_parent', $request->type_parent);
        }

        if ($request->filled('est_actif')) {
            $query->where('est_actif', $request->boolean('est_actif'));
        }

        $parents = $query->orderBy('nom')->orderBy('prenom')->paginate(15);
        $etudiants = Etudiant::all();

        return view('parents.index', compact('parents', 'etudiants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $etudiants = Etudiant::all();
        return view('parents.create', compact('etudiants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:parents',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'profession' => 'nullable|string|max:255',
            'numero_parent' => 'required|string|max:50|unique:parents',
            'type_parent' => 'required|in:pere,mere,tuteur,autre',
            'etudiants' => 'required|array',
            'etudiants.*' => 'exists:etudiants,id',
            'est_responsable_legal' => 'array',
            'peut_recevoir_notifications' => 'array',
        ]);

        $parent = ParentEtudiant::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'profession' => $request->profession,
            'numero_parent' => $request->numero_parent,
            'type_parent' => $request->type_parent,
            'est_actif' => true,
        ]);

        // Attacher les étudiants avec les options
        $etudiantsData = [];
        foreach ($request->etudiants as $etudiantId) {
            $etudiantsData[$etudiantId] = [
                'type_relation' => $request->type_parent,
                'est_responsable_legal' => in_array($etudiantId, $request->est_responsable_legal ?? []),
                'peut_recevoir_notifications' => in_array($etudiantId, $request->peut_recevoir_notifications ?? []),
            ];
        }

        $parent->etudiants()->attach($etudiantsData);

        return redirect()->route('parents.index')
            ->with('success', 'Parent créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ParentEtudiant $parent): View
    {
        $parent->load(['etudiants.utilisateur', 'etudiants.classes']);
        return view('parents.show', compact('parent'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ParentEtudiant $parent): View
    {
        $etudiants = Etudiant::all();
        $parent->load('etudiants');
        return view('parents.edit', compact('parent', 'etudiants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ParentEtudiant $parent): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:parents,email,' . $parent->id,
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'profession' => 'nullable|string|max:255',
            'numero_parent' => 'required|string|max:50|unique:parents,numero_parent,' . $parent->id,
            'type_parent' => 'required|in:pere,mere,tuteur,autre',
            'etudiants' => 'required|array',
            'etudiants.*' => 'exists:etudiants,id',
            'est_responsable_legal' => 'array',
            'peut_recevoir_notifications' => 'array',
        ]);

        $parent->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'profession' => $request->profession,
            'numero_parent' => $request->numero_parent,
            'type_parent' => $request->type_parent,
        ]);

        // Mettre à jour les relations avec les étudiants
        $etudiantsData = [];
        foreach ($request->etudiants as $etudiantId) {
            $etudiantsData[$etudiantId] = [
                'type_relation' => $request->type_parent,
                'est_responsable_legal' => in_array($etudiantId, $request->est_responsable_legal ?? []),
                'peut_recevoir_notifications' => in_array($etudiantId, $request->peut_recevoir_notifications ?? []),
            ];
        }

        $parent->etudiants()->sync($etudiantsData);

        return redirect()->route('parents.index')
            ->with('success', 'Parent mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ParentEtudiant $parent): RedirectResponse
    {
        $parent->delete();

        return redirect()->route('parents.index')
            ->with('success', 'Parent supprimé avec succès.');
    }

    /**
     * Activer/Désactiver un parent.
     */
    public function toggleStatus(ParentEtudiant $parent): RedirectResponse
    {
        $parent->update(['est_actif' => !$parent->est_actif]);

        $status = $parent->est_actif ? 'activé' : 'désactivé';
        return redirect()->route('parents.index')
            ->with('success', "Parent {$status} avec succès.");
    }
}
