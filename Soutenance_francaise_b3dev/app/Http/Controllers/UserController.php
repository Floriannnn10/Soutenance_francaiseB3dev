<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Etudiant;
use App\Models\Enseignant;
use App\Models\Coordinateur;
use App\Models\ParentEtudiant;
use App\Models\Classe;
use App\Models\Promotion;
use App\Models\Matiere;
use App\Rules\ValidEmailDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhereHas('roles', function($q2) use ($search) {
                      $q2->where('nom', 'like', "%$search%") ;
                  });
            });
        }
        $users = $query->orderBy('nom')->paginate(10)->withQueryString();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $classes = Classe::all();
        // Ne passer que les promotions non assignées
        $promotions = Promotion::whereNotIn('id', \App\Models\Coordinateur::pluck('promotion_id'))->get();
        $matieres = Matiere::all();
        return view('users.create', compact('roles', 'classes', 'promotions', 'matieres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'email' => ['required', 'email', 'unique:users', new ValidEmailDomain],
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
            'photo' => 'nullable|image|max:2048',
            'date_naissance' => 'nullable|date',
            'classe_id' => 'required_if:role_id,' . (Role::where('nom', 'like', '%etudiant%')->first()?->id ?? ''),
            'telephone' => 'required_if:role_id,' . (Role::where('nom', 'like', '%parent%')->first()?->id ?? ''),
            'promotion_id' => 'required_if:role_id,' . (Role::where('nom', 'like', '%coordinateur%')->first()?->id ?? ''),
        ]);

        $user = new User();
        $user->nom = $request->nom;
        $user->prenom = $request->prenom;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        if ($request->hasFile('photo')) {
            $user->photo = $request->file('photo')->store('photos', 'public');
        }

        $user->save();

        // Attacher le rôle à l'utilisateur
        $user->roles()->attach($request->role_id);

        // Création de l'entité liée selon le rôle
        $role =Role::find($request->role_id);
        if ($role && ($role->nom === 'Étudiant' || $role->nom === 'Etudiant')) {
            Etudiant::create([
                'user_id' => $user->id,
                'prenom' => $request->prenom ?? '',
                'nom' => $request->nom ?? '',
                'email' => $request->email,
                'classe_id' => $request->classe_id,
                'date_naissance' => $request->date_naissance,
                'photo' => $user->photo, // Synchroniser la photo
            ]);
        } elseif ($role && $role->nom === 'Enseignant') {
            $enseignant = Enseignant::create([
                'user_id' => $user->id,
                'prenom' => $request->prenom ?? '',
                'nom' => $request->nom ?? '',
                'photo' => $user->photo, // Synchroniser la photo
            ]);
            if ($request->filled('matieres')) {
                $enseignant->matieres()->attach($request->matieres);
            }
        } elseif ($role && $role->nom === 'Parent') {
            ParentEtudiant::create([
                'user_id' => $user->id,
                'prenom' => $request->prenom ?? '',
                'nom' => $request->nom ?? '',
                'telephone' => $request->telephone,
                'photo' => $user->photo, // Synchroniser la photo
            ]);
        } elseif ($role && $role->nom === 'Coordinateur') {
            // Vérifier unicité de la promotion
            if (\App\Models\Coordinateur::where('promotion_id', $request->promotion_id)->exists()) {
                // Supprimer l'utilisateur créé car la validation a échoué
                $user->delete();
                return back()->withInput()->withErrors(['promotion_id' => 'Cette promotion est déjà attribuée à un autre coordinateur.']);
            }

            Coordinateur::create([
                'user_id' => $user->id,
                'prenom' => $request->prenom ?? '',
                'nom' => $request->nom ?? '',
                'email' => $request->email, // Copier l'email
                'promotion_id' => $request->promotion_id,
                'photo' => $user->photo, // Synchroniser la photo
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function show(User $user)
    {
        $user->load(['roles', 'etudiant.classe', 'parent', 'coordinateur']); // On retire 'enseignant'
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $classes = Classe::all();
        // Pour l'édition, inclure la promotion actuelle du coordinateur + les non assignées
        $promotions = Promotion::whereNotIn('id', \App\Models\Coordinateur::where('id', '!=', $user->coordinateur?->id)->pluck('promotion_id'))->get();
        $matieres = Matiere::all();
        return view('users.edit', compact('user', 'roles', 'classes', 'promotions', 'matieres'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'email' => ['required', 'email', 'unique:users,email,' . $user->id, new ValidEmailDomain],
            'role_id' => 'required|exists:roles,id',
            'photo' => 'nullable|image|max:2048',
            'password' => 'nullable|min:6|confirmed',
            'promotion_id' => 'required_if:role_id,' . (Role::where('nom', 'like', '%coordinateur%')->first()?->id ?? ''),
        ]);

        $user->nom = $request->nom;
        $user->prenom = $request->prenom;

        // Vérifier unicité de la promotion pour les coordinateurs
        $role = Role::find($request->role_id);
        if ($role && $role->nom === 'Coordinateur' && $request->filled('promotion_id')) {
            $existingCoordinateur = \App\Models\Coordinateur::where('promotion_id', $request->promotion_id)
                ->where('id', '!=', $user->coordinateur?->id)
                ->first();
            if ($existingCoordinateur) {
                return back()->withInput()->withErrors(['promotion_id' => 'Cette promotion est déjà attribuée à un autre coordinateur.']);
            }
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $request->file('photo')->store('photos', 'public');
        }

        $user->save();

        // Synchroniser la photo avec l'entité associée
        $role = $user->roles->first();
        if ($role) {
            if ($role->nom === 'Étudiant' || $role->nom === 'Etudiant') {
                if ($user->etudiant) {
                    $user->etudiant->update(['photo' => $user->photo]);
                }
            } elseif ($role->nom === 'Enseignant') {
                if ($user->enseignant) {
                    $user->enseignant->update(['photo' => $user->photo]);
                }
            } elseif ($role->nom === 'Parent') {
                if ($user->parent) {
                    $user->parent->update(['photo' => $user->photo]);
                }
            } elseif ($role->nom === 'Coordinateur') {
                if ($user->coordinateur) {
                    $user->coordinateur->update([
                        'photo' => $user->photo,
                        'email' => $user->email // Synchroniser l'email
                    ]);
                }
            }
        }

        // Mettre à jour le rôle de l'utilisateur
        $user->roles()->sync([$request->role_id]);

        return redirect()->route('users.index')->with('success', 'Utilisateur modifié avec succès.');
    }

    public function destroy(User $user)
    {
        // Supprimer l'entité liée selon le rôle
        if ($user->enseignant) {
            $user->enseignant->delete();
        }
        if ($user->etudiant) {
            $user->etudiant->delete();
        }
        if ($user->parent) {
            $user->parent->delete();
        }
        if ($user->coordinateur) {
            $user->coordinateur->delete();
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}
