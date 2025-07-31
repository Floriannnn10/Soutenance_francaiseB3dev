<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SessionDeCours;
use App\Models\Etudiant;
use App\Models\Enseignant;
use App\Models\Classe;
use App\Models\Matiere;
use Carbon\Carbon;

class AgendaController extends Controller
{
    /**
     * Afficher l'agenda de l'étudiant connecté
     */
    public function agendaEtudiant()
    {
        $user = Auth::user();
        $etudiant = Etudiant::where('user_id', $user->id)->first();

        if (!$etudiant) {
            return redirect()->route('dashboard')->with('error', 'Profil étudiant non trouvé.');
        }

        // Récupérer les sessions futures pour l'étudiant
        $sessionsFutures = SessionDeCours::with(['matiere', 'classe', 'enseignant'])
            ->where('classe_id', $etudiant->classe_id)
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->get();

        // Récupérer les sessions récentes (passées)
        $sessionsRecentes = SessionDeCours::with(['matiere', 'classe', 'enseignant'])
            ->where('classe_id', $etudiant->classe_id)
            ->where('start_time', '<=', now())
            ->orderBy('start_time', 'desc')
            ->take(10)
            ->get();

        // Organiser par mois pour le calendrier
        $sessionsParMois = $this->organiserSessionsParMois($sessionsFutures);

        return view('agenda.etudiant', compact('sessionsFutures', 'sessionsRecentes', 'sessionsParMois', 'etudiant'));
    }

    /**
     * Afficher l'agenda de l'enseignant connecté
     */
    public function agendaEnseignant()
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();

        if (!$enseignant) {
            return redirect()->route('dashboard')->with('error', 'Profil enseignant non trouvé.');
        }

        // Récupérer les sessions futures pour l'enseignant
        $sessionsFutures = SessionDeCours::with(['matiere', 'classe'])
            ->where('enseignant_id', $enseignant->id)
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->get();

        // Récupérer les sessions récentes (passées)
        $sessionsRecentes = SessionDeCours::with(['matiere', 'classe'])
            ->where('enseignant_id', $enseignant->id)
            ->where('start_time', '<=', now())
            ->orderBy('start_time', 'desc')
            ->take(10)
            ->get();

        // Organiser par mois pour le calendrier
        $sessionsParMois = $this->organiserSessionsParMois($sessionsFutures);

        return view('agenda.enseignant', compact('sessionsFutures', 'sessionsRecentes', 'sessionsParMois', 'enseignant'));
    }

    /**
     * Organiser les sessions par mois pour l'affichage calendrier
     */
    private function organiserSessionsParMois($sessions)
    {
        $sessionsParMois = [];

        foreach ($sessions as $session) {
            $mois = $session->start_time->format('Y-m');
            $jour = $session->start_time->format('j');

            if (!isset($sessionsParMois[$mois])) {
                $sessionsParMois[$mois] = [
                    'mois' => $session->start_time->format('F Y'),
                    'sessions' => []
                ];
            }

            if (!isset($sessionsParMois[$mois]['sessions'][$jour])) {
                $sessionsParMois[$mois]['sessions'][$jour] = [];
            }

            $sessionsParMois[$mois]['sessions'][$jour][] = $session;
        }

        return $sessionsParMois;
    }

    /**
     * API pour récupérer les sessions d'un mois spécifique
     */
    public function getSessionsMois(Request $request)
    {
        $user = Auth::user();
        $mois = $request->input('mois'); // Format: Y-m
        $type = $request->input('type', 'etudiant'); // 'etudiant' ou 'enseignant'

        if ($type === 'etudiant') {
            $etudiant = Etudiant::where('user_id', $user->id)->first();
            if (!$etudiant) {
                return response()->json(['error' => 'Profil étudiant non trouvé'], 404);
            }

            $sessions = SessionDeCours::with(['matiere', 'classe', 'enseignant'])
                ->where('classe_id', $etudiant->classe_id)
                ->whereYear('start_time', Carbon::parse($mois)->year)
                ->whereMonth('start_time', Carbon::parse($mois)->month)
                ->orderBy('start_time')
                ->get();
        } else {
            $enseignant = Enseignant::where('user_id', $user->id)->first();
            if (!$enseignant) {
                return response()->json(['error' => 'Profil enseignant non trouvé'], 404);
            }

            $sessions = SessionDeCours::with(['matiere', 'classe'])
                ->where('enseignant_id', $enseignant->id)
                ->whereYear('start_time', Carbon::parse($mois)->year)
                ->whereMonth('start_time', Carbon::parse($mois)->month)
                ->orderBy('start_time')
                ->get();
        }

        return response()->json($sessions);
    }
}
