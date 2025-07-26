<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\SessionDeCours;
use App\Models\TypeCours;
use App\Models\StatutSession;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmploiDuTempsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $anneeActive = AnneeAcademique::getActive();

        switch ($user->role->code) {
            case 'coordinateur':
                return $this->indexCoordinateur($request, $anneeActive);
            case 'enseignant':
                return $this->indexEnseignant($request, $anneeActive);
            case 'etudiant':
                return $this->indexEtudiant($request, $anneeActive);
            default:
                return redirect()->back()->with('error', 'Accès non autorisé.');
        }
    }

    private function indexCoordinateur(Request $request, AnneeAcademique $anneeAcademique)
    {
        $classes = Classe::where('promotion_id', $request->user()->coordinateur->promotion_id)->get();
        $enseignants = Enseignant::all();
        $typesCours = TypeCours::all();
        $statutsSession = StatutSession::all();

        $sessions = SessionDeCours::with(['classe', 'matiere', 'enseignant', 'typeCours', 'statut'])
            ->whereIn('classe_id', $classes->pluck('id'))
            ->where('annee_academique_id', $anneeAcademique->id)
            ->orderBy('start_time')
            ->get();

        return view('emplois-du-temps.coordinateur', compact(
            'classes',
            'enseignants',
            'typesCours',
            'statutsSession',
            'sessions'
        ));
    }

    private function indexEnseignant(Request $request, AnneeAcademique $anneeAcademique)
    {
        $sessions = SessionDeCours::with(['classe', 'matiere', 'typeCours', 'statut'])
            ->where('enseignant_id', $request->user()->enseignant->id)
            ->where('annee_academique_id', $anneeAcademique->id)
            ->orderBy('start_time')
            ->get();

        return view('emplois-du-temps.enseignant', compact('sessions'));
    }

    private function indexEtudiant(Request $request, AnneeAcademique $anneeAcademique)
    {
        $sessions = SessionDeCours::with(['matiere', 'enseignant', 'typeCours', 'statut'])
            ->where('classe_id', $request->user()->etudiant->classe_id)
            ->where('annee_academique_id', $anneeAcademique->id)
            ->orderBy('start_time')
            ->get();

        return view('emplois-du-temps.etudiant', compact('sessions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'type_cours_id' => 'required|exists:types_cours,id',
            'status_id' => 'required|exists:statuts_session,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $anneeActive = AnneeAcademique::getActive();

        // Vérifier les conflits d'horaire pour la classe
        $conflitClasse = SessionDeCours::where('classe_id', $request->classe_id)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time]);
            })
            ->exists();

        if ($conflitClasse) {
            return redirect()->back()->with('error', 'Il y a un conflit d\'horaire pour cette classe.');
        }

        // Vérifier les conflits d'horaire pour l'enseignant
        $conflitEnseignant = SessionDeCours::where('enseignant_id', $request->enseignant_id)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time]);
            })
            ->exists();

        if ($conflitEnseignant) {
            return redirect()->back()->with('error', 'Il y a un conflit d\'horaire pour cet enseignant.');
        }

        SessionDeCours::create([
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
            'semester_id' => $anneeActive->semestres()->where('actif', true)->first()->id
        ]);

        return redirect()->back()->with('success', 'Session de cours créée avec succès.');
    }
}
