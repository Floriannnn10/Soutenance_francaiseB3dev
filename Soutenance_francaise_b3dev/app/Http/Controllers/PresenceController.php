<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\SessionDeCours;
use App\Models\StatutPresence;
use App\Traits\DaisyUINotifier;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PresenceController extends Controller
{
    use DaisyUINotifier;

    /**
     * Afficher la liste des présences
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 15);

        // Construire la requête de base
        $query = Presence::with(['etudiant', 'sessionDeCours.classe', 'sessionDeCours.matiere', 'statutPresence']);

        // Filtrer selon le rôle de l'utilisateur
        if ($user->roles->first()->code === 'enseignant') {
            // Les enseignants ne voient que les présences de leurs sessions
            $query->whereHas('sessionDeCours', function ($q) use ($user) {
                $q->where('enseignant_id', $user->enseignant->id);
            });
        } elseif ($user->roles->first()->code === 'coordinateur') {
            // Les coordinateurs voient les présences de toutes les sessions de leur promotion
            $coordinateur = $user->coordinateur;
            if ($coordinateur && $coordinateur->promotion) {
                $classesIds = $coordinateur->promotion->classes()->pluck('id');
                $query->whereHas('sessionDeCours', function ($q) use ($classesIds) {
                    $q->whereIn('classe_id', $classesIds);
                });
            } else {
                // Si pas de promotion, ne voir aucune présence
                $query->where('id', 0); // Condition impossible pour ne rien retourner
            }
        } elseif ($user->roles->first()->code === 'etudiant') {
            // Les étudiants ne voient que leurs propres présences
            $query->where('etudiant_id', $user->etudiant->id);
        } elseif ($user->roles->first()->code === 'parent') {
            // Les parents voient les présences de leurs enfants
            $query->whereHas('etudiant.parents', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        // Appliquer les filtres si présents
        if ($request->filled('session_id')) {
            $query->where('course_session_id', $request->session_id);
        }

        if ($request->filled('etudiant_id')) {
            $query->where('etudiant_id', $request->etudiant_id);
        }

        if ($request->filled('classe_id')) {
            $query->whereHas('sessionDeCours', function ($q) use ($request) {
                $q->where('classe_id', $request->classe_id);
            });
        }

        if ($request->filled('statut_id')) {
            $query->where('statut_presence_id', $request->statut_id);
        }

        // Validation des dates
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            if ($request->date_debut > $request->date_fin) {
                return redirect()->back()->withErrors(['date_fin' => 'La date de fin ne peut pas être antérieure à la date de début.']);
            }
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('enregistre_le', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('enregistre_le', '<=', $request->date_fin);
        }

        // Trier par date d'enregistrement (plus récent en premier)
        $presences = $query->orderBy('enregistre_le', 'desc')->paginate($perPage);

        // Récupérer les classes pour le filtre
        $classes = \App\Models\Classe::orderBy('nom')->get();

        // Filtrer les classes selon le rôle de l'utilisateur
        if ($user->roles->first()->code === 'coordinateur') {
            $coordinateur = $user->coordinateur;
            if ($coordinateur && $coordinateur->promotion) {
                $classes = $coordinateur->promotion->classes()->orderBy('nom')->get();
            } else {
                $classes = collect(); // Aucune classe si pas de promotion
            }
        } elseif ($user->roles->first()->code === 'enseignant') {
            // Les enseignants voient seulement les classes de leurs sessions
            $enseignant = $user->enseignant;
            if ($enseignant) {
                $classes = \App\Models\Classe::whereHas('sessionsDeCours', function ($q) use ($enseignant) {
                    $q->where('enseignant_id', $enseignant->id);
                })->orderBy('nom')->get();
            } else {
                $classes = collect(); // Aucune classe si pas de profil enseignant
            }
        }

        // Récupérer les statuts de présence pour le filtre
        $statutsPresence = StatutPresence::orderBy('nom')->get();

        return view('presences.index', compact('presences', 'classes', 'statutsPresence'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:course_sessions,id',
            'presences' => 'required|array',
            'presences.*.etudiant_id' => 'required|exists:etudiants,id',
            'presences.*.statut_id' => 'required|exists:statuts_presence,id'
        ]);

        $session = SessionDeCours::findOrFail($request->session_id);
        $user = Auth::user();

        // Vérifier que l'utilisateur est autorisé à faire l'appel
        if ($user->roles->first()->code === 'enseignant' && $session->enseignant_id !== $user->enseignant->id) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à faire l\'appel pour cette session.');
        }

        if ($user->roles->first()->code === 'coordinateur' && !in_array($session->typeCours->code, ['workshop', 'e_learning'])) {
            return redirect()->back()->with('error', 'Les coordinateurs ne peuvent faire l\'appel que pour les workshops et les cours en e-learning.');
        }

        // Vérifier que la session n'est pas terminée
        if ($session->end_time < Carbon::now()) {
            return redirect()->back()->with('error', 'La session est terminée, vous ne pouvez plus faire l\'appel.');
        }

        foreach ($request->presences as $presenceData) {
            Presence::updateOrCreate(
                [
                    'etudiant_id' => $presenceData['etudiant_id'],
                    'course_session_id' => $session->id
                ],
                [
                    'statut_presence_id' => $presenceData['statut_id'],
                    'enregistre_par_user_id' => $user->id,
                    'enregistre_le' => Carbon::now()
                ]
            );
        }

        return $this->successNotification('Présences enregistrées avec succès !');
    }

    public function update(Request $request, Presence $presence)
    {
        $request->validate([
            'statut_id' => 'required|exists:statuts_presence,id'
        ]);

        $user = Auth::user();

        // Vérifier que l'utilisateur est autorisé à modifier la présence
        if ($user->roles->first()->code === 'enseignant') {
            // Les enseignants peuvent modifier les présences de leurs sessions
            if ($presence->sessionDeCours->enseignant_id !== $user->enseignant->id) {
                return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à modifier cette présence.');
            }

            // Vérifier la fenêtre de modification de 2 semaines pour les enseignants
            $sessionDate = \Carbon\Carbon::parse($presence->sessionDeCours->start_time);
            $now = now();
            $deuxSemainesApres = $sessionDate->copy()->addWeeks(2);

            if ($now->gt($deuxSemainesApres)) {
                return redirect()->back()->with('error', 'Vous ne pouvez plus modifier les présences après 2 semaines. La session a eu lieu le ' . $sessionDate->format('d/m/Y') . '.');
            }
        } elseif ($user->roles->first()->code === 'coordinateur') {
            // Les coordinateurs ne peuvent modifier que les présences des workshops et e-learning
            if (!in_array($presence->sessionDeCours->typeCours->code, ['workshop', 'e_learning'])) {
                return redirect()->back()->with('error', 'Les coordinateurs ne peuvent modifier que les présences des workshops et e-learning.');
            }
        }

        $presence->update([
            'statut_presence_id' => $request->statut_id,
            'enregistre_par_user_id' => $user->id,
            'enregistre_le' => Carbon::now()
        ]);

        return $this->successNotification('Présence mise à jour avec succès !');
    }

    /**
     * Affiche les présences pour les parents
     */
    public function parent()
    {
        $user = Auth::user();
        $parent = $user->parent;

        if (!$parent) {
            abort(404, 'Parent non trouvé');
        }

        $enfants = $parent->etudiants()->with(['classe', 'presences.sessionDeCours.matiere'])->get();

        return view('presences.parent', compact('enfants', 'parent'));
    }

    /**
     * Affiche les présences des enfants du parent connecté
     */
    public function presencesEnfants()
    {
        $user = Auth::user();
        $parent = $user->parent;

        if (!$parent) {
            abort(404, 'Parent non trouvé');
        }

        $enfants = $parent->etudiants()->with(['classe', 'presences.sessionDeCours.matiere'])->get();

        return view('presences.enfants', compact('enfants', 'parent'));
    }

    /**
     * Affiche les présences pour l'étudiant connecté
     */
    public function etudiant(Request $request)
    {
        $user = Auth::user();
        $etudiant = $user->etudiant;

        if (!$etudiant) {
            abort(404, 'Profil étudiant non trouvé');
        }

        $perPage = $request->get('per_page', 15);

        // Récupérer les présences de l'étudiant
        $presences = Presence::with(['sessionDeCours.matiere', 'sessionDeCours.enseignant', 'sessionDeCours.classe', 'statutPresence'])
            ->where('etudiant_id', $etudiant->id)
            ->orderBy('enregistre_le', 'desc')
            ->paginate($perPage);

        // Récupérer les données pour les filtres
        $matieres = \App\Models\Matiere::orderBy('nom')->get();
        $statutsPresence = StatutPresence::orderBy('nom')->get();

        return view('presences.etudiant', compact('presences', 'etudiant', 'matieres', 'statutsPresence'));
    }
}
