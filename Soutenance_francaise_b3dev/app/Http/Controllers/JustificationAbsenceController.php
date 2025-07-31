<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JustificationAbsence;
use App\Models\Presence;
use App\Models\Etudiant;
use App\Models\AnneeAcademique;
use App\Models\Semestre;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Traits\DaisyUINotifier;

class JustificationAbsenceController extends Controller
{
    use DaisyUINotifier;
    /**
     * Affiche la page de justification d'absence
     */
    public function index(Request $request)
    {
        $anneeActive = AnneeAcademique::getActive();
        $semestreActif = $anneeActive ? Semestre::getActiveForYear($anneeActive->id) : null;
        $user = Auth::user();

        // Filtrer les données selon le rôle de l'utilisateur
        if ($user->roles->first()->code === 'coordinateur') {
            $coordinateur = $user->coordinateur;
            if (!$coordinateur || !$coordinateur->promotion) {
                return view('justifications.index', [
                    'presences' => collect(),
                    'etudiants' => collect(),
                    'matieres' => collect(),
                    'enseignants' => collect(),
                    'totalAbsences' => 0,
                    'justifiees' => 0,
                    'nonJustifiees' => 0,
                    'anneeActive' => $anneeActive,
                    'semestreActif' => $semestreActif
                ]);
            }

            // Récupérer les classes de la promotion du coordinateur
            $classes = \App\Models\Classe::where('promotion_id', $coordinateur->promotion_id)->pluck('id');

            // Récupérer les étudiants de ces classes
            $etudiants = Etudiant::whereIn('classe_id', $classes)->orderBy('nom')->get();
            $matieres = \App\Models\Matiere::orderBy('nom')->get();
            $enseignants = \App\Models\Enseignant::orderBy('nom')->get();

            // Construire la requête avec filtres pour les étudiants du coordinateur
            $query = Presence::with(['etudiant', 'sessionDeCours.matiere', 'sessionDeCours.enseignant', 'justification'])
                ->whereHas('statutPresence', function($query) {
                    $query->where('code', 'absent');
                })
                ->whereHas('etudiant', function($query) use ($classes) {
                    $query->whereIn('classe_id', $classes);
                })
                ->whereHas('sessionDeCours', function($query) use ($anneeActive, $semestreActif) {
                    if ($anneeActive) {
                        $query->where('annee_academique_id', $anneeActive->id);
                    }
                    if ($semestreActif) {
                        $query->where('semester_id', $semestreActif->id);
                    }
                });
        } else {
            // Pour les autres rôles (admin, etc.), afficher toutes les données
            $etudiants = Etudiant::orderBy('nom')->get();
            $matieres = \App\Models\Matiere::orderBy('nom')->get();
            $enseignants = \App\Models\Enseignant::orderBy('nom')->get();

            // Construire la requête avec filtres
            $query = Presence::with(['etudiant', 'sessionDeCours.matiere', 'sessionDeCours.enseignant', 'justification'])
                ->whereHas('statutPresence', function($query) {
                    $query->where('code', 'absent');
                })
                ->whereHas('sessionDeCours', function($query) use ($anneeActive, $semestreActif) {
                    if ($anneeActive) {
                        $query->where('annee_academique_id', $anneeActive->id);
                    }
                    if ($semestreActif) {
                        $query->where('semester_id', $semestreActif->id);
                    }
                });
        }

        // Filtre par étudiant
        if ($request->filled('etudiant_id')) {
            $query->where('etudiant_id', $request->etudiant_id);
        }

        // Filtre par matière
        if ($request->filled('matiere_id')) {
            $query->whereHas('sessionDeCours', function($q) use ($request) {
                $q->where('matiere_id', $request->matiere_id);
            });
        }

        // Filtre par enseignant
        if ($request->filled('enseignant_id')) {
            $query->whereHas('sessionDeCours', function($q) use ($request) {
                $q->where('enseignant_id', $request->enseignant_id);
            });
        }

        // Filtre par statut de justification
        if ($request->filled('statut_justification')) {
            if ($request->statut_justification === 'justifiee') {
                $query->whereHas('justification');
            } elseif ($request->statut_justification === 'non_justifiee') {
                $query->whereDoesntHave('justification');
            }
        }

        // Filtre par date
        if ($request->filled('date_debut')) {
            $query->where('enregistre_le', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->where('enregistre_le', '<=', $request->date_fin . ' 23:59:59');
        }

        // Recherche par nom d'étudiant
        if ($request->filled('search')) {
            $query->whereHas('etudiant', function($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('prenom', 'like', '%' . $request->search . '%');
            });
        }

                // Cloner la requête pour les statistiques (pour éviter les conflits)
        $queryForStats = clone $query;

        // Calculer les statistiques
        $totalAbsences = $queryForStats->count();
        $justifiees = $queryForStats->whereHas('justification')->count();
        $nonJustifiees = $queryForStats->whereDoesntHave('justification')->count();



        // Pagination avec 15 éléments par page
        $absences = $query->orderBy('enregistre_le', 'desc')
                         ->paginate(15)
                         ->withQueryString();

        return view('justifications.index', compact('absences', 'anneeActive', 'semestreActif', 'etudiants', 'matieres', 'enseignants', 'totalAbsences', 'justifiees', 'nonJustifiees'));
    }

    /**
     * Affiche le formulaire de justification
     */
    public function create($presenceId)
    {
        $presence = Presence::with(['etudiant', 'sessionDeCours.matiere', 'sessionDeCours.enseignant'])
            ->findOrFail($presenceId);

        return view('justifications.create', compact('presence'));
    }

    /**
     * Enregistre une justification d'absence
     */
    public function store(Request $request, $presenceId)
    {
        // Debug: Log les données reçues
        Log::info('JustificationAbsenceController::store - Données reçues', [
            'presenceId' => $presenceId,
            'request_data' => $request->all(),
            'user_id' => Auth::id()
        ]);

        $request->validate([
            'motif' => 'required|string|max:500',
            'date_justification' => 'required|date',
            'piece_jointe' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $presence = \App\Models\Presence::findOrFail($presenceId);
        Log::info('Presence trouvée', ['presence_id' => $presence->id, 'presence_data' => $presence->toArray()]);

        $session = $presence->sessionDeCours;
        $semestre = $session ? $session->semestre : null;
        $annee = $semestre ? $semestre->anneeAcademique : null;

        Log::info('Vérifications période', [
            'annee' => $annee ? $annee->toArray() : null,
            'semestre' => $semestre ? $semestre->toArray() : null,
            'annee_actif' => $annee ? $annee->actif : null,
            'semestre_actif' => $semestre ? $semestre->actif : null
        ]);

        if (!$annee || !$annee->actif || !$semestre || !$semestre->actif) {
            Log::warning('Période non active - redirection');
            return redirect()->back()->with('error', "Impossible de justifier une absence sur une période non active.");
        }

        // Vérifier que l'absence n'est pas déjà justifiée
        if ($presence->justification) {
            Log::warning('Absence déjà justifiée', ['presence_id' => $presence->id]);
            return back()->withErrors(['message' => 'Cette absence est déjà justifiée']);
        }

        // Vérifier que la date de justification est dans la limite de 2 semaines
        $dateAbsence = Carbon::parse($presence->enregistre_le);
        $dateJustification = Carbon::parse($request->date_justification);

        Log::info('Vérification délai', [
            'date_absence' => $dateAbsence->toDateTimeString(),
            'date_justification' => $dateJustification->toDateTimeString(),
            'diff_days' => $dateJustification->diffInDays($dateAbsence)
        ]);

        // Permettre la justification pour les absences futures ou dans un délai de 2 semaines après
        $now = Carbon::now();
        $isFutureAbsence = $dateAbsence->isAfter($now);
        $isWithinTwoWeeks = $dateAbsence->diffInDays($now) <= 14;

        if (!$isFutureAbsence && !$isWithinTwoWeeks) {
            Log::warning('Délai dépassé');
            return back()->withErrors(['message' => 'La justification doit être faite dans un délai de 2 semaines maximum après l\'absence']);
        }

        $justification = new JustificationAbsence([
            'presence_id' => $presenceId,
            'motif' => $request->motif,
            'date_justification' => $request->date_justification,
            'justifie_par_user_id' => Auth::id(),
        ]);

        Log::info('Justification créée', ['justification_data' => $justification->toArray()]);

        // Gérer le fichier joint si fourni
        if ($request->hasFile('piece_jointe')) {
            $path = $request->file('piece_jointe')->store('justifications', 'public');
            $justification->piece_jointe = $path;
            Log::info('Fichier joint sauvegardé', ['path' => $path]);
        }

        try {
            $justification->save();
            Log::info('Justification sauvegardée avec succès', ['justification_id' => $justification->id]);
            return $this->successNotification('Absence justifiée avec succès !', 'justifications.index');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la sauvegarde de la justification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['message' => 'Erreur lors de la sauvegarde de la justification: ' . $e->getMessage()]);
        }
    }

    /**
     * Affiche les détails d'une justification
     */
    public function show($justificationId)
    {
        $justification = JustificationAbsence::with([
            'presence.etudiant',
            'presence.sessionDeCours.matiere',
            'presence.sessionDeCours.enseignant',
            'justifiePar'
        ])->findOrFail($justificationId);

        return view('justifications.show', compact('justification'));
    }

    /**
     * Affiche le formulaire de modification d'une justification
     */
    public function edit($justificationId)
    {
        $justification = JustificationAbsence::with([
            'presence.etudiant',
            'presence.sessionDeCours.matiere'
        ])->findOrFail($justificationId);

        return view('justifications.edit', compact('justification'));
    }

    /**
     * Met à jour une justification
     */
    public function update(Request $request, $justificationId)
    {
        $request->validate([
            'motif' => 'required|string|max:500',
            'date_justification' => 'required|date',
            'piece_jointe' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $justification = \App\Models\JustificationAbsence::findOrFail($justificationId);
        $presence = $justification->presence;
        $session = $presence ? $presence->sessionDeCours : null;
        $semestre = $session ? $session->semestre : null;
        $annee = $semestre ? $semestre->anneeAcademique : null;
        if (!$annee || !$annee->actif || !$semestre || !$semestre->actif) {
            return redirect()->back()->with('error', "Impossible de modifier une justification sur une période non active.");
        }

        // Vérifier que la modification est dans la limite de 2 semaines
        $dateAbsence = Carbon::parse($justification->presence->enregistre_le);
        $dateJustification = Carbon::parse($request->date_justification);

        if ($dateJustification->diffInDays($dateAbsence) > 14) {
            return back()->withErrors(['message' => 'La modification doit respecter le délai de 2 semaines maximum']);
        }

        $justification->update([
            'motif' => $request->motif,
            'date_justification' => $request->date_justification,
        ]);

        // Gérer le fichier joint si fourni
        if ($request->hasFile('piece_jointe')) {
            // Supprimer l'ancien fichier
            if ($justification->piece_jointe) {
                Storage::disk('public')->delete($justification->piece_jointe);
            }

            $path = $request->file('piece_jointe')->store('justifications', 'public');
            $justification->piece_jointe = $path;
            $justification->save();
        }

        return $this->warningNotification('Justification mise à jour avec succès !', 'justifications.index');
    }

    /**
     * Supprime une justification
     */
    public function destroy($justificationId)
    {
        $justification = \App\Models\JustificationAbsence::findOrFail($justificationId);
        $presence = $justification->presence;
        $session = $presence ? $presence->sessionDeCours : null;
        $semestre = $session ? $session->semestre : null;
        $annee = $semestre ? $semestre->anneeAcademique : null;
        if (!$annee || !$annee->actif || !$semestre || !$semestre->actif) {
            return redirect()->back()->with('error', "Impossible de supprimer une justification sur une période non active.");
        }

        // Supprimer le fichier joint s'il existe
        if ($justification->piece_jointe) {
            Storage::disk('public')->delete($justification->piece_jointe);
        }

        $justification->delete();

        return $this->errorNotification('Justification supprimée avec succès !', 'justifications.index');
    }
}
