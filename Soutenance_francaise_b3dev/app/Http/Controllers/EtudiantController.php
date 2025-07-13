<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EtudiantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Etudiant::with(['classes', 'presences']);

        if ($request->filled('classe_id')) {
            $query->whereHas('classes', function ($q) use ($request) {
                $q->where('classes.id', $request->classe_id);
            });
        }

        $etudiants = $query->orderBy('nom')->orderBy('prenom')->paginate(15);
        $classes = Classe::all();

        return view('etudiants.index', compact('etudiants', 'classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $classes = Classe::all();
        return view('etudiants.create', compact('classes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:etudiants',
            'telephone' => 'nullable|string|max:20',
            'date_naissance' => 'nullable|date',
            'adresse' => 'nullable|string',
            'numero_etudiant' => 'required|string|max:50|unique:etudiants',
            'classe_id' => 'required|exists:classes,id',
        ]);

        $etudiant = Etudiant::create($request->except('classe_id'));

        // Attacher à la classe
        if ($request->classe_id) {
            $etudiant->classes()->attach($request->classe_id);
        }

        return redirect()->route('etudiants.index')
            ->with('success', 'Étudiant créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Etudiant $etudiant): View
    {
        $etudiant->load(['classes', 'presences.sessionDeCours.matiere', 'presences.statutPresence']);
        return view('etudiants.show', compact('etudiant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Etudiant $etudiant): View
    {
        $classes = Classe::all();
        return view('etudiants.edit', compact('etudiant', 'classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Etudiant $etudiant): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:etudiants,email,' . $etudiant->id,
            'telephone' => 'nullable|string|max:20',
            'date_naissance' => 'nullable|date',
            'adresse' => 'nullable|string',
            'numero_etudiant' => 'required|string|max:50|unique:etudiants,numero_etudiant,' . $etudiant->id,
            'classe_id' => 'required|exists:classes,id',
        ]);

        $etudiant->update($request->except('classe_id'));

        // Mettre à jour la classe
        if ($request->classe_id) {
            $etudiant->classes()->sync([$request->classe_id]);
        }

        return redirect()->route('etudiants.index')
            ->with('success', 'Étudiant mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Etudiant $etudiant): RedirectResponse
    {
        if ($etudiant->presences()->count() > 0) {
            return redirect()->route('etudiants.index')
                ->with('error', 'Impossible de supprimer cet étudiant car il a des présences enregistrées.');
        }

        $etudiant->delete();

        return redirect()->route('etudiants.index')
            ->with('success', 'Étudiant supprimé avec succès.');
    }
}
