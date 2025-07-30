<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use App\Traits\DaisyUINotifier;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Enseignant;

class MatiereController extends Controller
{
    use DaisyUINotifier;
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
        $enseignants = Enseignant::orderBy('nom')->get();
        return view('matieres.create', compact('enseignants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:matieres',
            'coefficient' => 'required|numeric|min:1',
            'volume_horaire' => 'required|integer|min:1',
            'enseignants' => 'array',
            'enseignants.*' => 'exists:enseignants,id',
        ]);

        $matiere = Matiere::create([
            'nom' => $request->nom,
            'code' => $request->code,
            'coefficient' => $request->coefficient,
            'volume_horaire' => $request->volume_horaire,
        ]);

        // Associer les enseignants sélectionnés
        if ($request->filled('enseignants')) {
            $matiere->enseignants()->attach($request->enseignants);
        }

        return $this->successNotification('Matière créée avec succès !', 'matieres.index');
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
        $enseignants = Enseignant::orderBy('nom')->get();
        return view('matieres.edit', compact('matiere', 'enseignants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Matiere $matiere): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:matieres,code,' . $matiere->id,
            'coefficient' => 'required|numeric|min:1',
            'volume_horaire' => 'required|integer|min:1',
            'enseignants' => 'array',
            'enseignants.*' => 'exists:enseignants,id',
        ]);

        $matiere->update([
            'nom' => $request->nom,
            'code' => $request->code,
            'coefficient' => $request->coefficient,
            'volume_horaire' => $request->volume_horaire,
        ]);

        // Synchroniser les enseignants
        $matiere->enseignants()->sync($request->enseignants ?? []);

        return $this->warningNotification('Matière mise à jour avec succès !', 'matieres.index');
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

        return $this->errorNotification('Matière supprimée avec succès !', 'matieres.index');
    }
}
