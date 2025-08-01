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

use Carbon\Carbon;
use App\Traits\DaisyUINotifier;

class SessionDeCoursController extends Controller
{
    use DaisyUINotifier;

    /**
     * Afficher la liste des sessions de cours.
     */
    public function index(Request $request): View
    {
        $perPage = $request->get('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50]) ? $perPage : 15;

        // Charger les sessions avec toutes les informations jointes
        $sessionsQuery = SessionDeCours::with([
            'matiere',
            'classe',
            'enseignant',
            'statutSession',
            'semestre.anneeAcademique',
            'typeCours'
        ])->orderBy('start_time', 'desc');

        // Filtrer selon le rôle de l'utilisateur
        $user = Auth::user();
        if ($user && $user->roles->first()->code === 'coordinateur') {
            // Coordinateur : voir seulement les sessions de sa promotion
            $coordinateur = $user->coordinateur;
            if ($coordinateur && $coordinateur->promotion) {
                try {
                    $classesIds = $coordinateur->promotion->classes()->pluck('id');
                    $sessionsQuery->whereIn('classe_id', $classesIds);
                } catch (\Exception $e) {
                    // En cas d'erreur, ne pas afficher de sessions
                    $sessionsQuery->where('id', 0);
                }
            } else {
                // Si pas de promotion, aucune session
                $sessionsQuery->where('id', 0);
            }
        } elseif ($user && $user->roles->first()->code === 'enseignant') {
            // Enseignant : voir seulement ses sessions
            $enseignant = $user->enseignant;
            if ($enseignant) {
                $sessionsQuery->where('enseignant_id', $enseignant->id);
            } else {
                // Si pas de profil enseignant, aucune session
                $sessionsQuery->where('id', 0);
            }
        }

        // Appliquer les filtres si présents
        if ($request->filled('annee_academique_id')) {
            $sessionsQuery->whereHas('semestre.anneeAcademique', function($query) use ($request) {
                $query->where('id', $request->annee_academique_id);
            });
        }

        if ($request->filled('semestre_id')) {
            $sessionsQuery->where('semester_id', $request->semestre_id);
        }

        if ($request->filled('classe_id')) {
            $sessionsQuery->where('classe_id', $request->classe_id);
        }

        if ($request->filled('matiere_id')) {
            $sessionsQuery->where('matiere_id', $request->matiere_id);
        }

        if ($request->filled('status_id')) {
            $sessionsQuery->where('status_id', $request->status_id);
        }

        // Séparer les sessions récentes et futures
        $sessionsRecentes = (clone $sessionsQuery)->where('start_time', '<=', now())->get();
        $sessionsFutures = (clone $sessionsQuery)->where('start_time', '>', now())->get();

        // Paginer les résultats (sessions récentes par défaut)
        $sessions = $sessionsQuery->where('start_time', '<=', now())->paginate($perPage)->appends($request->query());

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

        return view('sessions-de-cours.index', compact('sessions', 'sessionsRecentes', 'sessionsFutures', 'anneesAcademiques', 'semestres', 'classes', 'matieres', 'typesCours'));
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
        $typesCours = TypeCours::whereIn('nom', ['Présentiel', 'E-learning', 'Workshop'])->get();
        $statutsSession = StatutSession::all();

        // Filtrer les enseignants selon le rôle et le type de cours
        $enseignants = collect();
        $coordinateurs = collect();

        if ($user && $user->roles->first()->code === 'coordinateur') {
            // Coordinateur : voir seulement les enseignants de sa promotion + lui-même
            $coordinateur = $user->coordinateur;
            if ($coordinateur && $coordinateur->promotion) {
                $classesIds = $coordinateur->promotion->classes()->pluck('id');

                // Récupérer les enseignants qui ont des sessions dans ces classes
                $enseignantsIds = SessionDeCours::whereIn('classe_id', $classesIds)
                    ->distinct()
                    ->pluck('enseignant_id');

                $enseignants = Enseignant::whereIn('id', $enseignantsIds)->get();

                // Ajouter le coordinateur lui-même s'il a un profil enseignant
                $coordinateurEnseignant = Enseignant::where('user_id', $coordinateur->user_id)->first();
                if ($coordinateurEnseignant && !$enseignants->contains('id', $coordinateurEnseignant->id)) {
                    $enseignants->push($coordinateurEnseignant);
                }
            }
        } elseif ($user && $user->roles->first()->code === 'enseignant') {
            // Enseignant : voir seulement lui-même
            $enseignant = $user->enseignant;
            if ($enseignant) {
                $enseignants = collect([$enseignant]);
            }
        } else {
            // Admin : voir tous les enseignants
            $enseignants = Enseignant::all();
        }

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

        // Créer la session de cours
        $sessionDeCours = SessionDeCours::create($request->all());

        // Test direct avec Sonner
        return $this->successNotification('Session de cours créée avec succès !', 'sessions-de-cours.index');
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
            // Les coordinateurs ne peuvent pas modifier les sessions en présentiel
            if ($sessionDeCour->typeCours && $sessionDeCour->typeCours->nom === 'Présentiel') {
                return redirect()->route('sessions-de-cours.index')
                    ->with('error', 'Les coordinateurs ne peuvent pas modifier les sessions en présentiel. Seuls les enseignants peuvent les modifier.');
            }

            // Vérifier si l'année académique est terminée
            if ($anneeAcademique && $anneeAcademique->date_debut && $anneeAcademique->date_fin) {
                $now = now();
                $dateFin = \Carbon\Carbon::parse($anneeAcademique->date_fin);
                if ($now->gt($dateFin)) {
                    $peutModifier = false;
                }
            }
        } elseif ($user && $user->roles->first()->code === 'enseignant') {
            // Les enseignants ne peuvent pas modifier leurs sessions en présentiel
            if ($sessionDeCour->typeCours && $sessionDeCour->typeCours->nom === 'Présentiel') {
                return redirect()->route('enseignant.sessions-de-cours.index')
                    ->with('error', 'Les enseignants ne peuvent pas modifier les sessions en présentiel. Vous pouvez seulement visualiser et marquer les présences.');
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

        // Vérifier les permissions selon le rôle de l'utilisateur
        $user = Auth::user();
        if ($user && $user->roles->first()->code === 'coordinateur') {
            // Les coordinateurs ne peuvent pas modifier les sessions en présentiel
            if ($typeCours && $typeCours->nom === 'Présentiel') {
                return redirect()->route('sessions-de-cours.index')
                    ->with('error', 'Les coordinateurs ne peuvent pas modifier les sessions en présentiel. Seuls les enseignants peuvent les modifier.');
            }
        } elseif ($user && $user->roles->first()->code === 'enseignant') {
            // Les enseignants ne peuvent pas modifier leurs sessions en présentiel
            if ($typeCours && $typeCours->nom === 'Présentiel') {
                return redirect()->route('enseignant.sessions-de-cours.index')
                    ->with('error', 'Les enseignants ne peuvent pas modifier les sessions en présentiel. Vous pouvez seulement visualiser et marquer les présences.');
            }
        }

        // Mettre à jour la session de cours
        $sessionDeCour->update($request->all());

        return $this->warningNotification('Session mise à jour avec succès !', $this->getRouteName('index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SessionDeCours $sessionDeCour): mixed
    {
        // Vérifier les permissions selon le rôle de l'utilisateur
        $user = Auth::user();
        if ($user && $user->roles->first()->code === 'enseignant') {
            // Les enseignants ne peuvent pas supprimer leurs sessions en présentiel
            if ($sessionDeCour->typeCours && $sessionDeCour->typeCours->nom === 'Présentiel') {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Les enseignants ne peuvent pas supprimer les sessions en présentiel.'
                    ]);
                }
                return redirect()->route('enseignant.sessions-de-cours.index')
                    ->with('error', 'Les enseignants ne peuvent pas supprimer les sessions en présentiel. Vous pouvez seulement visualiser et marquer les présences.');
            }
        }

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

        // Supprimer la session de cours
        $sessionDeCour->delete();

        return $this->errorNotification('Session supprimée avec succès !', $this->getRouteName('index'));
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

        return $this->warningNotification('Session reportée avec succès !', 'sessions-de-cours.index');
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
            $hasAccess = true;
        } elseif ($user->roles->first()->code === 'coordinateur') {
            $coordinateur = $user->coordinateur;
            if ($coordinateur && $coordinateur->promotion) {
                $classesIds = $coordinateur->promotion->classes()->pluck('id');
                $hasAccess = in_array($session->classe_id, $classesIds->toArray());
            }
        } elseif ($user->roles->first()->code === 'enseignant') {
            // Enseignant : vérifier si c'est sa session (pour tous types de cours)
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
                return redirect()->route($this->getRouteName('show'), $sessionId)
                    ->with('error', 'Cette année académique est terminée. Vous ne pouvez plus faire l\'appel.');
            }
        }

        // Vérifier la fenêtre de modification de 2 semaines pour les enseignants
        $user = Auth::user();
        if ($user->roles->first()->code === 'enseignant') {
            $sessionDate = \Carbon\Carbon::parse($session->start_time);
            $now = now();
            $deuxSemainesApres = $sessionDate->copy()->addWeeks(2);

            if ($now->gt($deuxSemainesApres)) {
                return redirect()->route($this->getRouteName('show'), $sessionId)
                    ->with('error', 'Vous ne pouvez plus faire l\'appel après 2 semaines. La session a eu lieu le ' . $sessionDate->format('d/m/Y') . '.');
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
            // Enseignant : vérifier si c'est sa session (pour tous types de cours)
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
                return redirect()->route($this->getRouteName('show'), $sessionId)
                    ->with('error', 'Cette année académique est terminée. Vous ne pouvez plus enregistrer les présences.');
            }
        }

        // Vérifier la fenêtre de modification de 2 semaines pour les enseignants
        if ($user->roles->first()->code === 'enseignant') {
            $sessionDate = \Carbon\Carbon::parse($session->start_time);
            $now = now();
            $deuxSemainesApres = $sessionDate->copy()->addWeeks(2);

            if ($now->gt($deuxSemainesApres)) {
                return redirect()->route($this->getRouteName('show'), $sessionId)
                    ->with('error', 'Vous ne pouvez plus modifier les présences après 2 semaines. La session a eu lieu le ' . $sessionDate->format('d/m/Y') . '.');
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

        return $this->successNotification('Présences enregistrées avec succès !', $this->getRouteName('show'), [$sessionId]);
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
            // Enseignant : vérifier si c'est sa session (pour tous types de cours)
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

        // Charger toutes les sessions (récentes et futures)
        $sessionsQuery = SessionDeCours::with([
            'matiere',
            'classe',
            'enseignant',
            'statutSession',
            'semestre.anneeAcademique',
            'typeCours'
        ])->orderBy('start_time', 'desc');

        // Filtrer selon le rôle de l'utilisateur
        $user = Auth::user();
        if ($user && $user->roles->first()->code === 'coordinateur') {
            // Coordinateur : voir seulement les sessions de sa promotion
            $coordinateur = $user->coordinateur;
            if ($coordinateur && $coordinateur->promotion) {
                try {
                    $classesIds = $coordinateur->promotion->classes()->pluck('id');
                    $sessionsQuery->whereIn('classe_id', $classesIds);
                } catch (\Exception $e) {
                    // En cas d'erreur, ne pas afficher de sessions
                    $sessionsQuery->where('id', 0);
                }
            } else {
                // Si pas de promotion, aucune session
                $sessionsQuery->where('id', 0);
            }
        } elseif ($user && $user->roles->first()->code === 'enseignant') {
            // Enseignant : voir seulement ses sessions
            $enseignant = $user->enseignant;
            if ($enseignant) {
                $sessionsQuery->where('enseignant_id', $enseignant->id);
            } else {
                // Si pas de profil enseignant, aucune session
                $sessionsQuery->where('id', 0);
            }
        }

        // Appliquer les filtres si présents
        if ($request->filled('annee_academique_id')) {
            $sessionsQuery->whereHas('semestre.anneeAcademique', function($query) use ($request) {
                $query->where('id', $request->annee_academique_id);
            });
        }

        if ($request->filled('semestre_id')) {
            $sessionsQuery->where('semester_id', $request->semestre_id);
        }

        if ($request->filled('classe_id')) {
            $sessionsQuery->where('classe_id', $request->classe_id);
        }

        if ($request->filled('matiere_id')) {
            $sessionsQuery->where('matiere_id', $request->matiere_id);
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

    /**
     * Afficher les cours pour l'étudiant
     */
    public function etudiant(Request $request): View
    {
        $user = Auth::user();
        $etudiant = $user->etudiant;

        if (!$etudiant) {
            abort(404, 'Profil étudiant non trouvé');
        }

        // Récupérer les sessions de cours de l'étudiant
        $sessions = SessionDeCours::with(['matiere', 'enseignant', 'classe', 'typeCours', 'statutSession'])
            ->where('classe_id', $etudiant->classe_id)
            ->where('annee_academique_id', $etudiant->classe->promotion->annee_academique_id)
            ->orderBy('start_time', 'desc')
            ->paginate(15);

        // Récupérer les données pour les filtres
        $matieres = Matiere::orderBy('nom')->get();
        $typesCours = TypeCours::orderBy('nom')->get();
        $statutsSession = StatutSession::orderBy('nom')->get();

        return view('sessions-de-cours.etudiant', compact('sessions', 'matieres', 'typesCours', 'statutsSession'));
    }
}
