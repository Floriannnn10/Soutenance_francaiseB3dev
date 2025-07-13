<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MatiereController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $matieres = Matiere::orderBy('nom')->paginate(10);
        return view('matieres.index', compact('matieres'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('matieres.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:matieres',
            'description' => 'nullable|string',
            'coefficient' => 'required|numeric|min:0',
            'volume_horaire' => 'required|integer|min:0',
        ]);

        Matiere::create($request->all());

        return redirect()->route('matieres.index')
            ->with('success', 'Matière créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Matiere $matiere): View
    {
        $matiere->load(['sessionsDeCours.classe', 'sessionsDeCours.enseignant']);
        return view('matieres.show', compact('matiere'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Matiere $matiere): View
    {
        return view('matieres.edit', compact('matiere'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Matiere $matiere): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:matieres,code,' . $matiere->id,
            'description' => 'nullable|string',
            'coefficient' => 'required|numeric|min:0',
            'volume_horaire' => 'required|integer|min:0',
        ]);

        $matiere->update($request->all());

        return redirect()->route('matieres.index')
            ->with('success', 'Matière mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Matiere $matiere): RedirectResponse
    {
        if ($matiere->sessionsDeCours()->count() > 0) {
            return redirect()->route('matieres.index')
                ->with('error', 'Impossible de supprimer cette matière car elle contient des sessions de cours.');
        }

        $matiere->delete();

        return redirect()->route('matieres.index')
            ->with('success', 'Matière supprimée avec succès.');
    }
}
