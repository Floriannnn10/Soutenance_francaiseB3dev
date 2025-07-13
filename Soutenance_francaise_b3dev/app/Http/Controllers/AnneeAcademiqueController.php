<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AnneeAcademiqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $anneesAcademiques = AnneeAcademique::orderBy('nom', 'desc')->paginate(10);
        return view('annees-academiques.index', compact('anneesAcademiques'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('annees-academiques.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:annees_academiques',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
        ]);

        $anneeAcademique = AnneeAcademique::create($request->all());

        return redirect()->route('annees-academiques.index')
            ->with('success', 'Année académique créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AnneeAcademique $anneeAcademique): View
    {
        $anneeAcademique->load(['semestres', 'inscriptions.classe', 'inscriptions.etudiant']);
        return view('annees-academiques.show', compact('anneeAcademique'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AnneeAcademique $anneeAcademique): View
    {
        return view('annees-academiques.edit', compact('anneeAcademique'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AnneeAcademique $anneeAcademique): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:annees_academiques,nom,' . $anneeAcademique->id,
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
        ]);

        $anneeAcademique->update($request->all());

        return redirect()->route('annees-academiques.index')
            ->with('success', 'Année académique mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AnneeAcademique $anneeAcademique): RedirectResponse
    {
        // Vérifier s'il y a des données liées
        if ($anneeAcademique->semestres()->count() > 0 || $anneeAcademique->inscriptions()->count() > 0) {
            return redirect()->route('annees-academiques.index')
                ->with('error', 'Impossible de supprimer cette année académique car elle contient des données liées.');
        }

        $anneeAcademique->delete();

        return redirect()->route('annees-academiques.index')
            ->with('success', 'Année académique supprimée avec succès.');
    }

    /**
     * Activer une année académique.
     */
    public function activate(AnneeAcademique $anneeAcademique): RedirectResponse
    {
        $anneeAcademique->activate();

        return redirect()->route('annees-academiques.index')
            ->with('success', 'Année académique activée avec succès.');
    }
}
