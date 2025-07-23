<?php

namespace App\Http\Controllers;

use App\Models\Coordinateur;
use App\Models\Classe;
use App\Models\AnneeAcademique;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class CoordinateurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Coordinateur::with('promotion');

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
        $promotions = \App\Models\Promotion::all();
        return view('coordinateurs.create', compact('promotions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'promotion_id' => 'required|exists:promotions,id',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Vérifier unicité de la promotion
        if (\App\Models\Coordinateur::where('promotion_id', $request->promotion_id)->exists()) {
            return back()->withInput()->withErrors(['promotion_id' => 'Cette promotion est déjà attribuée à un autre coordinateur.']);
        }

        $coordinateur = Coordinateur::create([
            'user_id' => Auth::id(),
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'promotion_id' => $request->promotion_id,
            'est_actif' => true,
        ]);

        // Gérer l'upload de photo si fournie
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('coordinateurs', 'public');
            $coordinateur->update(['photo' => $photoPath]);
        }

        return redirect()->route('coordinateurs.index')
            ->with('success', 'Coordinateur créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Coordinateur $coordinateur): View
    {
        $coordinateur->load(['promotion']);
        return view('coordinateurs.show', compact('coordinateur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coordinateur $coordinateur): View
    {
        $promotions = \App\Models\Promotion::all();
        return view('coordinateurs.edit', compact('coordinateur', 'promotions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coordinateur $coordinateur): RedirectResponse
    {
        $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'promotion_id' => 'required|exists:promotions,id',
            'photo' => 'nullable|image|max:2048',
        ]);
        // Vérifier unicité de la promotion (hors coordinateur actuel)
        if (\App\Models\Coordinateur::where('promotion_id', $request->promotion_id)->where('id', '!=', $coordinateur->id)->exists()) {
            return back()->withInput()->withErrors(['promotion_id' => 'Cette promotion est déjà attribuée à un autre coordinateur.']);
        }

        $data = [
            'prenom' => $request->prenom,
            'nom' => $request->nom,
            'promotion_id' => $request->promotion_id,
        ];

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('coordinateurs', 'public');
            $data['photo'] = $photoPath;
        }

        $coordinateur->update($data);

        return redirect()->route('coordinateurs.show', $coordinateur->id)
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
