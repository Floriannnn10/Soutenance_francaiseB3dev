<?php

namespace App\Http\Controllers;

use App\Models\Enseignant;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EnseignantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $enseignants = Enseignant::with(['sessionsDeCours.matiere', 'sessionsDeCours.classe'])
            ->orderBy('nom')
            ->orderBy('prenom')
            ->paginate(15);

        return view('enseignants.index', compact('enseignants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('enseignants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:enseignants',
            'telephone' => 'nullable|string|max:20',
            'specialite' => 'nullable|string|max:255',
            'grade' => 'nullable|string|max:100',
            'adresse' => 'nullable|string',
            'numero_enseignant' => 'required|string|max:50|unique:enseignants',
        ]);

        Enseignant::create($request->all());

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
        return view('enseignants.edit', compact('enseignant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Enseignant $enseignant): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:enseignants,email,' . $enseignant->id,
            'telephone' => 'nullable|string|max:20',
            'specialite' => 'nullable|string|max:255',
            'grade' => 'nullable|string|max:100',
            'adresse' => 'nullable|string',
            'numero_enseignant' => 'required|string|max:50|unique:enseignants,numero_enseignant,' . $enseignant->id,
        ]);

        $enseignant->update($request->all());

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
