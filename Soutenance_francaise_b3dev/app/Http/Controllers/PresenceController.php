<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\SessionDeCours;
use App\Models\StatutPresence;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresenceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:course_sessions,id',
            'presences' => 'required|array',
            'presences.*.etudiant_id' => 'required|exists:etudiants,id',
            'presences.*.statut_id' => 'required|exists:statuts_presence,id'
        ]);

        $session = SessionDeCours::findOrFail($request->session_id);
        $user = auth()->user();

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

        $user = auth()->user();

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
