<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%$search%")
                  ->orWhere('nom', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhereHas('role', function($q2) use ($search) {
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
        $classes = \App\Models\Classe::all();
        return view('users.create', compact('roles', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'prenom' => 'required',
            'nom' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
            'photo' => 'nullable|image|max:2048',
            'date_naissance' => 'nullable|date',
            'classe_id' => 'required_if:role_id,' . (\App\Models\Role::where('nom', 'like', '%etudiant%')->first()?->id ?? ''),
            'telephone' => 'required_if:role_id,' . (\App\Models\Role::where('nom', 'like', '%parent%')->first()?->id ?? ''),
        ]);

        $user = new User();
        $user->prenom = $request->prenom;
        $user->nom = $request->nom;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role_id = $request->role_id;
        if ($request->hasFile('photo')) {
            $user->photo = $request->file('photo')->store('photos', 'public');
        }
        $user->save();

        // Création de l'entité liée selon le rôle
        $role = $user->role->nom ?? null;
        if ($role === 'Étudiant' || $role === 'Etudiant') {
            \App\Models\Etudiant::create([
                'prenom' => $user->prenom,
                'nom' => $user->nom,
                'classe_id' => $request->classe_id,
                'date_naissance' => $request->date_naissance,
            ]);
        } elseif ($role === 'Enseignant') {
            \App\Models\Enseignant::create([
                'prenom' => $user->prenom,
                'nom' => $user->nom,
            ]);
        } elseif ($role === 'Parent') {
            \App\Models\ParentEtudiant::create([
                'user_id' => $user->id,
                'prenom' => $user->prenom,
                'nom' => $user->nom,
                'telephone' => $request->telephone,
            ]);
        } elseif ($role === 'Coordinateur') {
            \App\Models\Coordinateur::create([
                'user_id' => $user->id,
                'prenom' => $user->prenom,
                'nom' => $user->nom,
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function show(User $user)
    {
        $user->load(['role', 'etudiant.classe', 'parent', 'coordinateur']); // On retire 'enseignant'
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'prenom' => 'required',
            'nom' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user->prenom = $request->prenom;
        $user->nom = $request->nom;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        if ($request->hasFile('photo')) {
            $user->photo = $request->file('photo')->store('photos', 'public');
        }
        $user->save();

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
