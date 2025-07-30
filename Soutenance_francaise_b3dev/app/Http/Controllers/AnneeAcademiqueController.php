<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Traits\DaisyUINotifier;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class AnneeAcademiqueController extends Controller
{
    use DaisyUINotifier;
    /**
     * Afficher la liste des années académiques.
     */
    public function index(Request $request): View
    {
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50]) ? $perPage : 10;

        $anneesAcademiques = AnneeAcademique::orderBy('date_debut', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

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

        $data = $request->all();
        $data['actif'] = $request->has('actif');

        $anneeAcademique = AnneeAcademique::create($data);

        return $this->successNotification('Année académique créée avec succès !', 'annees-academiques.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(AnneeAcademique $anneeAcademique): View
    {
        $anneeAcademique->load(['semestres']);
        // Temporairement désactivé : 'inscriptions.classe', 'inscriptions.etudiant'
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

        $data = $request->all();
        $data['actif'] = $request->has('actif');

        $anneeAcademique->update($data);

        return $this->warningNotification('Année académique mise à jour avec succès !', 'annees-academiques.index');
    }

    /**
     * Supprimer l'année académique spécifiée du stockage.
     */
    public function destroy(AnneeAcademique $anneeAcademique): RedirectResponse
    {
        try {
            // Vérifier s'il y a des semestres liés à cette année académique
            $semestresCount = $anneeAcademique->semestres()->count();

            if ($semestresCount > 0) {
                return redirect()->route('annees-academiques.index')
                    ->with('error', 'Impossible de supprimer cette année académique car elle contient ' . $semestresCount . ' semestre(s). Veuillez d\'abord supprimer ces semestres.');
            }

            $anneeAcademique->delete();

                    return $this->errorNotification('Année académique supprimée avec succès !', 'annees-academiques.index');
        } catch (\Exception $e) {
            return redirect()->route('annees-academiques.index')
                ->with('error', 'Erreur lors de la suppression de l\'année académique : ' . $e->getMessage());
        }
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

    /**
     * Désactiver une année académique.
     */
    public function deactivate(AnneeAcademique $anneeAcademique): RedirectResponse
    {
        $anneeAcademique->update(['actif' => false]);

        return redirect()->route('annees-academiques.index')
            ->with('success', 'Année académique désactivée avec succès.');
    }
}
