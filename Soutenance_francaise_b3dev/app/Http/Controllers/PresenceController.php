<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\SessionDeCours;
use App\Models\Etudiant;
use App\Models\StatutPresence;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PresenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Presence::with(['etudiant', 'sessionDeCours.classe', 'sessionDeCours.matiere', 'statutPresence']);

        // Filtres
        if ($request->filled('session_id')) {
            $query->where('course_session_id', $request->session_id);
        }
        if ($request->filled('etudiant_id')) {
            $query->where('etudiant_id', $request->etudiant_id);
        }

        $presences = $query->orderBy('created_at', 'desc')->paginate(20);
        $sessions = SessionDeCours::with(['classe', 'matiere'])->get();
        $etudiants = Etudiant::all();

        return view('presences.index', compact('presences', 'sessions', 'etudiants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $sessions = SessionDeCours::with(['classe', 'matiere'])->get();
        $etudiants = Etudiant::all();
        $statutsPresence = StatutPresence::all();

        return view('presences.create', compact('sessions', 'etudiants', 'statutsPresence'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'course_session_id' => 'required|exists:course_sessions,id',
            'presence_status_id' => 'required|exists:presence_statuses,id',
        ]);

        // Vérifier si une présence existe déjà pour cet étudiant et cette session
        $existingPresence = Presence::where('etudiant_id', $request->etudiant_id)
            ->where('course_session_id', $request->course_session_id)
            ->first();

        if ($existingPresence) {
            return redirect()->back()
                ->with('error', 'Une présence existe déjà pour cet étudiant et cette session.');
        }

        Presence::create([
            'etudiant_id' => $request->etudiant_id,
            'course_session_id' => $request->course_session_id,
            'presence_status_id' => $request->presence_status_id,
            'enregistre_par_user_id' => auth()->id(),
            'enregistre_le' => now(),
        ]);

        return redirect()->route('presences.index')
            ->with('success', 'Présence enregistrée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Presence $presence): View
    {
        $presence->load(['etudiant', 'sessionDeCours.classe', 'sessionDeCours.matiere', 'statutPresence', 'enregistrePar']);
        return view('presences.show', compact('presence'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presence $presence): View
    {
        $statutsPresence = StatutPresence::all();
        return view('presences.edit', compact('presence', 'statutsPresence'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Presence $presence): RedirectResponse
    {
        $request->validate([
            'presence_status_id' => 'required|exists:presence_statuses,id',
        ]);

        $presence->update([
            'presence_status_id' => $request->presence_status_id,
        ]);

        return redirect()->route('presences.index')
            ->with('success', 'Présence mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Presence $presence): RedirectResponse
    {
        $presence->delete();

        return redirect()->route('presences.index')
            ->with('success', 'Présence supprimée avec succès.');
    }

    /**
     * Afficher le formulaire d'appel pour une session spécifique.
     */
    public function appel(SessionDeCours $sessionDeCour): View
    {
        // Récupérer les étudiants de la classe
        $etudiants = $sessionDeCour->classe->etudiants;

        // Récupérer les présences déjà enregistrées pour cette session
        $presences = Presence::where('course_session_id', $sessionDeCour->getKey())
            ->with(['etudiant', 'statutPresence'])
            ->get()
            ->keyBy('etudiant_id');

        $statutsPresence = StatutPresence::all();

        return view('presences.appel', compact('sessionDeCour', 'etudiants', 'presences', 'statutsPresence'));
    }

    /**
     * Enregistrer l'appel pour une session.
     */
    public function storeAppel(Request $request, SessionDeCours $sessionDeCour): RedirectResponse
    {
        $request->validate([
            'presences' => 'required|array',
            'presences.*.etudiant_id' => 'required|exists:etudiants,id',
            'presences.*.presence_status_id' => 'required|exists:presence_statuses,id',
        ]);

        foreach ($request->presences as $presenceData) {
            // Vérifier si une présence existe déjà
            $existingPresence = Presence::where('etudiant_id', $presenceData['etudiant_id'])
                ->where('course_session_id', $sessionDeCour->getKey())
                ->first();

            if ($existingPresence) {
                // Mettre à jour la présence existante
                $existingPresence->update([
                    'presence_status_id' => $presenceData['presence_status_id'],
                ]);
            } else {
                // Créer une nouvelle présence
                Presence::create([
                    'etudiant_id' => $presenceData['etudiant_id'],
                    'course_session_id' => $sessionDeCour->id,
                    'presence_status_id' => $presenceData['presence_status_id'],
                    'enregistre_par_user_id' => auth()->id(),
                    'enregistre_le' => now(),
                ]);
            }
        }

        return redirect()->route('sessions-de-cours.show', $sessionDeCour)
            ->with('success', 'Appel enregistré avec succès.');
    }
}
