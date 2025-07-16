<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\AnneeAcademique;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClasseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $classes = Classe::with(['etudiants', 'sessionsDeCours'])->paginate(10);
        return view('classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $anneesAcademiques = AnneeAcademique::all();
        return view('classes.create', compact('anneesAcademiques'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'niveau' => 'nullable|string|max:255',
        ]);

        Classe::create($request->all());

        return redirect()->route('classes.index')
            ->with('success', 'Classe créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Classe $class): View
    {
        $class->load(['etudiants', 'sessionsDeCours.matiere', 'sessionsDeCours.enseignant']);
        return view('classes.show', compact('class'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classe $class): View
    {
        return view('classes.edit', compact('class'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classe $class): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'niveau' => 'nullable|string|max:255',
        ]);

        $class->update($request->all());

        return redirect()->route('classes.index')
            ->with('success', 'Classe mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classe $class): RedirectResponse
    {
        // Vérifier s'il y a des données liées
        if ($class->etudiants()->count() > 0 || $class->sessionsDeCours()->count() > 0) {
            return redirect()->route('classes.index')
                ->with('error', 'Impossible de supprimer cette classe car elle contient des données liées.');
        }

        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Classe supprimée avec succès.');
    }

    /**
     * Marquer un semestre comme terminé pour une classe.
     */
    public function semestreTermine(Request $request, Classe $classe): RedirectResponse
    {
        $request->validate([
            'semestre_id' => 'required|exists:semestres,id',
        ]);

        // Logique pour marquer le semestre comme terminé
        // Ici vous pouvez ajouter la logique métier nécessaire

        return redirect()->route('classes.show', $classe)
            ->with('success', 'Semestre marqué comme terminé avec succès.');
    }
}
