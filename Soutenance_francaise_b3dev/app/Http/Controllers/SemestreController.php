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
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $semestres = Semestre::with('anneeAcademique')->orderBy('nom')->paginate(10);
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
        $semestre->load(['anneeAcademique', 'sessionsDeCours.classe', 'sessionsDeCours.matiere']);
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
        if ($semestre->sessionsDeCours()->count() > 0) {
            return redirect()->route('semestres.index')
                ->with('error', 'Impossible de supprimer ce semestre car il contient des sessions de cours.');
        }

        $semestre->delete();

        return redirect()->route('semestres.index')
            ->with('success', 'Semestre supprimé avec succès.');
    }
}
