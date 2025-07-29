<?php

namespace App\Http\Controllers;

use App\Models\SessionDeCours;
use App\Models\Semestre;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Enseignant;
use App\Models\TypeCours;
use App\Models\StatutSession;
use App\Models\AnneeAcademique;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
                'annees_academiques.id as annee_id',
                'annees_academiques.date_debut as annee_date_debut',
                'annees_academiques.date_fin as annee_date_fin',
                'types_cours.nom as type_cours_nom',
                'types_cours.code as type_cours_code'
            )
            ->leftJoin('matieres', 'course_sessions.matiere_id', '=', 'matieres.id')
            ->leftJoin('classes', 'course_sessions.classe_id', '=', 'classes.id')
            ->leftJoin('enseignants', 'course_sessions.enseignant_id', '=', 'enseignants.id')
            ->leftJoin('statuts_session', 'course_sessions.status_id', '=', 'statuts_session.id')
            ->leftJoin('semestres', 'course_sessions.semester_id', '=', 'semestres.id')
            ->leftJoin('annees_academiques', 'semestres.annee_academique_id', '=', 'annees_academiques.id')
            ->leftJoin('types_cours', 'course_sessions.type_cours_id', '=', 'types_cours.id')
            ->orderBy('course_sessions.start_time', 'desc');

        // Filtrer selon le rôle de l'utilisateur
        $user = Auth::user();
        if ($user && $user->roles->first()->code === 'coordinateur') {
            // Coordinateur : voir seulement les sessions de sa promotion
            $coordinateur = $user->coordinateur;
            if ($coordinateur && $coordinateur->promotion) {
                $classesIds = $coordinateur->promotion->classes()->pluck('id');
                $sessionsQuery->whereIn('course_sessions.classe_id', $classesIds);
            }
        } elseif ($user && $user->roles->first()->code === 'enseignant') {
            // Enseignant : voir seulement ses sessions
            $enseignant = $user->enseignant;
            if ($enseignant) {
                $sessionsQuery->where('course_sessions.enseignant_id', $enseignant->id);
            }
        }

        // Appliquer les filtres si présents
        if ($request->filled('annee_academique_id')) {
            $sessionsQuery->where('annees_academiques.id', $request->annee_academique_id);
        }

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

        // Récupérer les données pour les filtres
        $anneesAcademiques = AnneeAcademique::orderBy('nom', 'desc')->get();
        $semestres = Semestre::with('anneeAcademique')->orderBy('nom')->get();
        $matieres = Matiere::orderBy('nom')->get();

        // Filtrer les classes selon le rôle de l'utilisateur
        if ($user && $user->roles->first()->code === 'coordinateur') {
            $coordinateur = $user->coordinateur;
            if ($coordinateur && $coordinateur->promotion) {
                $classes = $coordinateur->promotion->classes()->orderBy('nom')->get();
            } else {
                $classes = collect();
            }
        } else {
            $classes = Classe::orderBy('nom')->get();
        }

        // Récupérer seulement les types de cours autorisés
        $typesCours = TypeCours::whereIn('nom', ['Présentiel', 'E-learning', 'Workshop'])->orderBy('nom')->get();

        return view('sessions-de-cours.index', compact('sessions', 'anneesAcademiques', 'semestres', 'classes', 'matieres', 'typesCours'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View|RedirectResponse
    {
        // Récupérer l'année sélectionnée ou l'année en cours par défaut
        $anneeSelectionnee = $request->get('annee_academique_id');
        if (!$anneeSelectionnee) {
            $anneeEnCours = DB::table('annees_academiques')->where('statut', 'En cours')->first();
            $anneeSelectionnee = $anneeEnCours ? $anneeEnCours->id : null;
        }

        // Récupérer l'année pour vérifier le statut
        $anneeAcademique = null;
        if ($anneeSelectionnee) {
            $anneeAcademique = DB::table('annees_academiques')->find($anneeSelectionnee);
        }

        // Vérifier si l'utilisateur peut modifier cette année
        $user = Auth::user();
        $peutModifier = true;
        if ($user && $user->roles->first()->code === 'coordinateur') {
            if ($anneeAcademique && $anneeAcademique->date_debut && $anneeAcademique->date_fin) {
                $now = now();
                $dateFin = \Carbon\Carbon::parse($anneeAcademique->date_fin);
                if ($now->gt($dateFin)) {
                    $peutModifier = false;
                }
            }
        }

        // Si l'année est terminée, rediriger vers l'index avec un message
        if (!$peutModifier) {
            return redirect()->route('sessions-de-cours.index')
                ->with('error', 'Vous ne pouvez pas créer de sessions pour une année académique terminée.');
        }

        // Filtrer les semestres selon l'année sélectionnée
        $semestresQuery = Semestre::with('anneeAcademique');
        if ($anneeSelectionnee) {
            $semestresQuery = $semestresQuery->where('annee_academique_id', $anneeSelectionnee);
        }
        $semestres = $semestresQuery->get();

        $matieres = Matiere::all();
        $enseignants = Enseignant::all();
        $typesCours = TypeCours::whereIn('nom', ['Présentiel', 'E-learning', 'Workshop'])->get();
        $statutsSession = StatutSession::all();

        // Récupérer les coordinateurs pour les cours Workshop et E-learning
        $coordinateurs = \App\Models\Coordinateur::with('user')->get();

        // Filtrer les classes selon le rôle de l'utilisateur
        if ($user && $user->roles->first()->code === 'coordinateur') {
            $coordinateur = $user->coordinateur;
            if ($coordinateur && $coordinateur->promotion) {
                $classes = $coordinateur->promotion->classes()->orderBy('nom')->get();
            } else {
                $classes = collect();
            }
        } elseif ($user && $user->roles->first()->code === 'enseignant') {
            // Enseignant : voir seulement les classes où il enseigne
            $enseignant = $user->enseignant;
            if ($enseignant) {
                $classesIds = SessionDeCours::where('enseignant_id', $enseignant->id)
                    ->distinct()
                    ->pluck('classe_id');
                $classes = Classe::whereIn('id', $classesIds)->orderBy('nom')->get();
            } else {
                $classes = collect();
            }
        } else {
            $classes = Classe::orderBy('nom')->get();
        }

        // Récupérer toutes les années académiques pour le sélecteur
        $anneesAcademiques = DB::table('annees_academiques')->orderBy('nom', 'desc')->get();

        return view('sessions-de-cours.create', compact(
            'semestres', 'classes', 'matieres', 'enseignants', 'typesCours', 'statutsSession',
            'coordinateurs', 'anneesAcademiques', 'anneeSelectionnee', 'anneeAcademique', 'peutModifier'
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

        // Vérifier le statut de l'année académique
        $semestre = Semestre::with('anneeAcademique')->find($request->semestre_id);
        if ($semestre && $semestre->anneeAcademique && $semestre->anneeAcademique->date_debut && $semestre->anneeAcademique->date_fin) {
            $now = now();
            $dateFin = \Carbon\Carbon::parse($semestre->anneeAcademique->date_fin);
            if ($now->gt($dateFin)) {
                $user = Auth::user();
                if ($user && $user->roles->first()->code === 'coordinateur') {
                    return back()->withInput()->withErrors([
                        'semestre_id' => 'Vous ne pouvez pas créer de sessions pour une année académique terminée.'
                    ]);
                }
            }
        }

        // Vérifier que pour les cours Workshop et E-learning, l'enseignant est un coordinateur
        $typeCours = TypeCours::find($request->type_cours_id);
        if (in_array($typeCours->nom, ['Workshop', 'E-learning'])) {
            $enseignant = Enseignant::find($request->enseignant_id);
            if (!$enseignant || !$enseignant->user || $enseignant->user->roles->first()->code !== 'coordinateur') {
                return back()->withInput()->withErrors([
                    'enseignant_id' => 'Pour les cours de type "' . $typeCours->nom . '", l\'enseignant sélectionné doit obligatoirement être un coordinateur pédagogique.'
                ]);
            }
        }

        // Créer la session de cours avec les bons noms de colonnes
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
            'annee_academique_id' => DB::table('semestres')
                ->where('semestres.id', $request->semestre_id)
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
    public function edit(SessionDeCours $sessionDeCour): View|RedirectResponse
    {
        // Récupérer l'année académique de cette session
        $anneeAcademique = null;
        if ($sessionDeCour->semester && $sessionDeCour->semester->anneeAcademique) {
            $anneeAcademique = $sessionDeCour->semester->anneeAcademique;
        }

        // Vérifier si l'utilisateur peut modifier cette session
        $user = Auth::user();
        $peutModifier = true;
        if ($user && $user->roles->first()->code === 'coordinateur') {
            if ($anneeAcademique && $anneeAcademique->date_debut && $anneeAcademique->date_fin) {
                $now = now();
                $dateFin = \Carbon\Carbon::parse($anneeAcademique->date_fin);
                if ($now->gt($dateFin)) {
                    $peutModifier = false;
                }
            }
        }

        // Si l'année est terminée, rediriger vers l'index avec un message
        if (!$peutModifier) {
            return redirect()->route('sessions-de-cours.index')
                ->with('error', 'Vous ne pouvez pas modifier des sessions pour une année académique terminée.');
        }

        $semestres = Semestre::with('anneeAcademique')->get();
        $matieres = Matiere::all();
        $enseignants = Enseignant::all();
        $typesCours = TypeCours::whereIn('nom', ['Présentiel', 'E-learning', 'Workshop'])->get();
        $statutsSession = StatutSession::all();

        // Récupérer les coordinateurs pour les cours Workshop et E-learning
        $coordinateurs = \App\Models\Coordinateur::with('user')->get();

        // Filtrer les classes selon le rôle de l'utilisateur
        if ($user && $user->roles->first()->code === 'coordinateur') {
            $coordinateur = $user->coordinateur;
            if ($coordinateur && $coordinateur->promotion) {
                $classes = $coordinateur->promotion->classes()->orderBy('nom')->get();
            } else {
                $classes = collect();
            }
        } else {
            $classes = Classe::orderBy('nom')->get();
        }

        // Récupérer toutes les années académiques pour le sélecteur
        $anneesAcademiques = DB::table('annees_academiques')->orderBy('nom', 'desc')->get();

        return view('sessions-de-cours.edit', compact(
            'sessionDeCour', 'semestres', 'classes', 'matieres', 'enseignants', 'typesCours',
            'statutsSession', 'coordinateurs', 'anneesAcademiques', 'anneeAcademique', 'peutModifier'
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
            'status_id' => 'required|exists:statuts_session,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Vérifier le statut de l'année académique
        $semestre = Semestre::with('anneeAcademique')->find($request->semestre_id);
        if ($semestre && $semestre->anneeAcademique && $semestre->anneeAcademique->date_debut && $semestre->anneeAcademique->date_fin) {
            $now = now();
            $dateFin = \Carbon\Carbon::parse($semestre->anneeAcademique->date_fin);
            if ($now->gt($dateFin)) {
                $user = Auth::user();
                if ($user && $user->roles->first()->code === 'coordinateur') {
                    return back()->withInput()->withErrors([
                        'semestre_id' => 'Vous ne pouvez pas modifier de sessions pour une année académique terminée.'
                    ]);
                }
            }
        }

        // Vérifier que pour les cours Workshop et E-learning, l'enseignant est un coordinateur
        $typeCours = TypeCours::find($request->type_cours_id);
        if (in_array($typeCours->nom, ['Workshop', 'E-learning'])) {
            $enseignant = Enseignant::find($request->enseignant_id);
            if (!$enseignant || !$enseignant->user || $enseignant->user->roles->first()->code !== 'coordinateur') {
                return back()->withInput()->withErrors([
                    'enseignant_id' => 'Pour les cours de type "' . $typeCours->nom . '", l\'enseignant sélectionné doit obligatoirement être un coordinateur pédagogique.'
                ]);
            }
        }

        // Mettre à jour la session de cours
        $sessionDeCour->update([
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
            'annee_academique_id' => DB::table('semestres')
                ->where('semestres.id', $request->semestre_id)
                ->value('annee_academique_id'),
        ]);

        return redirect()->route('sessions-de-cours.index')
            ->with('success', 'Session de cours mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SessionDeCours $sessionDeCour): mixed
    {
        // Vérifier s'il y a des présences liées
        if ($sessionDeCour->presences()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer cette session car elle contient des présences enregistrées.'
                ]);
            }
            return redirect()->route('sessions-de-cours.index')
                ->with('error', 'Impossible de supprimer cette session car elle contient des présences enregistrées.');
        }

        $sessionDeCour->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Session de cours supprimée avec succès.'
            ]);
        }

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
                'annees_academiques.date_debut as annee_date_debut',
                'annees_academiques.date_fin as annee_date_fin',
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
            return redirect()->route($this->getRouteName('index'))
                ->with('error', 'Session introuvable.');
        }

        // Vérifier les permissions selon le rôle de l'utilisateur
        $user = Auth::user();
        $hasAccess = false;

        if ($user->roles->first()->code === 'admin') {
            // Admin a accès à tout
            $hasAccess = true;
        } elseif ($user->roles->first()->code === 'coordinateur') {
            // Coordinateur : vérifier si la session appartient à sa promotion
            $coordinateur = $user->coordinateur;
            if ($coordinateur && $coordinateur->promotion) {
                $classesIds = $coordinateur->promotion->classes()->pluck('id');
                $hasAccess = in_array($session->classe_id, $classesIds->toArray());
            }
        } elseif ($user->roles->first()->code === 'enseignant') {
            // Enseignant : vérifier si c'est sa session
            $enseignant = $user->enseignant;
            if ($enseignant) {
                $hasAccess = $session->enseignant_id == $enseignant->id;
            }
        }

        if (!$hasAccess) {
            abort(403, 'Accès non autorisé à cette session.');
        }

        // Vérifier si l'année académique est terminée
        if ($session->annee_date_debut && $session->annee_date_fin) {
            $now = now();
            $dateFin = \Carbon\Carbon::parse($session->annee_date_fin);
            if ($now->gt($dateFin)) {
                return redirect()->route('sessions-de-cours.show', $sessionId)
                    ->with('error', 'Cette année académique est terminée. Vous ne pouvez plus faire l\'appel.');
            }
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
            'presences.*.statut_id' => 'required|exists:statuts_presence,id'
        ]);

        // Récupérer les informations de la session
        $session = DB::table('course_sessions')
            ->select('course_sessions.*', 'semestres.annee_academique_id', 'annees_academiques.date_debut', 'annees_academiques.date_fin')
            ->leftJoin('semestres', 'course_sessions.semester_id', '=', 'semestres.id')
            ->leftJoin('annees_academiques', 'semestres.annee_academique_id', '=', 'annees_academiques.id')
            ->where('course_sessions.id', $sessionId)
            ->first();

        if (!$session) {
            return redirect()->route($this->getRouteName('index'))
                ->with('error', 'Session introuvable.');
        }

        // Vérifier les permissions selon le rôle de l'utilisateur
        $user = Auth::user();
        $hasAccess = false;

        if ($user->roles->first()->code === 'admin') {
            // Admin a accès à tout
            $hasAccess = true;
        } elseif ($user->roles->first()->code === 'coordinateur') {
            // Coordinateur : vérifier si la session appartient à sa promotion
            $coordinateur = $user->coordinateur;
            if ($coordinateur && $coordinateur->promotion) {
                $classesIds = $coordinateur->promotion->classes()->pluck('id');
                $hasAccess = in_array($session->classe_id, $classesIds->toArray());
            }
        } elseif ($user->roles->first()->code === 'enseignant') {
            // Enseignant : vérifier si c'est sa session
            $enseignant = $user->enseignant;
            if ($enseignant) {
                $hasAccess = $session->enseignant_id == $enseignant->id;
            }
        }

        if (!$hasAccess) {
            abort(403, 'Accès non autorisé à cette session.');
        }

        // Vérifier si l'année académique est terminée
        if ($session->date_debut && $session->date_fin) {
            $now = now();
            $dateFin = \Carbon\Carbon::parse($session->date_fin);
            if ($now->gt($dateFin)) {
                return redirect()->route('sessions-de-cours.show', $sessionId)
                    ->with('error', 'Cette année académique est terminée. Vous ne pouvez plus enregistrer les présences.');
            }
        }

        // Supprimer les anciennes présences pour cette session
        DB::table('presences')->where('course_session_id', $sessionId)->delete();

        // Enregistrer les nouvelles présences
        foreach ($request->presences as $etudiantId => $data) {
            DB::table('presences')->insert([
                'etudiant_id' => $etudiantId,
                'course_session_id' => $sessionId,
                'statut_presence_id' => $data['statut_id'],
                'enregistre_le' => now(),
                'enregistre_par_user_id' => Auth::id(),
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
                'annees_academiques.nom as annee_nom',
                'annees_academiques.date_debut as annee_date_debut',
                'annees_academiques.date_fin as annee_date_fin',
                'types_cours.nom as type_cours_nom',
                'types_cours.code as type_cours_code'
            )
            ->leftJoin('matieres', 'course_sessions.matiere_id', '=', 'matieres.id')
            ->leftJoin('classes', 'course_sessions.classe_id', '=', 'classes.id')
            ->leftJoin('enseignants', 'course_sessions.enseignant_id', '=', 'enseignants.id')
            ->leftJoin('statuts_session', 'course_sessions.status_id', '=', 'statuts_session.id')
            ->leftJoin('semestres', 'course_sessions.semester_id', '=', 'semestres.id')
            ->leftJoin('annees_academiques', 'semestres.annee_academique_id', '=', 'annees_academiques.id')
            ->leftJoin('types_cours', 'course_sessions.type_cours_id', '=', 'types_cours.id')
            ->where('course_sessions.id', $sessionId)
            ->first();

        if (!$session) {
            return redirect()->route($this->getRouteName('index'))
                ->with('error', 'Session introuvable.');
        }

        // Vérifier les permissions selon le rôle de l'utilisateur
        $user = Auth::user();
        $hasAccess = false;

        if ($user->roles->first()->code === 'admin') {
            // Admin a accès à tout
            $hasAccess = true;
        } elseif ($user->roles->first()->code === 'coordinateur') {
            // Coordinateur : vérifier si la session appartient à sa promotion
            $coordinateur = $user->coordinateur;
            if ($coordinateur && $coordinateur->promotion) {
                $classesIds = $coordinateur->promotion->classes()->pluck('id');
                $hasAccess = in_array($session->classe_id, $classesIds->toArray());
            }
        } elseif ($user->roles->first()->code === 'enseignant') {
            // Enseignant : vérifier si c'est sa session
            $enseignant = $user->enseignant;
            if ($enseignant) {
                $hasAccess = $session->enseignant_id == $enseignant->id;
            }
        }

        if (!$hasAccess) {
            abort(403, 'Accès non autorisé à cette session.');
        }

        // Vérifier si l'année académique est terminée
        $anneeTerminee = false;
        if ($session->annee_date_debut && $session->annee_date_fin) {
            $now = now();
            $dateFin = \Carbon\Carbon::parse($session->annee_date_fin);
            $anneeTerminee = $now->gt($dateFin);
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

        return view('sessions-de-cours.show', compact('session', 'presencesStats', 'anneeTerminee'));
    }

    /**
     * Récupérer les données d'une session en JSON pour l'édition
     */
    public function getSessionJson(SessionDeCours $session)
    {
        try {
            $session->load(['classe', 'matiere', 'enseignant', 'typeCours', 'statutSession']);

            return response()->json([
                'success' => true,
                'session' => $session
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher les sessions historiques (années terminées) en lecture seule.
     */
    public function historique(Request $request): View
    {
        $perPage = $request->get('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50]) ? $perPage : 15;

        // Charger les sessions historiques (années terminées)
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
                'types_cours.nom as type_cours_nom',
                'types_cours.code as type_cours_code'
            )
            ->leftJoin('matieres', 'course_sessions.matiere_id', '=', 'matieres.id')
            ->leftJoin('classes', 'course_sessions.classe_id', '=', 'classes.id')
            ->leftJoin('enseignants', 'course_sessions.enseignant_id', '=', 'enseignants.id')
            ->leftJoin('statuts_session', 'course_sessions.status_id', '=', 'statuts_session.id')
            ->leftJoin('semestres', 'course_sessions.semester_id', '=', 'semestres.id')
            ->leftJoin('annees_academiques', 'semestres.annee_academique_id', '=', 'annees_academiques.id')
            ->leftJoin('types_cours', 'course_sessions.type_cours_id', '=', 'types_cours.id')
            ->where('annees_academiques.statut', 'Terminée')
            ->orderBy('course_sessions.start_time', 'desc');

        // Filtrer selon le rôle de l'utilisateur
        $user = Auth::user();
        if ($user && $user->roles->first()->code === 'coordinateur') {
            // Coordinateur : voir seulement les sessions de sa promotion
            $coordinateur = $user->coordinateur;
            if ($coordinateur && $coordinateur->promotion) {
                $classesIds = $coordinateur->promotion->classes()->pluck('id');
                $sessionsQuery->whereIn('course_sessions.classe_id', $classesIds);
            }
        } elseif ($user && $user->roles->first()->code === 'enseignant') {
            // Enseignant : voir seulement ses sessions
            $enseignant = $user->enseignant;
            if ($enseignant) {
                $sessionsQuery->where('course_sessions.enseignant_id', $enseignant->id);
            }
        }

        // Appliquer les filtres si présents
        if ($request->filled('annee_academique_id')) {
            $sessionsQuery->where('annees_academiques.id', $request->annee_academique_id);
        }

        if ($request->filled('semestre_id')) {
            $sessionsQuery->where('course_sessions.semester_id', $request->semestre_id);
        }

        if ($request->filled('classe_id')) {
            $sessionsQuery->where('course_sessions.classe_id', $request->classe_id);
        }

        if ($request->filled('matiere_id')) {
            $sessionsQuery->where('course_sessions.matiere_id', $request->matiere_id);
        }

        // Paginer les résultats
        $sessions = $sessionsQuery->paginate($perPage)->appends($request->query());

        // Récupérer les données pour les filtres
        $anneesAcademiques = DB::table('annees_academiques')
            ->select('id', 'nom', 'date_debut', 'date_fin')
            ->orderBy('nom', 'desc')
            ->get()
            ->filter(function($annee) {
                // Filtrer seulement les années terminées
                if ($annee->date_debut && $annee->date_fin) {
                    $now = now();
                    $dateFin = \Carbon\Carbon::parse($annee->date_fin);
                    return $now->gt($dateFin);
                }
                return false;
            });

        $semestres = Semestre::with('anneeAcademique')
            ->whereHas('anneeAcademique', function($query) {
                $query->where('date_fin', '<', now());
            })
            ->orderBy('nom')
            ->get();

        $matieres = Matiere::orderBy('nom')->get();

        // Filtrer les classes selon le rôle de l'utilisateur
        if ($user && $user->roles->first()->code === 'coordinateur') {
            $coordinateur = $user->coordinateur;
            if ($coordinateur && $coordinateur->promotion) {
                $classes = $coordinateur->promotion->classes()->orderBy('nom')->get();
            } else {
                $classes = collect();
            }
        } else {
            $classes = Classe::orderBy('nom')->get();
        }

        $typesCours = TypeCours::whereIn('nom', ['Présentiel', 'E-learning', 'Workshop'])->orderBy('nom')->get();

        return view('sessions-de-cours.historique', compact(
            'sessions', 'anneesAcademiques', 'semestres', 'classes', 'matieres', 'typesCours'
        ));
    }

    /**
     * Obtenir la route appropriée selon le rôle de l'utilisateur
     */
    private function getRouteName($route)
    {
        $user = Auth::user();
        if ($user && $user->roles->first()->code === 'enseignant') {
            return 'enseignant.sessions-de-cours.' . $route;
        }
        return 'sessions-de-cours.' . $route;
    }
}
