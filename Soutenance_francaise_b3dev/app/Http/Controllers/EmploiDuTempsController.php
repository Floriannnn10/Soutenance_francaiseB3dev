<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\SessionDeCours;
use App\Models\TypeCours;
use App\Models\StatutSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmploiDuTempsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $anneeActive = AnneeAcademique::getActive();

        // Si aucune année active n'est trouvée, récupérer la première année disponible
        if (!$anneeActive) {
            $anneeActive = AnneeAcademique::orderBy('date_debut', 'desc')->first();

            if (!$anneeActive) {
                return redirect()->back()->with('error', 'Aucune année académique configurée.');
            }
        }

        switch ($user->roles->first()->code) {
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
        $user = Auth::user();
        $coordinateur = $user->coordinateur;

        if (!$coordinateur || !$coordinateur->promotion) {
            return view('emplois-du-temps.coordinateur', [
                'classes' => collect(),
                'enseignants' => collect(),
                'typesCours' => collect(),
                'statutsSession' => collect(),
                'sessions' => collect(),
                'anneeActive' => $anneeAcademique,
                'anneesAcademiques' => AnneeAcademique::orderBy('date_debut', 'desc')->get(),
                'matieres' => collect(),
                'classeFiltree' => null
            ]);
        }

        $classes = Classe::where('promotion_id', $coordinateur->promotion_id)->get();
        $enseignants = Enseignant::all();
        $typesCours = TypeCours::all();
        $statutsSession = StatutSession::all();
        $anneeActive = $anneeAcademique;
        $anneesAcademiques = AnneeAcademique::orderBy('date_debut', 'desc')->get();
        $matieres = \App\Models\Matiere::all();

        // Récupérer les sessions pour les classes de la promotion du coordinateur
        $sessionsQuery = SessionDeCours::with(['classe', 'matiere', 'enseignant', 'typeCours', 'statutSession'])
            ->whereIn('classe_id', $classes->pluck('id'))
            ->where('annee_academique_id', $anneeAcademique->id);

        // Filtrer par classe spécifique si demandé
        $classeFiltree = null;
        if ($request->has('classe_id') && $request->classe_id) {
            $classeFiltree = Classe::find($request->classe_id);
            if ($classeFiltree && $classeFiltree->promotion_id == $coordinateur->promotion_id) {
                $sessionsQuery->where('classe_id', $request->classe_id);
            }
        }

        // Filtres supplémentaires
        if ($request->has('enseignant_id') && $request->enseignant_id) {
            $sessionsQuery->where('enseignant_id', $request->enseignant_id);
        }

        if ($request->has('type_cours_id') && $request->type_cours_id) {
            $sessionsQuery->where('type_cours_id', $request->type_cours_id);
        }

        if ($request->has('statut_id') && $request->statut_id) {
            $sessionsQuery->where('status_id', $request->statut_id);
        }

        if ($request->has('date_debut') && $request->date_debut) {
            $sessionsQuery->where('start_time', '>=', $request->date_debut);
        }

        if ($request->has('date_fin') && $request->date_fin) {
            $sessionsQuery->where('start_time', '<=', $request->date_fin . ' 23:59:59');
        }

        // Pagination
        $sessions = $sessionsQuery->orderBy('start_time')->paginate(15);

        return view('emplois-du-temps.coordinateur', compact(
            'classes',
            'enseignants',
            'typesCours',
            'statutsSession',
            'sessions',
            'anneeActive',
            'anneesAcademiques',
            'matieres',
            'classeFiltree'
        ));
    }

    private function indexEnseignant(Request $request, AnneeAcademique $anneeAcademique)
    {
        $user = Auth::user();
        $enseignant = $user->enseignant;

        if (!$enseignant) {
            return redirect()->back()->with('error', 'Accès non autorisé.');
        }

        // Sélection d'année académique
        $anneeId = $request->input('annee_id');
        $anneesAcademiques = AnneeAcademique::orderBy('date_debut', 'desc')->get();

        if ($anneeId) {
            $anneeActive = AnneeAcademique::find($anneeId);
        } else {
            $anneeActive = $anneeAcademique;
        }

        if (!$anneeActive) {
            $anneeActive = $anneesAcademiques->first();
        }

        // Récupérer les sessions de l'enseignant pour l'année sélectionnée
        $sessions = SessionDeCours::with(['classe', 'matiere', 'typeCours', 'statutSession'])
            ->where('enseignant_id', $enseignant->id)
            ->where('annee_academique_id', $anneeActive->id)
            ->orderBy('start_time')
            ->get();

        return view('emplois-du-temps.enseignant', compact('sessions', 'anneeActive', 'anneesAcademiques'));
    }

    private function indexEtudiant(Request $request, AnneeAcademique $anneeAcademique)
    {
        $sessions = SessionDeCours::with(['matiere', 'enseignant', 'typeCours', 'statutSession'])
            ->where('classe_id', Auth::user()->etudiant->classe_id)
            ->where('annee_academique_id', $anneeAcademique->id)
            ->orderBy('start_time')
            ->get();

        return view('emplois-du-temps.etudiant', compact('sessions'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'classe_id' => 'required|exists:classes,id',
                'matiere_id' => 'required|exists:matieres,id',
                'enseignant_id' => 'required|exists:enseignants,id',
                'type_cours_id' => 'required|exists:types_cours,id',
                'status_id' => 'required|exists:statuts_session,id',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'notes' => 'nullable|string'
            ]);

            $anneeActive = AnneeAcademique::getActive();

            if (!$anneeActive) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune année académique active trouvée.'
                ]);
            }

            // Vérifier les conflits d'horaire pour la classe
            $conflitClasse = SessionDeCours::where('classe_id', $request->classe_id)
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

            $session = SessionDeCours::create([
                'classe_id' => $request->classe_id,
                'matiere_id' => $request->matiere_id,
                'enseignant_id' => $request->enseignant_id,
                'type_cours_id' => $request->type_cours_id,
                'status_id' => $request->status_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'notes' => $request->notes,
                'annee_academique_id' => $anneeActive->id,
                'semester_id' => $anneeActive->semestres()->where('actif', true)->first()->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Session de cours créée avec succès.',
                'session' => $session
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation: ' . implode(', ', $e->errors()),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la session: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $session = SessionDeCours::findOrFail($id);
            $session->delete();

            return response()->json([
                'success' => true,
                'message' => 'Session supprimée avec succès.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Affiche les emplois du temps pour les parents
     */
    public function parent()
    {
        $user = Auth::user();
        $parent = $user->parent;

        if (!$parent) {
            abort(404, 'Parent non trouvé');
        }

        $enfants = $parent->etudiants()->with(['classe'])->get();

        return view('emplois-du-temps.parent', compact('enfants', 'parent'));
    }

    /**
     * Affiche les emplois du temps des enfants du parent connecté
     */
    public function emploisDuTempsEnfants()
    {
        $user = Auth::user();
        $parent = $user->parent;

        if (!$parent) {
            abort(404, 'Parent non trouvé');
        }

        $enfants = $parent->etudiants()->with(['classe'])->get();

        return view('emplois-du-temps.enfants', compact('enfants', 'parent'));
    }
}
