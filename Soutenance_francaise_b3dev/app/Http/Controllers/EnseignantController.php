<?php

namespace App\Http\Controllers;

use App\Models\Enseignant;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Matiere;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Etudiant;
use App\Models\Presence;
use App\Models\StatutPresence;
use App\Models\SessionDeCours;
use App\Models\TypeCours;
use App\Models\AnneeAcademique;
use App\Rules\ValidEmailDomain;

class EnseignantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $enseignants = Enseignant::with(['matieres'])
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        return view('enseignants.index', compact('enseignants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $matieres = Matiere::orderBy('nom')->get();
        $users = User::whereHas('roles', function($q) {
            $q->where('nom', 'Enseignant');
        })->orderBy('nom')->get();
        return view('enseignants.create', compact('matieres', 'users'));
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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'matieres' => 'array',
            'matieres.*' => 'exists:matieres,id',
        ]);

        // Créer l'utilisateur
        $user = \App\Models\User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Attacher le rôle enseignant
        $roleEnseignant = \App\Models\Role::where('nom', 'Enseignant')->first();
        if ($roleEnseignant) {
            $user->roles()->attach($roleEnseignant->id);
        }

        $enseignant = Enseignant::create([
            'user_id' => $user->id,
            'nom' => $request->nom,
            'prenom' => $request->prenom,
        ]);

        // Gérer l'upload de la photo
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('enseignants', 'public');
            $enseignant->update(['photo' => $photoPath]);
            // Synchroniser avec l'utilisateur
            $user->update(['photo' => $photoPath]);
        }

        // Affecter les matières sélectionnées à cet enseignant
        if ($request->filled('matieres')) {
            $enseignant->matieres()->attach($request->matieres);
        }

        return redirect()->route('enseignants.index')
            ->with('success', 'Enseignant créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Enseignant $enseignant): View
    {
        $enseignant->load(['sessionsDeCours.matiere', 'sessionsDeCours.classe', 'sessionsDeCours.semestre']);
        return view('enseignants.show', compact('enseignant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Enseignant $enseignant): View
    {
        $matieres = Matiere::orderBy('nom')->get();
        return view('enseignants.edit', compact('enseignant', 'matieres'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Enseignant $enseignant): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => ['required', 'email', 'unique:users,email,' . $enseignant->user_id, new ValidEmailDomain],
            'password' => 'nullable|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'matieres' => 'array',
            'matieres.*' => 'exists:matieres,id',
        ]);

        // Mettre à jour l'utilisateur
        $user = $enseignant->user;
        $user->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
        ]);

        // Mettre à jour le mot de passe si fourni
        if ($request->filled('password')) {
            $user->update(['password' => bcrypt($request->password)]);
        }

        $enseignant->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
        ]);

        // Gérer l'upload de la photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($enseignant->photo) {
                Storage::disk('public')->delete($enseignant->photo);
            }
            $photoPath = $request->file('photo')->store('enseignants', 'public');
            $enseignant->update(['photo' => $photoPath]);
            // Synchroniser avec l'utilisateur
            $user->update(['photo' => $photoPath]);
        }

        // Synchroniser les matières avec cet enseignant
        $enseignant->matieres()->sync($request->matieres ?? []);

        return redirect()->route('enseignants.index')
            ->with('success', 'Enseignant mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enseignant $enseignant): RedirectResponse
    {
        if ($enseignant->sessionsDeCours()->count() > 0) {
            return redirect()->route('enseignants.index')
                ->with('error', 'Impossible de supprimer cet enseignant car il a des sessions de cours programmées.');
        }

        $enseignant->delete();

        return redirect()->route('enseignants.index')
            ->with('success', 'Enseignant supprimé avec succès.');
    }

    /**
     * Obtenir les étudiants d'une classe pour la prise de présence (cours présentiel uniquement)
     */
    public function getEtudiantsClasse(SessionDeCours $session)
    {
        $user = Auth::user();
        $enseignant = $user->enseignant;

        if (!$enseignant) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé.'
            ]);
        }

        // Vérifier que l'enseignant est bien l'enseignant de cette session
        if ($session->enseignant_id !== $enseignant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à prendre la présence pour cette session.'
            ]);
        }

        // Vérifier que le type de cours est Présentiel
        $typePresentiel = TypeCours::where('nom', 'Présentiel')->first();
        if ($session->type_cours_id !== $typePresentiel->id) {
            return response()->json([
                'success' => false,
                'message' => 'La prise de présence n\'est autorisée que pour les cours en présentiel.'
            ]);
        }

        // Récupérer les étudiants de la classe de cette session
        $etudiants = Etudiant::where('classe_id', $session->classe_id)
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
     * Enregistrer les présences pour une session (cours présentiel uniquement)
     */
    public function prisePresence(Request $request, SessionDeCours $session)
    {
        $user = Auth::user();
        $enseignant = $user->enseignant;

        if (!$enseignant) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé.'
            ]);
        }

        // Vérifier que l'enseignant est bien l'enseignant de cette session
        if ($session->enseignant_id !== $enseignant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à prendre la présence pour cette session.'
            ]);
        }

        // Vérifier que le type de cours est Présentiel
        $typePresentiel = TypeCours::where('nom', 'Présentiel')->first();
        if ($session->type_cours_id !== $typePresentiel->id) {
            return response()->json([
                'success' => false,
                'message' => 'La prise de présence n\'est autorisée que pour les cours en présentiel.'
            ]);
        }

        $request->validate([
            'presences' => 'required|array',
            'presences.*.statut_id' => 'required|exists:statuts_presence,id'
        ]);

        foreach ($request->presences as $etudiantId => $presenceData) {
            // Vérifier que l'étudiant appartient bien à la classe de la session
            $etudiant = Etudiant::find($etudiantId);
            if (!$etudiant || $etudiant->classe_id !== $session->classe_id) {
                continue;
            }

            // Mettre à jour ou créer la présence
            Presence::updateOrCreate(
                [
                    'course_session_id' => $session->id,
                    'etudiant_id' => $etudiantId
                ],
                [
                    'statut_presence_id' => $presenceData['statut_id'],
                    'enregistre_le' => now(),
                    'enregistre_par_user_id' => $user->id
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Présences enregistrées avec succès.'
        ]);
    }

    /**
     * Obtenir les sessions en présentiel pour la prise de présence
     */
    public function getSessionsPresentiel(Request $request)
    {
        $user = Auth::user();
        $enseignant = $user->enseignant;

        if (!$enseignant) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé.'
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

        $typePresentiel = TypeCours::where('nom', 'Présentiel')->first();

        $sessions = SessionDeCours::with(['classe', 'matiere', 'enseignant'])
            ->where('enseignant_id', $enseignant->id)
            ->where('annee_academique_id', $anneeActive->id)
            ->where('type_cours_id', $typePresentiel->id)
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'success' => true,
            'sessions' => $sessions
        ]);
    }
}
