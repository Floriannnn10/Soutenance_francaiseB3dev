<?php

namespace App\Http\Controllers;

use App\Models\SessionDeCours;
use App\Models\Semestre;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Enseignant;
use App\Models\TypeCours;
use App\Models\StatutSession;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SessionDeCoursController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = SessionDeCours::with(['semestre.anneeAcademique', 'classe', 'matiere', 'enseignant', 'typeCours', 'statutSession']);

        // Filtres
        if ($request->filled('semestre_id')) {
            $query->where('semestre_id', $request->semestre_id);
        }
        if ($request->filled('classe_id')) {
            $query->where('classe_id', $request->classe_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $sessions = $query->orderBy('date')->orderBy('heure_debut')->paginate(15);
        $semestres = Semestre::with('anneeAcademique')->get();
        $classes = Classe::all();

        return view('sessions-de-cours.index', compact('sessions', 'semestres', 'classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $semestres = Semestre::with('anneeAcademique')->get();
        $classes = Classe::all();
        $matieres = Matiere::all();
        $enseignants = Enseignant::all();
        $typesCours = TypeCours::all();
        $statutsSession = StatutSession::all();

        return view('sessions-de-cours.create', compact(
            'semestres', 'classes', 'matieres', 'enseignants', 'typesCours', 'statutsSession'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'semestre_id' => 'required|exists:semestres,id',
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'type_cours_id' => 'required|exists:types_cours,id',
            'statut_session_id' => 'required|exists:statuts_session,id',
            'date' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'salle' => 'nullable|string|max:255',
            'commentaire' => 'nullable|string',
        ]);

        SessionDeCours::create($request->all());

        return redirect()->route('sessions-de-cours.index')
            ->with('success', 'Session de cours créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SessionDeCours $sessionDeCour): View
    {
        $sessionDeCour->load([
            'semestre.anneeAcademique',
            'classe',
            'matiere',
            'enseignant',
            'typeCours',
            'statutSession',
            'presences.etudiant',
            'presences.statutPresence'
        ]);

        return view('sessions-de-cours.show', compact('sessionDeCour'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SessionDeCours $sessionDeCour): View
    {
        $semestres = Semestre::with('anneeAcademique')->get();
        $classes = Classe::all();
        $matieres = Matiere::all();
        $enseignants = Enseignant::all();
        $typesCours = TypeCours::all();
        $statutsSession = StatutSession::all();

        return view('sessions-de-cours.edit', compact(
            'sessionDeCour', 'semestres', 'classes', 'matieres', 'enseignants', 'typesCours', 'statutsSession'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SessionDeCours $sessionDeCour): RedirectResponse
    {
        $request->validate([
            'semestre_id' => 'required|exists:semestres,id',
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'type_cours_id' => 'required|exists:types_cours,id',
            'statut_session_id' => 'required|exists:statuts_session,id',
            'date' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'salle' => 'nullable|string|max:255',
            'commentaire' => 'nullable|string',
        ]);

        $sessionDeCour->update($request->all());

        return redirect()->route('sessions-de-cours.index')
            ->with('success', 'Session de cours mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SessionDeCours $sessionDeCour): RedirectResponse
    {
        // Vérifier s'il y a des présences liées
        if ($sessionDeCour->presences()->count() > 0) {
            return redirect()->route('sessions-de-cours.index')
                ->with('error', 'Impossible de supprimer cette session car elle contient des présences enregistrées.');
        }

        $sessionDeCour->delete();

        return redirect()->route('sessions-de-cours.index')
            ->with('success', 'Session de cours supprimée avec succès.');
    }

    /**
     * Afficher les sessions pour aujourd'hui.
     */
    public function today(): View
    {
        $sessions = SessionDeCours::with(['classe', 'matiere', 'enseignant', 'statutSession'])
            ->whereDate('date', today())
            ->orderBy('heure_debut')
            ->get();

        return view('sessions-de-cours.today', compact('sessions'));
    }

    /**
     * Reporter une session.
     */
    public function report(Request $request, SessionDeCours $sessionDeCour): RedirectResponse
    {
        $request->validate([
            'new_date' => 'required|date|after:today',
            'new_heure_debut' => 'required|date_format:H:i',
            'new_heure_fin' => 'required|date_format:H:i|after:new_heure_debut',
        ]);

        // Créer une nouvelle session reportée
        $newSession = $sessionDeCour->replicate();
        $newSession->date = $request->new_date;
        $newSession->heure_debut = $request->new_heure_debut;
        $newSession->heure_fin = $request->new_heure_fin;
        $newSession->session_originale_id = $sessionDeCour->id;
        $newSession->save();

        // Marquer l'ancienne session comme reportée
        $statutReportee = StatutSession::where('nom', StatutSession::REPORTEE)->first();
        $sessionDeCour->update(['statut_session_id' => $statutReportee->id]);

        return redirect()->route('sessions-de-cours.index')
            ->with('success', 'Session reportée avec succès.');
    }
}
