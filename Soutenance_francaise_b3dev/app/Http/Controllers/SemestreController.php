<?php

namespace App\Http\Controllers;

use App\Models\Semestre;
use App\Models\AnneeAcademique;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SemestreController extends Controller
{
    /**
     * Afficher la liste des semestres.
     */
    public function index(Request $request): View
    {
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50]) ? $perPage : 10;

        $semestres = Semestre::with('anneeAcademique')
            ->orderBy('date_debut', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        return view('semestres.index', compact('semestres'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $anneesAcademiques = AnneeAcademique::all();
        return view('semestres.create', compact('anneesAcademiques'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'annee_academique_id' => 'required|exists:annees_academiques,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
        ]);

        Semestre::create($request->all());

        return redirect()->route('semestres.index')
            ->with('success', 'Semestre créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Semestre $semestre): View
    {
        $semestre->load(['anneeAcademique']);
        // Temporairement désactivé : 'sessionsDeCours.classe', 'sessionsDeCours.matiere'
        return view('semestres.show', compact('semestre'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Semestre $semestre): View
    {
        $anneesAcademiques = AnneeAcademique::all();
        return view('semestres.edit', compact('semestre', 'anneesAcademiques'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Semestre $semestre): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'annee_academique_id' => 'required|exists:annees_academiques,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
        ]);

        $semestre->update($request->all());

        return redirect()->route('semestres.index')
            ->with('success', 'Semestre mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Semestre $semestre): RedirectResponse
    {
        // Temporairement désactivé la vérification des sessions de cours
        // if ($semestre->sessionsDeCours()->count() > 0) {
        //     return redirect()->route('semestres.index')
        //         ->with('error', 'Impossible de supprimer ce semestre car il contient des sessions de cours.');
        // }

        $semestre->delete();

        return redirect()->route('semestres.index')
            ->with('success', 'Semestre supprimé avec succès.');
    }

    /**
     * Activer un semestre.
     */
    public function activate(Semestre $semestre): RedirectResponse
    {
        $semestre->activate();

        return redirect()->route('semestres.index')
            ->with('success', 'Semestre activé avec succès.');
    }

    /**
     * Désactiver un semestre.
     */
    public function deactivate(Semestre $semestre): RedirectResponse
    {
        $semestre->update(['actif' => false]);

        return redirect()->route('semestres.index')
            ->with('success', 'Semestre désactivé avec succès.');
    }
}
