<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use App\Models\Classe;
use App\Models\ParentEtudiant;
use App\Rules\ValidEmailDomain;
use App\Traits\DaisyUINotifier;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class EtudiantController extends Controller
{
    use DaisyUINotifier;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Etudiant::with(['classe', 'presences']);

        if ($request->filled('classe_id')) {
            $query->where('classe_id', $request->classe_id);
        }

        $etudiants = $query->orderBy('nom')->orderBy('prenom')->paginate(15);
        $classes = Classe::all();

        return view('etudiants.index', compact('etudiants', 'classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $classes = Classe::all();
        $parents = \App\Models\ParentEtudiant::with('user')->orderBy('nom')->orderBy('prenom')->get();
        return view('etudiants.create', compact('classes', 'parents'));
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
            'password' => 'required|string|min:6|confirmed',
            'telephone' => 'nullable|string|max:20',
            'date_naissance' => 'nullable|date',
            'adresse' => 'nullable|string',
            'classe_id' => 'required|exists:classes,id',
            'photo' => 'nullable|image|max:2048',
            'parents' => 'nullable|array',
            'parents.*' => 'exists:parents,id',
        ]);

        // Créer l'utilisateur
        $user = \App\Models\User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Attacher le rôle étudiant
        $roleEtudiant = \App\Models\Role::where('nom', 'Étudiant')->first();
        if ($roleEtudiant) {
            $user->roles()->attach($roleEtudiant->id);
        }

        // Gérer l'upload de la photo
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('etudiants', 'public');
        }

        // Créer l'étudiant
        $etudiant = Etudiant::create([
            'user_id' => $user->id,
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'classe_id' => $request->classe_id,
            'date_naissance' => $request->date_naissance,
            'photo' => $photoPath,
        ]);

        // Synchroniser la photo avec l'utilisateur
        if ($photoPath) {
            $user->update(['photo' => $photoPath]);
        }

        // Attribuer les parents sélectionnés
        if ($request->has('parents') && is_array($request->parents)) {
            $etudiant->parents()->attach($request->parents);
        }

        return $this->successNotification('Étudiant créé avec succès !', 'etudiants.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Etudiant $etudiant): View
    {
        $etudiant->load(['classe', 'presences.sessionDeCours.matiere', 'presences.statutPresence']);
        return view('etudiants.show', compact('etudiant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Etudiant $etudiant): View
    {
        $classes = Classe::all();
        $parents = \App\Models\ParentEtudiant::with('user')->orderBy('nom')->orderBy('prenom')->get();
        return view('etudiants.edit', compact('etudiant', 'classes', 'parents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Etudiant $etudiant): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => ['required', 'email', 'unique:users,email,' . ($etudiant->user_id ?? ''), new ValidEmailDomain],
            'password' => 'nullable|string|min:6|confirmed',
            'telephone' => 'nullable|string|max:20',
            'date_naissance' => 'nullable|date',
            'adresse' => 'nullable|string',
            'classe_id' => 'required|exists:classes,id',
            'photo' => 'nullable|image|max:2048',
            'parents' => 'nullable|array',
            'parents.*' => 'exists:parents,id',
        ]);

        // Vérifier si l'étudiant a un utilisateur associé
        if ($etudiant->user_id && $etudiant->user) {
            // Mettre à jour l'utilisateur existant
            $user = $etudiant->user;
            $user->update([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
            ]);

            // Mettre à jour le mot de passe si fourni
            if ($request->filled('password')) {
                $user->update(['password' => bcrypt($request->password)]);
            }
        }

        // Gérer l'upload de la photo
        $photoPath = $etudiant->photo;
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($etudiant->photo && Storage::disk('public')->exists($etudiant->photo)) {
                Storage::disk('public')->delete($etudiant->photo);
            }
            $photoPath = $request->file('photo')->store('etudiants', 'public');
        }

        // Mettre à jour l'étudiant
        $etudiant->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'classe_id' => $request->classe_id,
            'date_naissance' => $request->date_naissance,
            'photo' => $photoPath,
        ]);

        // Mettre à jour le mot de passe dans la table etudiants si fourni
        if ($request->filled('password')) {
            $etudiant->update(['password' => bcrypt($request->password)]);
        }

        // Synchroniser la photo avec l'utilisateur
        if ($etudiant->user) {
            $etudiant->user->update(['photo' => $photoPath]);
        }

        // Mettre à jour les parents
        if ($request->has('parents')) {
            $etudiant->parents()->sync($request->parents);
        } else {
            $etudiant->parents()->detach();
        }

        return $this->warningNotification('Étudiant mis à jour avec succès !', 'etudiants.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Etudiant $etudiant): RedirectResponse
    {
        if ($etudiant->presences()->count() > 0) {
            return redirect()->route('etudiants.index')
                ->with('error', 'Impossible de supprimer cet étudiant car il a des présences enregistrées.');
        }

        $etudiant->delete();

        return $this->errorNotification('Étudiant supprimé avec succès !', 'etudiants.index');
    }

    /**
     * Afficher le formulaire pour attribuer des parents à un étudiant
     */
    public function attribuerParents(Etudiant $etudiant): View
    {
        $parents = ParentEtudiant::with('user')->get();
        $etudiant->load('parents');
        return view('etudiants.attribuer-parents', compact('etudiant', 'parents'));
    }

    /**
     * Attribuer des parents à un étudiant
     */
    public function storeParents(Request $request, Etudiant $etudiant): RedirectResponse
    {
        $request->validate([
            'parents' => 'required|array|min:1',
            'parents.*' => 'exists:parents,id',
        ]);

        $etudiant->parents()->sync($request->parents);

        return $this->successNotification('Parents attribués avec succès !', 'etudiants.show', [$etudiant]);
    }
}
