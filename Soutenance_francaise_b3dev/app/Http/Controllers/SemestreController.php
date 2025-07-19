<?php

namespace App\Http\Controllers;

use App\Models\Semestre;
use App\Models\AnneeAcademique;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class SemestreController extends Controller
{
    /**
     * Afficher la liste des semestres.
     */
    public function index(Request $request): View
    {
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25, 50]) ? $perPage : 10;

        $semestres = Semestre::with('anneeAcademique')
            ->orderBy('date_debut', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

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
     * Afficher le semestre spécifié.
     */
    public function show(Semestre $semestre): View
    {
        // Charger les sessions de cours liées à ce semestre
        $sessionsDeCours = DB::table('course_sessions')
            ->select(
                'course_sessions.*',
                'matieres.nom as matiere_nom',
                'classes.nom as classe_nom',
                'enseignants.nom as enseignant_nom',
                'enseignants.prenom as enseignant_prenom'
            )
            ->leftJoin('matieres', 'course_sessions.matiere_id', '=', 'matieres.id')
            ->leftJoin('classes', 'course_sessions.classe_id', '=', 'classes.id')
            ->leftJoin('enseignants', 'course_sessions.enseignant_id', '=', 'enseignants.id')
            ->where('course_sessions.semester_id', $semestre->id)
            ->get();

        // Compter les dépendances
        $dependancesCount = [
            'sessions_cours' => $sessionsDeCours->count(),
            'presences' => DB::table('presences')
                ->whereIn('course_session_id', $sessionsDeCours->pluck('id'))
                ->count()
        ];

        return view('semestres.show', compact('semestre', 'sessionsDeCours', 'dependancesCount'));
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
     * Supprimer le semestre spécifié du stockage.
     */
    public function destroy(Semestre $semestre): RedirectResponse
    {
        try {
            // Vérifier s'il y a des sessions de cours liées à ce semestre
            $sessionsCount = DB::table('course_sessions')
                ->where('semester_id', $semestre->id)
                ->count();

            if ($sessionsCount > 0) {
                return redirect()->route('semestres.index')
                    ->with('error', 'Impossible de supprimer ce semestre car il contient ' . $sessionsCount . ' session(s) de cours. Veuillez d\'abord supprimer ou réassigner ces sessions.');
            }

            $semestre->delete();

            return redirect()->route('semestres.index')
                ->with('success', 'Semestre supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('semestres.index')
                ->with('error', 'Erreur lors de la suppression du semestre : ' . $e->getMessage());
        }
    }

    /**
     * Activer un semestre.
     */
    public function activate(Semestre $semestre): RedirectResponse
    {
        $semestre->activate();

        return redirect()->route('semestres.index')
            ->with('success', 'Semestre activé avec succès.');
    }

    /**
     * Désactiver un semestre.
     */
    public function deactivate(Semestre $semestre): RedirectResponse
    {
        $semestre->update(['actif' => false]);

        return redirect()->route('semestres.index')
            ->with('success', 'Semestre désactivé avec succès.');
    }
}
