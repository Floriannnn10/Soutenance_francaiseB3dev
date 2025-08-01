<?php

namespace App\Http\Controllers;

use App\Models\ParentEtudiant;
use App\Models\Etudiant;
use App\Models\User;
use App\Models\Role;
use App\Rules\ValidEmailDomain;
use App\Traits\DaisyUINotifier;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ParentEtudiantController extends Controller
{
    use DaisyUINotifier;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $parents = ParentEtudiant::with(['user', 'etudiants'])->orderBy('nom')->orderBy('prenom')->paginate(15);
        return view('parents.index', compact('parents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $etudiants = Etudiant::all();
        return view('parents.create', compact('etudiants'));
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
            'telephone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'profession' => 'nullable|string|max:255',
            'adresse' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'etudiants' => 'nullable|array',
            'etudiants.*' => 'exists:etudiants,id',
        ]);

        // Créer l'utilisateur
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Attacher le rôle parent
        $roleParent = Role::where('nom', 'Parent')->first();
        if ($roleParent) {
            $user->roles()->attach($roleParent->id);
        }

        // Gérer l'upload de la photo
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('parents', 'public');
        }

        // Créer le parent
        $parent = ParentEtudiant::create([
            'user_id' => $user->id,
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'telephone' => $request->telephone,
            'profession' => $request->profession,
            'adresse' => $request->adresse,
            'photo' => $photoPath,
        ]);

        // Synchroniser la photo avec l'utilisateur
        if ($photoPath) {
            $user->update(['photo' => $photoPath]);
        }

        // Attacher les étudiants si fournis
        if ($request->filled('etudiants')) {
            $parent->etudiants()->attach($request->etudiants);
        }

        return $this->successNotification('Parent créé avec succès !', 'parents.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(ParentEtudiant $parent): View
    {
        $parent->load(['user', 'etudiants.classe']);
        return view('parents.show', compact('parent'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ParentEtudiant $parent): View
    {
        $etudiants = Etudiant::all();
        $parent->load('etudiants');
        return view('parents.edit', compact('parent', 'etudiants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ParentEtudiant $parent): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => ['required', 'email', 'unique:users,email,' . ($parent->user_id ?? ''), new ValidEmailDomain],
            'telephone' => 'required|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'profession' => 'nullable|string|max:255',
            'adresse' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'etudiants' => 'nullable|array',
            'etudiants.*' => 'exists:etudiants,id',
        ]);

        // Mettre à jour l'utilisateur
        if ($parent->user) {
            $userData = [
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
            ];

            // Mettre à jour le mot de passe si fourni
            if ($request->filled('password')) {
                $userData['password'] = bcrypt($request->password);
            }

            $parent->user->update($userData);
        }

        // Préparer les données du parent
        $parentData = [
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'telephone' => $request->telephone,
            'profession' => $request->profession,
            'adresse' => $request->adresse,
        ];

        // Gérer l'upload de la photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($parent->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($parent->photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($parent->photo);
            }

            // Sauvegarder la nouvelle photo
            $photoPath = $request->file('photo')->store('parents', 'public');
            $parentData['photo'] = $photoPath;
        }

        // Mettre à jour le parent
        $parent->update($parentData);

        // Synchroniser la photo avec l'utilisateur
        if (isset($parentData['photo']) && $parent->user) {
            $parent->user->update(['photo' => $parentData['photo']]);
        }

        // Mettre à jour les relations avec les étudiants
        if ($request->filled('etudiants')) {
            $parent->etudiants()->sync($request->etudiants);
        } else {
            $parent->etudiants()->detach();
        }

        return $this->warningNotification('Parent mis à jour avec succès !', 'parents.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ParentEtudiant $parent): RedirectResponse
    {
        $parent->delete();

        return $this->errorNotification('Parent supprimé avec succès !', 'parents.index');
    }

        /**
     * Affiche les enfants du parent connecté
     */
    public function mesEnfants(): View
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return view('parents.error', ['message' => 'Utilisateur non connecté']);
            }

            // Essayer de récupérer le parent via la relation
            $parent = $user->parent;

            // Si pas de relation, essayer de le trouver directement
            if (!$parent) {
                $parent = ParentEtudiant::where('user_id', $user->id)->first();

                if (!$parent) {
                    return view('parents.error', [
                        'message' => 'Aucun profil parent trouvé pour cet utilisateur',
                        'user_info' => [
                            'id' => $user->id,
                            'email' => $user->email,
                            'nom' => $user->nom,
                            'prenom' => $user->prenom,
                            'roles' => $user->roles->pluck('code')->toArray()
                        ]
                    ]);
                }
            }

            // Récupérer les enfants avec leurs relations
            $enfants = $parent->etudiants()->with(['classe', 'presences.statutPresence'])->get();

            return view('parents.mes-enfants', compact('enfants', 'parent'));

        } catch (\Exception $e) {
            Log::error('Erreur dans mesEnfants', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('parents.error', [
                'message' => 'Erreur: ' . $e->getMessage(),
                'user_info' => [
                    'id' => Auth::id(),
                    'error' => $e->getMessage()
                ]
            ]);
        }
    }
}
