<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\EtudiantMatiereDropped;
use App\Models\Etudiant;
use App\Models\Matiere;
use App\Models\AnneeAcademique;
use App\Models\Semestre;
use App\Services\DropNotificationService;
use App\Traits\DaisyUINotifier;

class EtudiantMatiereDroppedController extends Controller
{
    use DaisyUINotifier;

    protected $dropNotificationService;

    public function __construct(DropNotificationService $dropNotificationService)
    {
        $this->dropNotificationService = $dropNotificationService;
    }
    /**
     * Afficher la liste des étudiants qui ont abandonné des matières
     */
    public function index(): View
    {
        $user = Auth::user();

        // Construire la requête de base
        $dropsQuery = EtudiantMatiereDropped::with([
            'etudiant.classe',
            'matiere',
            'anneeAcademique',
            'semestre',
            'droppedByUser'
        ]);

        // Filtrer selon le rôle de l'utilisateur
        if ($user->roles->first()->code === 'coordinateur') {
            $coordinateur = $user->coordinateur;
            if ($coordinateur && $coordinateur->promotion) {
                try {
                    // Récupérer les classes de la promotion du coordinateur
                    $classesIds = $coordinateur->promotion->classes()->pluck('id');
                    // Filtrer les abandons pour les étudiants de ces classes
                    $dropsQuery->whereHas('etudiant', function($query) use ($classesIds) {
                        $query->whereIn('classe_id', $classesIds);
                    });
                } catch (\Exception $e) {
                    // En cas d'erreur, ne pas afficher d'abandons
                    $dropsQuery->where('id', 0);
                }
            } else {
                // Si pas de promotion, aucun abandon
                $dropsQuery->where('id', 0); // Condition impossible
            }
        }

        $drops = $dropsQuery->orderBy('date_drop', 'desc')->get();

        // Données pour les filtres (filtrées selon le rôle)
        if ($user->roles->first()->code === 'coordinateur') {
            $coordinateur = $user->coordinateur;
            if ($coordinateur && $coordinateur->promotion) {
                try {
                    $classesIds = $coordinateur->promotion->classes()->pluck('id');
                    $etudiants = Etudiant::whereIn('classe_id', $classesIds)->with('classe')->get();
                } catch (\Exception $e) {
                    $etudiants = collect();
                }
            } else {
                $etudiants = collect();
            }
        } else {
            $etudiants = Etudiant::with('classe')->get();
        }

        $matieres = Matiere::all();

        return view('etudiant-matiere-dropped.index', compact('drops', 'etudiants', 'matieres'));
    }

    /**
     * Afficher le formulaire pour marquer un étudiant comme ayant abandonné une matière
     */
    public function create(): View
    {
        $user = Auth::user();

        // Filtrer les étudiants selon le rôle de l'utilisateur
        if ($user->roles->first()->code === 'coordinateur') {
            $coordinateur = $user->coordinateur;
            if ($coordinateur && $coordinateur->promotion) {
                try {
                    // Récupérer les classes de la promotion du coordinateur
                    $classesIds = $coordinateur->promotion->classes()->pluck('id');
                    // Récupérer seulement les étudiants de ces classes
                    $etudiants = Etudiant::whereIn('classe_id', $classesIds)->with('classe')->get();
                } catch (\Exception $e) {
                    // Si erreur, aucun étudiant
                    $etudiants = collect();
                }
            } else {
                // Si pas de promotion, aucun étudiant
                $etudiants = collect();
            }
        } else {
            // Pour les autres rôles (admin, etc.), tous les étudiants
            $etudiants = Etudiant::with('classe')->get();
        }

        $matieres = Matiere::all();
        $anneesAcademiques = AnneeAcademique::where('actif', 1)->get();
        $semestres = Semestre::where('actif', 1)->get();

        return view('etudiant-matiere-dropped.create', compact('etudiants', 'matieres', 'anneesAcademiques', 'semestres'));
    }

    /**
     * Enregistrer un nouvel abandon de matière
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'matiere_id' => 'required|exists:matieres,id',
            'annee_academique_id' => 'required|exists:annees_academiques,id',
            'semestre_id' => 'required|exists:semestres,id',
            'raison_drop' => 'nullable|string|max:500',
            'date_drop' => 'required|date|before_or_equal:today',
        ]);

        // Vérifier si l'étudiant n'a pas déjà abandonné cette matière pour cette année/semestre
        $existingDrop = EtudiantMatiereDropped::where([
            'etudiant_id' => $request->etudiant_id,
            'matiere_id' => $request->matiere_id,
            'annee_academique_id' => $request->annee_academique_id,
            'semestre_id' => $request->semestre_id,
        ])->first();

        if ($existingDrop) {
            return response()->json([
                'success' => false,
                'message' => 'Cet étudiant a déjà abandonné cette matière pour cette année académique et ce semestre.'
            ], 422);
        }

        $drop = EtudiantMatiereDropped::create([
            'etudiant_id' => $request->etudiant_id,
            'matiere_id' => $request->matiere_id,
            'annee_academique_id' => $request->annee_academique_id,
            'semestre_id' => $request->semestre_id,
            'raison_drop' => $request->raison_drop,
            'date_drop' => $request->date_drop,
            'dropped_by' => Auth::id(),
        ]);

        // Envoyer les notifications de drop
        $this->dropNotificationService->sendDropNotifications($drop);

        return response()->json([
            'success' => true,
            'message' => 'Étudiant marqué comme ayant abandonné la matière avec succès. Les notifications ont été envoyées.',
            'drop' => $drop->load(['etudiant', 'matiere', 'anneeAcademique', 'semestre'])
        ]);
    }

    /**
     * Afficher les détails d'un abandon
     */
    public function show(EtudiantMatiereDropped $etudiant_matiere_dropped): View
    {
        $etudiant_matiere_dropped->load(['etudiant.classe', 'matiere', 'anneeAcademique', 'semestre', 'droppedByUser']);

        return view('etudiant-matiere-dropped.show', ['drop' => $etudiant_matiere_dropped]);
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(EtudiantMatiereDropped $etudiant_matiere_dropped): View
    {
        $user = Auth::user();

        // Filtrer les étudiants selon le rôle de l'utilisateur
        if ($user->roles->first()->code === 'coordinateur') {
            $coordinateur = $user->coordinateur;
            if ($coordinateur && $coordinateur->promotion) {
                try {
                    // Récupérer les classes de la promotion du coordinateur
                    $classesIds = $coordinateur->promotion->classes()->pluck('id');
                    // Récupérer seulement les étudiants de ces classes
                    $etudiants = Etudiant::whereIn('classe_id', $classesIds)->with('classe')->get();
                } catch (\Exception $e) {
                    // Si erreur, aucun étudiant
                    $etudiants = collect();
                }
            } else {
                // Si pas de promotion, aucun étudiant
                $etudiants = collect();
            }
        } else {
            // Pour les autres rôles (admin, etc.), tous les étudiants
            $etudiants = Etudiant::with('classe')->get();
        }

        $matieres = Matiere::all();
        $anneesAcademiques = AnneeAcademique::all();
        $semestres = Semestre::all();

        return view('etudiant-matiere-dropped.edit', [
            'drop' => $etudiant_matiere_dropped,
            'etudiants' => $etudiants,
            'matieres' => $matieres,
            'anneesAcademiques' => $anneesAcademiques,
            'semestres' => $semestres
        ]);
    }

    /**
     * Mettre à jour un abandon
     */
    public function update(Request $request, EtudiantMatiereDropped $etudiant_matiere_dropped): JsonResponse
    {
        $request->validate([
            'raison_drop' => 'nullable|string|max:500',
            'date_drop' => 'required|date|before_or_equal:today',
        ]);

        $etudiant_matiere_dropped->update([
            'raison_drop' => $request->raison_drop,
            'date_drop' => $request->date_drop,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Abandon de matière mis à jour avec succès.',
            'drop' => $etudiant_matiere_dropped->load(['etudiant', 'matiere', 'anneeAcademique', 'semestre'])
        ]);
    }

    /**
     * Supprimer un abandon (rétablir l'étudiant)
     */
    public function destroy(EtudiantMatiereDropped $etudiant_matiere_dropped): JsonResponse
    {
        $etudiant_matiere_dropped->delete();

        return response()->json([
            'success' => true,
            'message' => 'Étudiant rétabli dans la matière avec succès.'
        ]);
    }

    /**
     * Obtenir les statistiques des abandons
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_drops' => EtudiantMatiereDropped::count(),
            'drops_this_year' => EtudiantMatiereDropped::whereHas('anneeAcademique', function($query) {
                $query->where('actif', 1);
            })->count(),
            'top_dropped_matieres' => Matiere::withCount(['etudiantsDropped' => function($query) {
                $query->whereHas('anneeAcademique', function($q) {
                    $q->where('actif', 1);
                });
            }])->orderBy('etudiants_dropped_count', 'desc')->take(5)->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Filtrer les abandons par critères
     */
    public function filter(Request $request): JsonResponse
    {
        $query = EtudiantMatiereDropped::with([
            'etudiant.classe',
            'matiere',
            'anneeAcademique',
            'semestre',
            'droppedByUser'
        ]);

        if ($request->filled('etudiant_id')) {
            $query->where('etudiant_id', $request->etudiant_id);
        }

        if ($request->filled('matiere_id')) {
            $query->where('matiere_id', $request->matiere_id);
        }

        if ($request->filled('annee_academique_id')) {
            $query->where('annee_academique_id', $request->annee_academique_id);
        }

        if ($request->filled('semestre_id')) {
            $query->where('semestre_id', $request->semestre_id);
        }

        if ($request->filled('date_from')) {
            $query->where('date_drop', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date_drop', '<=', $request->date_to);
        }

        $drops = $query->orderBy('date_drop', 'desc')->get();

        return response()->json($drops);
    }
}
