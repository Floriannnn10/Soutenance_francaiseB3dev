<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\SessionDeCours;
use App\Models\StatutPresence;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PresenceController extends Controller
{
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
            // Les coordinateurs voient les présences des sessions Workshop et E-learning
            $query->whereHas('sessionDeCours.typeCours', function ($q) {
                $q->whereIn('code', ['workshop', 'e_learning']);
            });
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
        }

        return view('presences.index', compact('presences', 'classes'));
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

        return redirect()->back()->with('success', 'Les présences ont été enregistrées avec succès.');
    }

    public function update(Request $request, Presence $presence)
    {
        $request->validate([
            'statut_id' => 'required|exists:statuts_presence,id'
        ]);

        $user = Auth::user();

        // Vérifier que l'utilisateur est autorisé à modifier la présence
        if ($user->roles->first()->code === 'enseignant' && $presence->sessionDeCours->enseignant_id !== $user->enseignant->id) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à modifier cette présence.');
        }

        if ($user->roles->first()->code === 'coordinateur' && !in_array($presence->sessionDeCours->typeCours->code, ['workshop', 'e_learning'])) {
            return redirect()->back()->with('error', 'Les coordinateurs ne peuvent modifier les présences que pour les workshops et les cours en e-learning.');
        }

        // Vérifier que la modification est faite dans les 2 semaines suivant la session
        if (Carbon::parse($presence->enregistre_le)->diffInDays(Carbon::now()) > 14) {
            return redirect()->back()->with('error', 'Les présences ne peuvent être modifiées que dans les 2 semaines suivant la session.');
        }

        $presence->update([
            'statut_presence_id' => $request->statut_id,
            'enregistre_par_user_id' => $user->id,
            'enregistre_le' => Carbon::now()
        ]);

        return redirect()->back()->with('success', 'La présence a été modifiée avec succès.');
    }
}
