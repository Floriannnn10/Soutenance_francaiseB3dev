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

    /**
     * Dashboard coordinateur avec graphes dynamiques
     */
    public function dashboard(Request $request)
    {
        // Période sélectionnée (par défaut : tout)
        $periodeDebut = $request->input('debut');
        $periodeFin = $request->input('fin');
        $classeId = $request->input('classe_id');

        // 1. Taux de présence par étudiant (pour une classe et une période)
        $etudiants = \App\Models\Etudiant::query();
        if ($classeId) {
            $etudiants->where('classe_id', $classeId);
        }
        $etudiants = $etudiants->get();
        $presenceParEtudiant = [];
        foreach ($etudiants as $etudiant) {
            $presences = $etudiant->presences();
            if ($periodeDebut) $presences->where('enregistre_le', '>=', $periodeDebut);
            if ($periodeFin) $presences->where('enregistre_le', '<=', $periodeFin);
            $total = $presences->count();
            $presents = $presences->whereHas('statutPresence', function($q){ $q->where('nom', 'Présent'); })->count();
            $taux = $total > 0 ? round($presents / $total * 100, 1) : 0;
            $presenceParEtudiant[] = [
                'nom' => $etudiant->prenom . ' ' . $etudiant->nom,
                'taux' => $taux,
            ];
        }
        usort($presenceParEtudiant, fn($a, $b) => $b['taux'] <=> $a['taux']);

        // 2. Taux de présence par classe
        $classes = \App\Models\Classe::all();
        $presenceParClasse = [];
        foreach ($classes as $classe) {
            $etudiants = $classe->etudiants;
            $total = 0; $presents = 0;
            foreach ($etudiants as $etudiant) {
                $presences = $etudiant->presences();
                if ($periodeDebut) $presences->where('enregistre_le', '>=', $periodeDebut);
                if ($periodeFin) $presences->where('enregistre_le', '<=', $periodeFin);
                $total += $presences->count();
                $presents += $presences->whereHas('statutPresence', function($q){ $q->where('nom', 'Présent'); })->count();
            }
            $taux = $total > 0 ? round($presents / $total * 100, 1) : 0;
            $presenceParClasse[] = [
                'nom' => $classe->nom,
                'taux' => $taux,
            ];
        }

        // 3. Volume de cours dispensés par type
        $types = \App\Models\TypeCours::whereIn('nom', ['Workshop', 'E-learning', 'Présentiel'])->get();
        $volumeParType = [];
        foreach ($types as $type) {
            $sessions = $type->sessionsDeCours();
            if ($periodeDebut) $sessions->where('start_time', '>=', $periodeDebut);
            if ($periodeFin) $sessions->where('end_time', '<=', $periodeFin);
            $volumeParType[] = [
                'type' => $type->nom,
                'nb' => $sessions->count(),
            ];
        }

        // 4. Volume cumulé de cours dispensés par semestre
        $volumeCumule = [];
        $semestres = \App\Models\Semestre::orderBy('date_debut')->get();
        foreach ($semestres as $semestre) {
            $sessions = \App\Models\SessionDeCours::where('semester_id', $semestre->id);
            if ($classeId) {
                $sessions->where('classe_id', $classeId);
            }
            if ($periodeDebut) $sessions->where('start_time', '>=', $periodeDebut);
            if ($periodeFin) $sessions->where('end_time', '<=', $periodeFin);
            $volumeCumule[] = [
                'periode' => $semestre->nom,
                'nb' => $sessions->count(),
            ];
        }

        return view('dashboard.coordinateur', compact(
            'presenceParEtudiant', 'presenceParClasse', 'volumeParType', 'volumeCumule', 'classes', 'classeId', 'periodeDebut', 'periodeFin'
        ));
    }
}
