<?php

namespace App\Http\Controllers;

use App\Models\Coordinateur;
use App\Models\Classe;
use App\Models\AnneeAcademique;
use App\Models\SessionDeCours;
use App\Models\Presence;
use App\Models\StatutPresence;
use App\Models\Etudiant;
use App\Models\Enseignant;
use App\Models\Matiere;
use App\Models\TypeCours;
use App\Models\StatutSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Rules\ValidEmailDomain;
use Exception;

use function Laravel\Prompts\error;

class CoordinateurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Coordinateur::with(['promotion', 'user']);

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
            'email' => ['required', 'email', 'unique:users,email', new ValidEmailDomain],
            'password' => 'required|string|min:8|confirmed',
            'promotion_id' => 'required|exists:promotions,id',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Vérifier unicité de la promotion
        if (\App\Models\Coordinateur::where('promotion_id', $request->promotion_id)->exists()) {
            return back()->withInput()->withErrors(['promotion_id' => 'Cette promotion est déjà attribuée à un autre coordinateur.']);
        }

        // Créer l'utilisateur
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Attacher le rôle coordinateur
        $roleCoordinateur = \App\Models\Role::where('nom', 'Coordinateur')->first();
        if ($roleCoordinateur) {
            $user->roles()->attach($roleCoordinateur->id);
        }

        $coordinateur = Coordinateur::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'promotion_id' => $request->promotion_id,
            'est_actif' => true,
        ]);

        // Gérer l'upload de photo si fournie
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('coordinateurs', 'public');
            $coordinateur->update(['photo' => $photoPath]);
            // Synchroniser avec l'utilisateur
            $user->update(['photo' => $photoPath]);
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
            'email' => ['required', 'email', 'unique:users,email,' . $coordinateur->user_id, new ValidEmailDomain],
            'password' => 'nullable|string|min:8|confirmed',
            'promotion_id' => 'required|exists:promotions,id',
            'photo' => 'nullable|image|max:2048',
        ]);
        // Vérifier unicité de la promotion (hors coordinateur actuel)
        if (\App\Models\Coordinateur::where('promotion_id', $request->promotion_id)->where('coordinateurs.id', '!=', $coordinateur->id)->exists()) {
            return back()->withInput()->withErrors(['promotion_id' => 'Cette promotion est déjà attribuée à un autre coordinateur.']);
        }

        // Mettre à jour l'utilisateur
        $user = $coordinateur->user;
        $user->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
        ]);

        // Mettre à jour le mot de passe si fourni
        if ($request->filled('password')) {
            $user->update(['password' => bcrypt($request->password)]);
        }

        $data = [
            'prenom' => $request->prenom,
            'nom' => $request->nom,
            'promotion_id' => $request->promotion_id,
            'email' => $user->email,
        ];

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('coordinateurs', 'public');
            $data['photo'] = $photoPath;
            // Synchroniser avec l'utilisateur
            $user->update(['photo' => $photoPath]);
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
     * Dashboard coordinateur avec graphes dynamiques et sélection d'année
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $coordinateur = $user->coordinateur;

        if (!$coordinateur || !$coordinateur->promotion) {
            return view('dashboard.coordinateur', [
                'coordinateur' => $coordinateur,
                'promotion' => null,
                'classes' => collect(),
                'stats' => [],
                'anneesAcademiques' => AnneeAcademique::orderBy('date_debut', 'desc')->get(),
                'anneeActive' => null
            ]);
        }

        // Sélection d'année académique
        $anneeId = $request->input('annee_id');
        $anneesAcademiques = AnneeAcademique::orderBy('date_debut', 'desc')->get();

        if ($anneeId) {
            $anneeActive = AnneeAcademique::find($anneeId);
        } else {
            $anneeActive = AnneeAcademique::getActive();
        }

        if (!$anneeActive) {
            $anneeActive = $anneesAcademiques->first();
        }

        $promotion = $coordinateur->promotion;
        $classes = Classe::where('promotion_id', $promotion->id)->get();

        // Statistiques pour l'année sélectionnée
        $stats = $this->getStats($classes, $anneeActive);

        // Période sélectionnée (par défaut : tout)
        $periodeDebut = $request->input('debut');
        $periodeFin = $request->input('fin');
        $classeId = $request->input('classe_id');

        // 1. Taux de présence par étudiant (pour une classe et une période)
        $etudiants = \App\Models\Etudiant::query();
        if ($classeId) {
            $etudiants->where('classe_id', $classeId);
        } else {
            $etudiants->whereIn('classe_id', $classes->pluck('id'));
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
        $semestres = \App\Models\Semestre::where('annee_academique_id', $anneeActive->id)->orderBy('date_debut')->get();
        foreach ($semestres as $semestre) {
            $sessions = \App\Models\SessionDeCours::where('semester_id', $semestre->id);
            if ($classeId) {
                $sessions->where('classe_id', $classeId);
            } else {
                $sessions->whereIn('classe_id', $classes->pluck('id'));
            }
        if ($periodeDebut) $sessions->where('start_time', '>=', $periodeDebut);
        if ($periodeFin) $sessions->where('end_time', '<=', $periodeFin);
            $volumeCumule[] = [
                'periode' => $semestre->nom,
                'nb' => $sessions->count(),
            ];
        }

        return view('dashboard.coordinateur', compact(
            'coordinateur',
            'promotion',
            'classes',
            'stats',
            'presenceParEtudiant',
            'presenceParClasse',
            'volumeParType',
            'volumeCumule',
            'classeId',
            'periodeDebut',
            'periodeFin',
            'anneesAcademiques',
            'anneeActive'
        ));
    }

    /**
     * Obtenir les statistiques pour le dashboard
     */
    private function getStats($classes, $anneeActive)
    {
        $totalEtudiants = 0;
        $totalSessions = 0;
        $totalPresences = 0;
        $totalPresents = 0;
        $totalPourcentagePresence = 0;
        $presencesAvecPourcentage = 0;

        foreach ($classes as $classe) {
            $totalEtudiants += $classe->etudiants->count();

            $sessions = SessionDeCours::where('classe_id', $classe->id)
                ->where('annee_academique_id', $anneeActive->id);
            $totalSessions += $sessions->count();

            foreach ($classe->etudiants as $etudiant) {
                $presences = $etudiant->presences()
                    ->whereHas('sessionDeCours', function($q) use ($anneeActive) {
                        $q->where('annee_academique_id', $anneeActive->id);
                    });
                $totalPresences += $presences->count();

                // Compter les présences basées sur le statut
                $totalPresents += $presences->whereHas('statutPresence', function($q) {
                    $q->where('nom', 'Présent');
                })->count();


            }
        }

        // Calculer le taux de présence basé sur le statut
        $tauxPresenceStatut = $totalPresences > 0 ? round(($totalPresents / $totalPresences) * 100, 1) : 0;

        // Compter les justifications en attente
        $justificationsEnAttente = \App\Models\JustificationAbsence::where('statut', 'En attente')
            ->whereHas('presence.etudiant.classe', function($query) use ($classes) {
                $query->whereIn('id', $classes->pluck('id'));
            })->count();

        return [
            'total_etudiants' => $totalEtudiants,
            'total_classes' => $classes->count(),
            'total_sessions' => $totalSessions,
            'taux_presence_statut' => $tauxPresenceStatut,
            'total_presences' => $totalPresences,
            'justifications_en_attente' => $justificationsEnAttente
        ];
    }

    /**
     * Gestion des emplois du temps - Vue principale
     */
    public function emploisDuTemps(Request $request)
    {
        $user = Auth::user();
        $coordinateur = $user->coordinateur;

        if (!$coordinateur || !$coordinateur->promotion) {
            return redirect()->back()->with('error', 'Aucune promotion assignée.');
        }

        $anneeId = $request->input('annee_id');
        $anneesAcademiques = AnneeAcademique::orderBy('date_debut', 'desc')->get();

        if ($anneeId) {
            $anneeActive = AnneeAcademique::find($anneeId);
        } else {
            $anneeActive = AnneeAcademique::getActive();
        }

        if (!$anneeActive) {
            $anneeActive = $anneesAcademiques->first();
        }

        $classes = Classe::where('promotion_id', $coordinateur->promotion_id)->get();
        $enseignants = Enseignant::all();
        $matieres = Matiere::all();
        $typesCours = TypeCours::all();
        $statutsSession = StatutSession::all();

        // Récupérer les sessions pour les classes de la promotion du coordinateur
        $sessions = SessionDeCours::with(['classe', 'matiere', 'enseignant', 'typeCours', 'statutSession'])
            ->whereIn('classe_id', $classes->pluck('id'))
            ->where('annee_academique_id', $anneeActive->id)
            ->orderBy('start_time')
            ->get();

        return view('emplois-du-temps.coordinateur', compact(
            'classes',
            'enseignants',
            'matieres',
            'typesCours',
            'statutsSession',
            'sessions',
            'anneeActive',
            'anneesAcademiques'
        ));
    }

    /**
     * Créer une nouvelle session de cours
     */
    public function creerSession(Request $request)
    {
        $user = Auth::user();
        $coordinateur = $user->coordinateur;

        if (!$coordinateur || !$coordinateur->promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune promotion assignée.'
            ]);
        }

        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'type_cours_id' => 'required|exists:types_cours,id',
            'status_id' => 'required|exists:statuts_session,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

                // Vérifier le type de cours
        $typeCours = TypeCours::find($request->type_cours_id);

        // Pour Workshop et E-learning, forcer l'enseignant à être le coordinateur
        if (in_array($typeCours->nom, ['Workshop', 'E-learning'])) {
            // Vérifier si le coordinateur a un enseignant associé
            $enseignantCoordinateur = Enseignant::where('user_id', $coordinateur->user_id)->first();

            if (!$enseignantCoordinateur) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous devez avoir un profil enseignant pour créer des sessions Workshop et E-learning.'
                ]);
            }

            // Forcer automatiquement l'enseignant à être le coordinateur
            $request->merge(['enseignant_id' => $enseignantCoordinateur->id]);
        }

        $anneeActive = AnneeAcademique::find($request->input('annee_id'));
        if (!$anneeActive) {
            return response()->json([
                'success' => false,
                'message' => 'Année académique non trouvée.'
            ]);
        }

        // Vérifier les conflits d'horaire pour la classe
        $conflitClasse = SessionDeCours::where('classe_id', $request->classe_id)
            ->where('annee_academique_id', $anneeActive->id)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                          ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->exists();

        if ($conflitClasse) {
            return response()->json([
                'success' => false,
                'message' => 'Il y a un conflit d\'horaire pour cette classe.'
            ]);
        }

        // Vérifier les conflits d'horaire pour l'enseignant
        $conflitEnseignant = SessionDeCours::where('enseignant_id', $request->enseignant_id)
            ->where('annee_academique_id', $anneeActive->id)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                          ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->exists();

        if ($conflitEnseignant) {
            return response()->json([
                'success' => false,
                'message' => 'Il y a un conflit d\'horaire pour cet enseignant.'
            ]);
        }

        $semestre = $anneeActive->semestres()->where('actif', true)->first();
        if (!$semestre) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun semestre actif trouvé pour cette année académique.'
            ]);
        }

        $session = SessionDeCours::create([
            'classe_id' => $request->classe_id,
            'matiere_id' => $request->matiere_id,
            'enseignant_id' => $request->enseignant_id,
            'type_cours_id' => $request->type_cours_id,
            'status_id' => $request->status_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'location' => $request->location,
            'notes' => $request->notes,
            'annee_academique_id' => $anneeActive->id,
            'semester_id' => $semestre->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Session de cours créée avec succès.',
            'session' => $session->load(['classe', 'matiere', 'enseignant', 'typeCours', 'statutSession'])
        ]);
    }

    /**
     * Prise de présence pour une session de cours
     */
    public function prisePresence(Request $request, SessionDeCours $session)
    {
        $user = Auth::user();
        $coordinateur = $user->coordinateur;

        if (!$coordinateur || !$coordinateur->promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune promotion assignée.'
            ]);
        }

        // Vérifier que le type de cours est Workshop ou E-learning
        $typesAutorises = TypeCours::whereIn('nom', ['Workshop', 'E-learning'])->pluck('id');
        if (!in_array($session->type_cours_id, $typesAutorises->toArray())) {
            return response()->json([
                'success' => false,
                'message' => 'La prise de présence n\'est autorisée que pour les cours Workshop et E-learning.'
            ]);
        }

        // Vérifier que la session appartient à la promotion du coordinateur
        $classes = Classe::where('promotion_id', $coordinateur->promotion_id)->pluck('id');
        if (!in_array($session->classe_id, $classes->toArray())) {
            return response()->json([
                'success' => false,
                'message' => 'Cette session n\'appartient pas à votre promotion.'
            ]);
        }

        $request->validate([
            'presences' => 'required|array',
            'presences.*.etudiant_id' => 'required|exists:etudiants,id',
            'presences.*.statut_presence_id' => 'required|exists:statuts_presence,id'
        ]);

        try {
            foreach ($request->presences as $etudiantId => $presenceData) {
                // Vérifier si une présence existe déjà pour cet étudiant et cette session
                $presence = Presence::where('etudiant_id', $etudiantId)
                    ->where('course_session_id', $session->id)
                    ->first();

                if ($presence) {
                    // Mettre à jour la présence existante
                    $presence->update([
                        'statut_presence_id' => $presenceData['statut_presence_id'],
                        'enregistre_le' => now(),
                        'enregistre_par_user_id' => Auth::id()
                    ]);
                } else {
                    // Créer une nouvelle présence
                    Presence::create([
                        'etudiant_id' => $etudiantId,
                        'course_session_id' => $session->id,
                        'statut_presence_id' => $presenceData['statut_presence_id'],
                        'enregistre_le' => now(),
                        'enregistre_par_user_id' => Auth::id()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Présences enregistrées avec succès.'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement des présences: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Modifier une session de cours
     */
        public function modifierSession(Request $request, SessionDeCours $session)
    {
        try {
            $user = Auth::user();
            $coordinateur = $user->coordinateur;

            if (!$coordinateur || !$coordinateur->promotion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune promotion assignée.'
                ]);
            }
            $request->validate([
                'classe_id' => 'required|exists:classes,id',
                'matiere_id' => 'required|exists:matieres,id',
                'enseignant_id' => 'required|exists:enseignants,id',
                'type_cours_id' => 'required|exists:types_cours,id',
                'status_id' => 'required|exists:statuts_session,id',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'location' => 'nullable|string|max:255',
                'notes' => 'nullable|string'
            ]);

        // Vérifier le type de cours
        $typeCours = TypeCours::find($request->type_cours_id);

        // Pour Workshop et E-learning, forcer l'enseignant à être le coordinateur
        if (in_array($typeCours->nom, ['Workshop', 'E-learning'])) {
            // Vérifier si le coordinateur a un enseignant associé
            $enseignantCoordinateur = Enseignant::where('user_id', $coordinateur->user_id)->first();

            if (!$enseignantCoordinateur) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous devez avoir un profil enseignant pour modifier des sessions Workshop et E-learning.'
                ]);
            }

            // Forcer automatiquement l'enseignant à être le coordinateur
            $request->merge(['enseignant_id' => $enseignantCoordinateur->id]);
        }

        $anneeActive = AnneeAcademique::find($request->input('annee_id'));
        if (!$anneeActive) {
            return response()->json([
                'success' => false,
                'message' => 'Année académique non trouvée.'
            ]);
        }

        // Vérifier les conflits d'horaire pour la classe (exclure la session actuelle)
        $conflitClasse = SessionDeCours::where('classe_id', $request->classe_id)
            ->where('annee_academique_id', $anneeActive->id)
            ->where('course_sessions.id', '!=', $session->id)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                          ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->exists();

        if ($conflitClasse) {
            return response()->json([
                'success' => false,
                'message' => 'Il y a un conflit d\'horaire pour cette classe.'
            ]);
        }

        // Vérifier les conflits d'horaire pour l'enseignant (exclure la session actuelle)
        $conflitEnseignant = SessionDeCours::where('enseignant_id', $request->enseignant_id)
            ->where('annee_academique_id', $anneeActive->id)
            ->where('course_sessions.id', '!=', $session->id)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                          ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->exists();

        if ($conflitEnseignant) {
            return response()->json([
                'success' => false,
                'message' => 'Il y a un conflit d\'horaire pour cet enseignant.'
            ]);
        }

        try {
            $semestre = $anneeActive->semestres()->where('actif', true)->first();
            if (!$semestre) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun semestre actif trouvé pour cette année académique.'
                ]);
            }

            $session->update([
                'classe_id' => $request->classe_id,
                'matiere_id' => $request->matiere_id,
                'enseignant_id' => $request->enseignant_id,
                'type_cours_id' => $request->type_cours_id,
                'status_id' => $request->status_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'location' => $request->location,
                'notes' => $request->notes,
                'annee_academique_id' => $anneeActive->id,
                'semester_id' => $semestre->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Session modifiée avec succès.',
                'session' => $session->load(['classe', 'matiere', 'enseignant', 'typeCours', 'statutSession'])
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification de la session: ' . $e->getMessage()
            ], 500);
        }
        } catch (Exception $e) {
            error('Erreur dans modifierSession: ' . $e->getMessage());
            error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Erreur générale lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les étudiants de toutes les classes de la promotion pour la prise de présence (Workshop et E-learning uniquement)
     */
    public function getEtudiantsClasse(SessionDeCours $session)
    {
        $user = Auth::user();
        $coordinateur = $user->coordinateur;

        if (!$coordinateur || !$coordinateur->promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune promotion assignée.'
            ]);
        }

        // Vérifier que le type de cours est Workshop ou E-learning
        $typesAutorises = TypeCours::whereIn('nom', ['Workshop', 'E-learning'])->pluck('id');
        if (!in_array($session->type_cours_id, $typesAutorises->toArray())) {
            return response()->json([
                'success' => false,
                'message' => 'La prise de présence n\'est autorisée que pour les cours Workshop et E-learning.'
            ]);
        }

        // Vérifier que la session appartient à la promotion du coordinateur
        $classes = Classe::where('promotion_id', $coordinateur->promotion_id)->pluck('id');
        if (!in_array($session->classe_id, $classes->toArray())) {
            return response()->json([
                'success' => false,
                'message' => 'Cette session n\'appartient pas à votre promotion.'
            ]);
        }

        // Récupérer tous les étudiants de toutes les classes de la promotion
        $etudiants = Etudiant::whereIn('classe_id', $classes)
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        $statutsPresence = StatutPresence::all();
        $presences = $session->presences()->with(['etudiant', 'statutPresence'])->get();

        return response()->json([
            'success' => true,
            'etudiants' => $etudiants,
            'statuts_presence' => $statutsPresence,
            'presences' => $presences,
            'session' => $session->load(['classe', 'matiere', 'enseignant', 'typeCours'])
        ]);
    }

    /**
     * Obtenir les présences existantes pour une session
     */
    public function getPresencesSession(SessionDeCours $session)
    {
        $presences = $session->presences()->with(['etudiant', 'statutPresence'])->get();

        return response()->json([
            'success' => true,
            'presences' => $presences
        ]);
    }

    /**
     * Obtenir les sessions Workshop et E-learning pour la prise de présence
     */
    public function getSessionsPresentiel(Request $request)
    {
        $user = Auth::user();
        $coordinateur = $user->coordinateur;

        if (!$coordinateur || !$coordinateur->promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune promotion assignée.'
            ]);
        }

        $anneeId = $request->input('annee_id');
        $anneeActive = $anneeId ? AnneeAcademique::find($anneeId) : AnneeAcademique::getActive();

        if (!$anneeActive) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune année académique active.'
            ]);
        }

        $classes = Classe::where('promotion_id', $coordinateur->promotion_id)->get();
        $typesCours = TypeCours::whereIn('nom', ['Workshop', 'E-learning'])->get();

        $sessions = SessionDeCours::with(['classe', 'matiere', 'enseignant'])
            ->whereIn('classe_id', $classes->pluck('id'))
            ->where('annee_academique_id', $anneeActive->id)
            ->whereIn('type_cours_id', $typesCours->pluck('id'))
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'sessions' => $sessions
        ]);
    }
}
