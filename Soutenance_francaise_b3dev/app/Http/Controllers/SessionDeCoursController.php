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
use Illuminate\Support\Facades\DB;

class SessionDeCoursController extends Controller
{
    /**
     * Afficher la liste des sessions de cours.
     */
    public function index(Request $request): View
    {
        $perPage = $request->get('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50]) ? $perPage : 15;

        // Charger les sessions avec toutes les informations jointes
        $sessionsQuery = DB::table('course_sessions')
            ->select(
                'course_sessions.*',
                'matieres.nom as matiere_nom',
                'classes.nom as classe_nom',
                'enseignants.nom as enseignant_nom',
                'enseignants.prenom as enseignant_prenom',
                'statuts_session.nom as statut_nom',
                'semestres.nom as semestre_nom',
                'annees_academiques.nom as annee_nom',
                'types_cours.nom as type_cours_nom' // Ajout du nom du type de cours
            )
            ->leftJoin('matieres', 'course_sessions.matiere_id', '=', 'matieres.id')
            ->leftJoin('classes', 'course_sessions.classe_id', '=', 'classes.id')
            ->leftJoin('enseignants', 'course_sessions.enseignant_id', '=', 'enseignants.id')
            ->leftJoin('statuts_session', 'course_sessions.status_id', '=', 'statuts_session.id')
            ->leftJoin('semestres', 'course_sessions.semester_id', '=', 'semestres.id')
            ->leftJoin('annees_academiques', 'semestres.annee_academique_id', '=', 'annees_academiques.id')
            ->leftJoin('types_cours', 'course_sessions.type_cours_id', '=', 'types_cours.id') // Jointure ajoutée
            ->orderBy('course_sessions.start_time', 'desc');

        // Appliquer les filtres si présents
        if ($request->filled('semestre_id')) {
            $sessionsQuery->where('course_sessions.semester_id', $request->semestre_id);
        }

        if ($request->filled('classe_id')) {
            $sessionsQuery->where('course_sessions.classe_id', $request->classe_id);
        }

        if ($request->filled('matiere_id')) {
            $sessionsQuery->where('course_sessions.matiere_id', $request->matiere_id);
        }

        if ($request->filled('status_id')) {
            $sessionsQuery->where('course_sessions.status_id', $request->status_id);
        }

        // Paginer les résultats
        $sessions = $sessionsQuery->paginate($perPage)->appends($request->query());

        return view('sessions-de-cours.index', compact('sessions'));
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
            'status_id' => 'required|exists:statuts_session,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Créer la session de cours avec les bons noms de colonnes
        $sessionDeCours = new \stdClass();
        $sessionDeCours = DB::table('course_sessions')->insertGetId([
            'semester_id' => $request->semestre_id,
            'classe_id' => $request->classe_id,
            'matiere_id' => $request->matiere_id,
            'enseignant_id' => $request->enseignant_id,
            'type_cours_id' => $request->type_cours_id,
            'status_id' => $request->status_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'location' => $request->location,
            'notes' => $request->notes,
            'academic_year_id' => DB::table('semestres')
                ->where('id', $request->semestre_id)
                ->value('annee_academique_id'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('semestres.show', $request->semestre_id)
            ->with('success', 'Session de cours créée avec succès.');
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
        $statutReportee = StatutSession::where('nom', 'Reportée')->first();
        $sessionDeCour->update(['statut_session_id' => $statutReportee->id]);

        return redirect()->route('sessions-de-cours.index')
            ->with('success', 'Session reportée avec succès.');
    }

    /**
     * Afficher la page d'appel des présences pour une session.
     */
    public function appel($sessionId)
    {
        // Récupérer la session avec toutes les informations nécessaires
        $session = DB::table('course_sessions')
            ->select(
                'course_sessions.*',
                'matieres.nom as matiere_nom',
                'classes.nom as classe_nom',
                'enseignants.nom as enseignant_nom',
                'enseignants.prenom as enseignant_prenom',
                'semestres.nom as semestre_nom',
                'annees_academiques.nom as annee_nom',
                'types_cours.nom as type_cours_nom'
            )
            ->leftJoin('matieres', 'course_sessions.matiere_id', '=', 'matieres.id')
            ->leftJoin('classes', 'course_sessions.classe_id', '=', 'classes.id')
            ->leftJoin('enseignants', 'course_sessions.enseignant_id', '=', 'enseignants.id')
            ->leftJoin('semestres', 'course_sessions.semester_id', '=', 'semestres.id')
            ->leftJoin('annees_academiques', 'semestres.annee_academique_id', '=', 'annees_academiques.id')
            ->leftJoin('types_cours', 'course_sessions.type_cours_id', '=', 'types_cours.id')
            ->where('course_sessions.id', $sessionId)
            ->first();

        if (!$session) {
            return redirect()->route('sessions-de-cours.index')
                ->with('error', 'Session de cours introuvable.');
        }

        // Récupérer tous les étudiants de la classe
        $etudiants = DB::table('etudiants')
            ->select('etudiants.*')
            ->where('etudiants.classe_id', $session->classe_id)
            ->orderBy('nom')
            ->get();

        // Récupérer les présences déjà enregistrées pour cette session
        $presencesExistantes = DB::table('presences')
            ->select('presences.*', 'statuts_presence.nom as statut_nom')
            ->leftJoin('statuts_presence', 'presences.statut_presence_id', '=', 'statuts_presence.id')
            ->where('presences.course_session_id', $sessionId)
            ->get()
            ->keyBy('etudiant_id');

        // Récupérer les statuts de présence disponibles
        $statutsPresence = DB::table('statuts_presence')->get();

        return view('sessions-de-cours.appel', compact(
            'session', 'etudiants', 'presencesExistantes', 'statutsPresence'
        ));
    }

    /**
     * Enregistrer les présences d'une session.
     */
    public function enregistrerPresences(Request $request, $sessionId): RedirectResponse
    {
        $request->validate([
            'presences' => 'required|array',
            'presences.*' => 'required|exists:statuts_presence,id'
        ]);

        // Récupérer les informations de la session
        $session = DB::table('course_sessions')
            ->select('course_sessions.*', 'semestres.annee_academique_id')
            ->leftJoin('semestres', 'course_sessions.semester_id', '=', 'semestres.id')
            ->where('course_sessions.id', $sessionId)
            ->first();

        if (!$session) {
            return redirect()->route('sessions-de-cours.index')
                ->with('error', 'Session introuvable.');
        }

        // Supprimer les anciennes présences pour cette session
        DB::table('presences')->where('course_session_id', $sessionId)->delete();

        // Enregistrer les nouvelles présences
        foreach ($request->presences as $etudiantId => $statutId) {
            DB::table('presences')->insert([
                'etudiant_id' => $etudiantId,
                'course_session_id' => $sessionId,
                'statut_presence_id' => $statutId,
                'enregistre_le' => now(),
                'enregistre_par_user_id' => 1, // ID utilisateur par défaut
                'academic_year_id' => $session->annee_academique_id,
                'semester_id' => $session->semester_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return redirect()->route('sessions-de-cours.show', $sessionId)
            ->with('success', 'Présences enregistrées avec succès.');
    }

    /**
     * Afficher les détails d'une session de cours.
     */
    public function show($sessionId)
    {
        // Récupérer la session avec toutes les informations
        $session = DB::table('course_sessions')
            ->select(
                'course_sessions.*',
                'matieres.nom as matiere_nom',
                'classes.nom as classe_nom',
                'enseignants.nom as enseignant_nom',
                'enseignants.prenom as enseignant_prenom',
                'statuts_session.nom as statut_nom',
                'semestres.nom as semestre_nom',
                'annees_academiques.nom as annee_nom'
            )
            ->leftJoin('matieres', 'course_sessions.matiere_id', '=', 'matieres.id')
            ->leftJoin('classes', 'course_sessions.classe_id', '=', 'classes.id')
            ->leftJoin('enseignants', 'course_sessions.enseignant_id', '=', 'enseignants.id')
            ->leftJoin('statuts_session', 'course_sessions.status_id', '=', 'statuts_session.id')
            ->leftJoin('semestres', 'course_sessions.semester_id', '=', 'semestres.id')
            ->leftJoin('annees_academiques', 'semestres.annee_academique_id', '=', 'annees_academiques.id')
            ->where('course_sessions.id', $sessionId)
            ->first();

        if (!$session) {
            return redirect()->route('sessions-de-cours.index')
                ->with('error', 'Session introuvable.');
        }

        // Calculer les statistiques de présence
        $presencesStats = [
            'total' => 0,
            'present' => 0,
            'absent' => 0,
            'justified' => 0,
            'late' => 0,
            'left_early' => 0
        ];

        $presences = DB::table('presences')
            ->select('presences.*', 'statuts_presence.nom as statut_nom')
            ->leftJoin('statuts_presence', 'presences.statut_presence_id', '=', 'statuts_presence.id')
            ->where('presences.course_session_id', $sessionId)
            ->get();

        $presencesStats['total'] = $presences->count();

        foreach ($presences as $presence) {
            switch ($presence->statut_nom) {
                case 'Présent':
                    $presencesStats['present']++;
                    break;
                case 'Absent':
                    $presencesStats['absent']++;
                    break;
                case 'Absent Justifié':
                    $presencesStats['justified']++;
                    break;
                case 'Retard':
                    $presencesStats['late']++;
                    break;
                case 'Parti Tôt':
                    $presencesStats['left_early']++;
                    break;
            }
        }

        return view('sessions-de-cours.show', compact('session', 'presencesStats'));
    }
}
