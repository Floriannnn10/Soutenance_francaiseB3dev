<?php

namespace App\Http\Controllers;

use App\Models\Coordinateur;
use App\Models\Classe;
use App\Models\AnneeAcademique;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CoordinateurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Coordinateur::with(['classes', 'anneesAcademiques']);

        if ($request->filled('specialite')) {
            $query->where('specialite', $request->specialite);
        }

        if ($request->filled('est_actif')) {
            $query->where('est_actif', $request->boolean('est_actif'));
        }

        $coordinateurs = $query->orderBy('nom')->orderBy('prenom')->paginate(15);
        $classes = Classe::all();
        $anneesAcademiques = AnneeAcademique::all();

        return view('coordinateurs.index', compact('coordinateurs', 'classes', 'anneesAcademiques'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $classes = Classe::all();
        $anneesAcademiques = AnneeAcademique::all();
        return view('coordinateurs.create', compact('classes', 'anneesAcademiques'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:coordinateurs',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'specialite' => 'nullable|string|max:255',
            'grade' => 'nullable|string|max:100',
            'numero_coordinateur' => 'required|string|max:50|unique:coordinateurs',
            'responsabilites' => 'nullable|string',
            'classes' => 'required|array',
            'classes.*' => 'exists:classes,id',
            'annee_academique_id' => 'required|exists:annees_academiques,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after:date_debut',
        ]);

        $coordinateur = Coordinateur::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'specialite' => $request->specialite,
            'grade' => $request->grade,
            'numero_coordinateur' => $request->numero_coordinateur,
            'responsabilites' => $request->responsabilites,
            'est_actif' => true,
        ]);

        // Attacher les classes avec les options
        $classesData = [];
        foreach ($request->classes as $classeId) {
            $classesData[$classeId] = [
                'annee_academique_id' => $request->annee_academique_id,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'commentaire' => $request->commentaire,
                'est_actif' => true,
            ];
        }

        $coordinateur->classes()->attach($classesData);

        return redirect()->route('coordinateurs.index')
            ->with('success', 'Coordinateur créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Coordinateur $coordinateur): View
    {
        $coordinateur->load(['classes.etudiants', 'anneesAcademiques']);
        return view('coordinateurs.show', compact('coordinateur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coordinateur $coordinateur): View
    {
        $classes = Classe::all();
        $anneesAcademiques = AnneeAcademique::all();
        $coordinateur->load('classes');
        return view('coordinateurs.edit', compact('coordinateur', 'classes', 'anneesAcademiques'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coordinateur $coordinateur): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:coordinateurs,email,' . $coordinateur->id,
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'specialite' => 'nullable|string|max:255',
            'grade' => 'nullable|string|max:100',
            'numero_coordinateur' => 'required|string|max:50|unique:coordinateurs,numero_coordinateur,' . $coordinateur->id,
            'responsabilites' => 'nullable|string',
            'classes' => 'required|array',
            'classes.*' => 'exists:classes,id',
            'annee_academique_id' => 'required|exists:annees_academiques,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after:date_debut',
        ]);

        $coordinateur->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'specialite' => $request->specialite,
            'grade' => $request->grade,
            'numero_coordinateur' => $request->numero_coordinateur,
            'responsabilites' => $request->responsabilites,
        ]);

        // Mettre à jour les relations avec les classes
        $classesData = [];
        foreach ($request->classes as $classeId) {
            $classesData[$classeId] = [
                'annee_academique_id' => $request->annee_academique_id,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'commentaire' => $request->commentaire,
                'est_actif' => true,
            ];
        }

        $coordinateur->classes()->sync($classesData);

        return redirect()->route('coordinateurs.index')
            ->with('success', 'Coordinateur mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coordinateur $coordinateur): RedirectResponse
    {
        $coordinateur->delete();

        return redirect()->route('coordinateurs.index')
            ->with('success', 'Coordinateur supprimé avec succès.');
    }

    /**
     * Activer/Désactiver un coordinateur.
     */
    public function toggleStatus(Coordinateur $coordinateur): RedirectResponse
    {
        $coordinateur->update(['est_actif' => !$coordinateur->est_actif]);

        $status = $coordinateur->est_actif ? 'activé' : 'désactivé';
        return redirect()->route('coordinateurs.index')
            ->with('success', "Coordinateur {$status} avec succès.");
    }
}
